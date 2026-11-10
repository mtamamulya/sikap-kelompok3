<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Akun yang dinonaktifkan (users.status = 0) tidak boleh login, dan sesi yang sudah berjalan ikut diakhiri. */
class LoginAkunNonaktifTest extends TestCase
{
    use RefreshDatabase;

    private function buatAkun(bool $aktif): User
    {
        $idRole = DB::table('roles')->where('nama_role', 'dosen')->value('id_role')
            ?? DB::table('roles')->insertGetId(['nama_role' => 'dosen']);

        $u = new User;
        $u->forceFill([
            'id_role' => $idRole,
            'email' => 'uji-'.uniqid().'@example.test',
            'password' => 'rahasia123',
            'status' => $aktif,
        ])->save();

        return $u;
    }

    public function test_akun_aktif_bisa_login(): void
    {
        $u = $this->buatAkun(true);

        $this->post('/login', ['email' => $u->email, 'password' => 'rahasia123'])->assertRedirect();

        $this->assertAuthenticatedAs($u);
    }

    public function test_akun_nonaktif_ditolak_dengan_pesan_jelas_bila_sandi_benar(): void
    {
        $u = $this->buatAkun(false);

        $this->post('/login', ['email' => $u->email, 'password' => 'rahasia123'])
            ->assertSessionHasErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi admin.']);

        $this->assertGuest();
    }

    public function test_akun_nonaktif_dengan_sandi_salah_tidak_membocorkan_statusnya(): void
    {
        $u = $this->buatAkun(false);

        $this->post('/login', ['email' => $u->email, 'password' => 'salah-total'])
            ->assertSessionHasErrors(['email' => 'Email atau sandi yang Anda masukkan salah.']);

        $this->assertGuest();
    }

    public function test_sesi_akun_yang_baru_dinonaktifkan_langsung_diakhiri(): void
    {
        $u = $this->buatAkun(true);
        $this->actingAs($u);

        $u->forceFill(['status' => false])->save();

        $this->get('/login')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi admin.']);

        $this->assertGuest();
    }
}