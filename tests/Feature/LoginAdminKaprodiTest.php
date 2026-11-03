<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginAdminKaprodiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);   // 4 role + akun admin & kaprodi (sandi: password)
    }

    public function test_admin_bisa_login_dan_diarahkan_ke_dashboard_admin(): void
    {
        $this->post(route('login.attempt'), ['email' => 'admin@trk.ac.id', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_kaprodi_bisa_login_dan_diarahkan_ke_dashboard_kaprodi(): void
    {
        $this->post(route('login.attempt'), ['email' => 'kaprodi@trk.ac.id', 'password' => 'password'])
            ->assertRedirect(route('kaprodi.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_sandi_salah_ditolak(): void
    {
        $this->post(route('login.attempt'), ['email' => 'admin@trk.ac.id', 'password' => 'salah'])
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_akun_nonaktif_ditolak(): void
    {
        User::where('email', 'admin@trk.ac.id')->update(['status' => false]);

        $this->post(route('login.attempt'), ['email' => 'admin@trk.ac.id', 'password' => 'password'])
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_role_dosen_tidak_bisa_masuk_lewat_form_ini(): void
    {
        User::factory()->create(['email' => 'dosen@trk.ac.id', 'id_role' => 3]);

        $this->post(route('login.attempt'), ['email' => 'dosen@trk.ac.id', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_tamu_yang_membuka_halaman_admin_dialihkan_ke_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('kaprodi.dashboard'))->assertRedirect(route('login'));
    }

    public function test_kaprodi_tidak_boleh_membuka_halaman_admin(): void
    {
        $kaprodi = User::where('email', 'kaprodi@trk.ac.id')->firstOrFail();

        $this->actingAs($kaprodi)->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_tidak_boleh_membuka_halaman_kaprodi(): void
    {
        $admin = User::where('email', 'admin@trk.ac.id')->firstOrFail();

        $this->actingAs($admin)->get(route('kaprodi.dashboard'))->assertRedirect(route('login'));
    }

    public function test_logout_mengakhiri_sesi(): void
    {
        $admin = User::where('email', 'admin@trk.ac.id')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.logout'))->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
