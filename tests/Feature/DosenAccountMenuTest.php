<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DosenAccountMenuTest extends TestCase
{
    use RefreshDatabase;

    private function lecturer(): User
    {
        $role = DB::table('roles')->where('nama_role', 'dosen')->value('id_role')
            ?? DB::table('roles')->insertGetId(['nama_role' => 'dosen'], 'id_role');

        return User::create([
            'id_role' => $role,
            'email' => 'yanto@example.test',
            'password' => 'password',
            'status' => true, // <-- Tambahkan ini agar akun dianggap aktif oleh middleware
        ]);
    }

    public function test_header_and_dropdown_use_the_logged_in_lecturer_name(): void
    {
        $this->withoutVite();
        $user = $this->lecturer();
        Dosen::create(['id_user' => $user->getKey(), 'nip' => '123456789', 'nama' => 'Yanto']);

        $response = $this->actingAs($user)->get('/dosen/kegiatan/create');
        $response->assertOk()->assertSee('Yanto')->assertDontSee('Pratinjau dosen')
            ->assertDontSee('Halaman utama')->assertSee('Keluar')
            ->assertSee('Profil Saya')
            ->assertSee('action="'.route('logout').'"', false);
        $this->assertSame(2, substr_count($response->getContent(), '>Yanto<'));
    }

    public function test_an_account_without_a_lecturer_profile_uses_its_email(): void
    {
        $this->withoutVite();
        $this->actingAs($this->lecturer())->get('/dosen/kegiatan/create')
            ->assertOk()->assertSee('yanto@example.test')->assertDontSee('Pratinjau dosen');
    }

    public function test_logout_ends_authentication_and_clears_legacy_role_sessions(): void
    {
        $this->actingAs($this->lecturer())
            ->withSession(['admin' => ['nama' => 'Admin'], 'kaprodi' => ['nama' => 'Kaprodi']])
            ->post(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionMissing('admin')->assertSessionMissing('kaprodi');
        $this->assertGuest();
    }

    public function test_guests_are_offered_login_instead_of_logout(): void
    {
        $this->withoutVite();
        $this->get('/dosen/kegiatan/create')->assertOk()->assertSee('Pratinjau dosen')
            ->assertDontSee('Halaman utama')->assertDontSee('action="'.route('logout').'"', false);
        $this->post(route('logout'))->assertRedirect(route('login'));
    }
}
