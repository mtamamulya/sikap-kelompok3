<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\ApiTokenSesi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class ApiTokenSesiTest extends TestCase
{
    use RefreshDatabase;

    private function buatKaprodi(): User
    {
        DB::table('roles')->insertOrIgnore(['id_role' => 2, 'nama_role' => 'kaprodi']);

        return User::firstOrCreate(
            ['email' => 'kaprodi@trk.ac.id'],
            ['id_role' => 2, 'password' => 'password', 'status' => true]
        );
    }

    public function test_bawaannya_mati_dan_tidak_membuat_token(): void
    {
        $user = $this->buatKaprodi();

        ApiTokenSesi::terbitkan($user);

        $this->assertNull(session('api_token'));
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_terbitkan_menyimpan_token_di_sesi_dan_cabut_menghapusnya(): void
    {
        config(['api_client.token_sesi' => true]);
        $user = $this->buatKaprodi();

        ApiTokenSesi::terbitkan($user);

        $teks = session('api_token');
        $this->assertNotEmpty($teks);
        $this->assertNotNull(PersonalAccessToken::findToken($teks));

        ApiTokenSesi::cabut();

        $this->assertNull(session('api_token'));
        $this->assertNull(PersonalAccessToken::findToken($teks));
    }

    public function test_kegagalan_membuat_token_tidak_menggagalkan_login(): void
    {
        config(['api_client.token_sesi' => true]);
        $user = $this->buatKaprodi();
        Schema::drop('personal_access_tokens');

        ApiTokenSesi::terbitkan($user);

        $this->assertNull(session('api_token'));
    }

    public function test_perintah_token_layanan_membuat_token_dengan_kemampuan_terbatas(): void
    {
        $this->buatKaprodi();

        $kode = Artisan::call('api:token-layanan', [
            'email' => 'kaprodi@trk.ac.id',
            'nama' => 'kelompok1-landing',
            '--hari' => 5,
            '--abilities' => ['baca'],
        ]);

        $this->assertSame(0, $kode);
        $token = PersonalAccessToken::where('name', 'kelompok1-landing')->first();
        $this->assertNotNull($token);
        $this->assertSame(['baca'], $token->abilities);
        $this->assertNotNull($token->expires_at);
    }

    public function test_perintah_token_layanan_menolak_email_tidak_dikenal(): void
    {
        $kode = Artisan::call('api:token-layanan', ['email' => 'tidak-ada@trk.ac.id', 'nama' => 'x']);

        $this->assertSame(1, $kode);
        $this->assertSame(0, PersonalAccessToken::count());
    }
}
