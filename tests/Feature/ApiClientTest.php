<?php

namespace Tests\Feature;

use App\Support\ApiClient;
use App\Support\ApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'api_client.base_url' => 'http://api.test/api/v1/',
            'api_client.service_token' => null,
        ]);
    }

    private function jawabanOk(mixed $data = []): array
    {
        return ['success' => true, 'message' => 'ok', 'data' => $data];
    }

    public function test_pengguna_mengirim_token_sesi_dan_mengurai_data(): void
    {
        Http::fake(['*' => Http::response($this->jawabanOk([['id' => 1]]))]);
        $this->withSession(['api_token' => 'token-budi']);

        $hasil = ApiClient::pengguna()->get('/kurikulum', ['per_page' => 5]);

        $this->assertSame([['id' => 1]], $hasil['data']);
        Http::assertSent(fn (Request $r) => $r->hasHeader('Authorization', 'Bearer token-budi')
            && str_starts_with($r->url(), 'http://api.test/api/v1/kurikulum')
            && str_contains($r->url(), 'per_page=5'));
    }

    public function test_pengguna_tanpa_token_sesi_tidak_memakai_token_layanan(): void
    {
        config(['api_client.service_token' => 'token-layanan']);
        Http::fake(['*' => Http::response($this->jawabanOk())]);

        ApiClient::pengguna()->get('/kurikulum');

        Http::assertSent(fn (Request $r) => ! $r->hasHeader('Authorization'));
    }

    public function test_layanan_memakai_token_layanan(): void
    {
        config(['api_client.service_token' => 'token-layanan']);
        Http::fake(['*' => Http::response($this->jawabanOk())]);

        ApiClient::layanan()->get('/akreditasi/aktif');

        Http::assertSent(fn (Request $r) => $r->hasHeader('Authorization', 'Bearer token-layanan'));
    }

    public function test_publik_tidak_mengirim_token(): void
    {
        config(['api_client.service_token' => 'token-layanan']);
        $this->withSession(['api_token' => 'token-budi']);
        Http::fake(['*' => Http::response($this->jawabanOk())]);

        ApiClient::publik()->get('/kurikulum');

        Http::assertSent(fn (Request $r) => ! $r->hasHeader('Authorization'));
    }

    public function test_base_url_kosong_memakai_server_yang_sama(): void
    {
        config(['api_client.base_url' => null]);
        Http::fake(['*' => Http::response($this->jawabanOk())]);

        ApiClient::publik()->get('/ping');

        Http::assertSent(fn (Request $r) => str_contains($r->url(), '/api/v1/ping'));
    }

    public function test_put_dengan_berkas_dikirim_sebagai_post_dengan_method_override(): void
    {
        Http::fake(['*' => Http::response($this->jawabanOk())]);
        $pdf = UploadedFile::fake()->create('kurikulum.pdf', 10, 'application/pdf');

        ApiClient::publik()->put('/kurikulum/3', ['nama_kurikulum' => 'KBK 2026'], ['pdf' => $pdf]);

        Http::assertSent(fn (Request $r) => $r->method() === 'POST'
            && str_contains($r->body(), 'name="_method"')
            && str_contains($r->body(), 'PUT')
            && str_contains($r->body(), 'filename="kurikulum.pdf"'));
    }

    public function test_respons_422_menjadi_api_exception_dengan_galat(): void
    {
        Http::fake(['*' => Http::response([
            'success' => false,
            'message' => 'Data tidak valid.',
            'errors' => ['nama_kurikulum' => ['Wajib diisi.']],
        ], 422)]);

        try {
            ApiClient::publik()->post('/kurikulum', []);
            $this->fail('ApiException seharusnya dilempar.');
        } catch (ApiException $e) {
            $this->assertSame(422, $e->getStatusCode());
            $this->assertSame('Data tidak valid.', $e->getMessage());
            $this->assertSame(['nama_kurikulum' => ['Wajib diisi.']], $e->errors);
        }
    }

    public function test_koneksi_gagal_menjadi_api_exception_503(): void
    {
        Http::fake(function () {
            throw new ConnectionException('tidak tersambung');
        });

        try {
            ApiClient::publik()->get('/kurikulum');
            $this->fail('ApiException seharusnya dilempar.');
        } catch (ApiException $e) {
            $this->assertSame(503, $e->getStatusCode());
        }
    }

    public function test_422_dari_api_mengembalikan_user_ke_form_dengan_galat(): void
    {
        Route::middleware('web')->get('/_uji-api', fn () => ApiClient::publik()->get('/kurikulum'));
        Http::fake(['*' => Http::response([
            'success' => false,
            'message' => 'Data tidak valid.',
            'errors' => ['nama' => ['Wajib diisi.']],
        ], 422)]);

        $this->from('/form')->get('/_uji-api')
            ->assertRedirect('/form')
            ->assertSessionHasErrors('nama');
    }

    public function test_401_dari_api_mengakhiri_token_sesi_dan_mengarahkan_ke_login(): void
    {
        Route::middleware('web')->get('/_uji-api', fn () => ApiClient::pengguna()->get('/pengajaran'));
        Http::fake(['*' => Http::response(['success' => false, 'message' => 'Belum login atau token tidak valid.'], 401)]);

        $this->withSession(['api_token' => 'token-kedaluwarsa'])
            ->get('/_uji-api')
            ->assertRedirect(route('login'))
            ->assertSessionMissing('api_token');
    }

    public function test_404_dari_api_menjadi_halaman_404(): void
    {
        Route::middleware('web')->get('/_uji-api', fn () => ApiClient::publik()->get('/kurikulum/999'));
        Http::fake(['*' => Http::response(['success' => false, 'message' => 'Tidak ditemukan.'], 404)]);

        $this->get('/_uji-api')->assertNotFound();
    }
}
