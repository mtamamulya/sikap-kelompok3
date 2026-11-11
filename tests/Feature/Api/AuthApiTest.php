<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Memakai akun dari DatabaseSeeder: admin@trk.ac.id / password. */
class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private const EMAIL = 'admin@trk.ac.id';

    private const SANDI = 'password';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_berhasil_mengembalikan_token_dan_user(): void
    {
        $res = $this->postJson('/api/v1/login', ['email' => self::EMAIL, 'password' => self::SANDI]);

        $res->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.email', self::EMAIL)
            ->assertJsonStructure(['data' => ['token', 'expires_at', 'user' => ['id_user', 'email', 'id_role', 'role', 'status']]])
            ->assertJsonMissingPath('data.user.password');
    }

    public function test_token_bisa_dipakai_untuk_me(): void
    {
        $token = $this->postJson('/api/v1/login', ['email' => self::EMAIL, 'password' => self::SANDI])
            ->json('data.token');

        $this->withToken($token)->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', self::EMAIL)
            ->assertJsonMissingPath('data.password');
    }

    public function test_sandi_salah_ditolak(): void
    {
        $this->postJson('/api/v1/login', ['email' => self::EMAIL, 'password' => 'salah'])
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_email_tidak_terdaftar_ditolak(): void
    {
        $this->postJson('/api/v1/login', ['email' => 'tidakada@trk.ac.id', 'password' => 'apa-saja'])
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_akun_nonaktif_ditolak(): void
    {
        User::where('email', self::EMAIL)->update(['status' => false]);

        $this->postJson('/api/v1/login', ['email' => self::EMAIL, 'password' => self::SANDI])
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_validasi_input_kosong(): void
    {
        $this->postJson('/api/v1/login', [])->assertStatus(422)->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_me_tanpa_token_ditolak(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_logout_menghapus_token(): void
    {
        $token = $this->postJson('/api/v1/login', ['email' => self::EMAIL, 'password' => self::SANDI])
            ->json('data.token');
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withToken($token)->postJson('/api/v1/logout')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
