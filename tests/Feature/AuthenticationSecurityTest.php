<?php

namespace Tests\Feature;

use App\Mail\PasswordResetOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function user(int $roleId, string $email = 'student@test.local', bool $active = true): User
    {
        DB::table('roles')->insert([
            'id_role' => $roleId,
            'nama_role' => match ($roleId) {
                1 => 'admin',
                2 => 'kaprodi',
                3 => 'dosen',
                default => 'mahasiswa',
            },
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::create([
            'id_role' => $roleId,
            'email' => $email,
            'password' => 'password',
            'status' => $active,
        ]);
    }

    public function test_login_success_and_failure_are_audited_without_storing_passwords(): void
    {
        $user = $this->user(4);

        $this->post(route('login.attempt'), ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->post(route('login.attempt'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('mahasiswa.dashboard'));

        $this->assertDatabaseCount('login_logs', 2);
        $this->assertDatabaseHas('login_logs', [
            'id_user' => $user->getKey(),
            'email' => $user->email,
            'successful' => false,
        ]);
        $this->assertDatabaseHas('login_logs', [
            'id_user' => $user->getKey(),
            'email' => $user->email,
            'successful' => true,
        ]);
        $this->assertDatabaseMissing('login_logs', ['email' => 'password']);
    }

    public function test_login_uses_the_account_role_without_a_role_selector(): void
    {
        $this->withoutVite();
        $this->user(4);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('name="role"', false)
            ->assertDontSee('Masuk sebagai');

        $this->post(route('login.attempt'), [
            'email' => 'student@test.local',
            'password' => 'password',
        ])->assertRedirect(route('mahasiswa.dashboard'));

        $lecturer = $this->user(3, 'lecturer@test.local');
        auth()->logout();
        $this->post(route('login.attempt'), [
            'email' => $lecturer->email,
            'password' => 'password',
        ])->assertRedirect(route('dosen.dashboard'));
    }

    public function test_login_is_limited_after_five_failed_attempts(): void
    {
        $user = $this->user(4);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login.attempt'), ['email' => $user->email, 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }

        $this->post(route('login.attempt'), ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('login_logs', 5);
    }

    public function test_student_routes_require_an_authenticated_student_role(): void
    {
        $this->get(route('mahasiswa.dashboard'))->assertRedirect(route('login'));

        $lecturer = $this->user(3, 'lecturer@test.local');
        $this->actingAs($lecturer)->get(route('mahasiswa.dashboard'))->assertForbidden();

        $student = $this->user(4, 'another-student@test.local');
        $this->actingAs($student)->get(route('mahasiswa.dashboard'))->assertOk();
    }

    public function test_admin_and_kaprodi_routes_require_their_respective_roles(): void
    {
        $this->get(route('admin.pengajuan'))->assertRedirect(route('login'));
        $this->get(route('kaprodi.pengajuan'))->assertRedirect(route('login'));

        $admin = $this->user(1, 'admin@test.local');
        $this->actingAs($admin)->get(route('admin.pengajuan'))->assertOk();
        $this->get(route('kaprodi.pengajuan'))->assertForbidden();

        $kaprodi = $this->user(2, 'kaprodi@test.local');
        $this->actingAs($kaprodi)->get(route('kaprodi.pengajuan'))->assertOk();
        $this->get(route('admin.pengajuan'))->assertForbidden();
    }

    public function test_otp_is_mailed_as_plain_code_but_only_its_hash_is_stored(): void
    {
        Mail::fake();
        $user = $this->user(4);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('pemulihan.step', 2);

        Mail::assertSent(PasswordResetOtp::class, fn (PasswordResetOtp $mail) => $mail->hasTo($user->email));
        $mail = Mail::sent(PasswordResetOtp::class)->first();
        $otp = DB::table('otp_codes')->first();

        $this->assertNotNull($mail);
        $this->assertNotNull($otp);
        $this->assertNotSame($mail->code, $otp->code_hash);
        $this->assertTrue(Hash::check($mail->code, $otp->code_hash));
        $this->assertSame(0, (int) $otp->attempts);
    }

    public function test_otp_resend_is_limited_to_once_per_minute(): void
    {
        Mail::fake();
        $user = $this->user(4);

        $this->post(route('password.email'), ['email' => $user->email]);
        $this->post(route('password.kirim-ulang'));

        Mail::assertSent(PasswordResetOtp::class, 1);
        $this->assertDatabaseCount('otp_codes', 1);
    }

    public function test_otp_expires_after_five_minutes_and_allows_only_three_attempts(): void
    {
        Mail::fake();
        $user = $this->user(4);
        $this->post(route('password.email'), ['email' => $user->email]);

        $code = Mail::sent(PasswordResetOtp::class)->first()->code;
        $wrongCode = $code === '000000' ? '000001' : '000000';
        foreach (range(1, 3) as $attempt) {
            $this->post(route('password.otp'), ['otp' => $wrongCode])
                ->assertSessionHasErrors('otp');
        }

        $otp = DB::table('otp_codes')->first();
        $this->assertSame(3, (int) $otp->attempts);
        $this->assertNotNull($otp->consumed_at);

        $this->travel(6)->minutes();
        $this->post(route('password.email'), ['email' => $user->email]);
        $this->travel(6)->minutes();
        $this->post(route('password.otp'), ['otp' => Mail::sent(PasswordResetOtp::class)->last()->code])
            ->assertSessionHasErrors('otp');
    }

    public function test_verified_otp_can_reset_password_once(): void
    {
        Mail::fake();
        $user = $this->user(4);
        $this->post(route('password.email'), ['email' => $user->email]);
        $code = Mail::sent(PasswordResetOtp::class)->first()->code;

        $this->post(route('password.otp'), ['otp' => $code])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('pemulihan.step', 3);

        $this->post(route('password.simpan'), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('password.request'));

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
        $this->assertNotNull(DB::table('otp_codes')->value('consumed_at'));
    }

    public function test_unknown_email_receives_the_same_non_disclosing_response(): void
    {
        Mail::fake();

        $this->post(route('password.email'), ['email' => 'unknown@test.local'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('pemulihan.step', 2)
            ->assertSessionHas('status');

        Mail::assertNothingSent();
        $this->assertDatabaseCount('otp_codes', 0);
    }
}
