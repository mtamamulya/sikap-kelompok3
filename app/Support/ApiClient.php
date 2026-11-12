<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

/**
 * Jalur tunggal web memanggil API lewat HTTP sungguhan.
 *
 * Contoh di controller:
 *   $hasil = ApiClient::pengguna()->get('/kurikulum', ['per_page' => 50]);
 *   $rows = $hasil['data'];
 *
 * Setiap metode mengembalikan envelope API {success, message, data[, meta]}.
 * Bila API menjawab gagal, dilempar ApiException (401 -> login, 422 -> form).
 */
class ApiClient
{
    private function __construct(private readonly ?string $token) {}

    /** Atas nama pengguna yang sedang login (token dari sesi). Tanpa token sesi, permintaan dikirim tanpa token. */
    public static function pengguna(): self
    {
        $token = session('api_token');

        return new self(is_string($token) && $token !== '' ? $token : null);
    }

    /** Atas nama layanan (API_SERVICE_TOKEN), tanpa konteks pengguna. */
    public static function layanan(): self
    {
        $token = config('api_client.service_token');

        return new self(is_string($token) && $token !== '' ? $token : null);
    }

    /** Tanpa token, untuk endpoint publik. */
    public static function publik(): self
    {
        return new self(null);
    }

    /** @param  array<string, mixed>  $query */
    public function get(string $path, array $query = []): array
    {
        return $this->kirim('get', $path, $query);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile>  $berkas  nama field => file unggahan
     */
    public function post(string $path, array $data = [], array $berkas = []): array
    {
        return $this->kirim('post', $path, $data, $berkas);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile>  $berkas
     */
    public function put(string $path, array $data = [], array $berkas = []): array
    {
        return $this->kirim('put', $path, $data, $berkas);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile>  $berkas
     */
    public function patch(string $path, array $data = [], array $berkas = []): array
    {
        return $this->kirim('patch', $path, $data, $berkas);
    }

    public function delete(string $path): array
    {
        return $this->kirim('delete', $path);
    }

    private function kirim(string $metode, string $path, array $data = [], array $berkas = []): array
    {
        $permintaan = Http::baseUrl($this->urlDasar())
            ->acceptJson()
            ->timeout((int) config('api_client.timeout', 10));

        if ($this->token !== null) {
            $permintaan = $permintaan->withToken($this->token);
        }

        if ($berkas !== []) {
            // PHP tidak membaca multipart pada PUT/PATCH, jadi kirim POST + _method.
            if (in_array($metode, ['put', 'patch'], true)) {
                $data['_method'] = strtoupper($metode);
                $metode = 'post';
            }

            foreach ($berkas as $nama => $file) {
                if ($file instanceof UploadedFile) {
                    $permintaan = $permintaan->attach($nama, fopen($file->getRealPath(), 'r'), $file->getClientOriginalName());
                }
            }
        }

        try {
            $respons = $permintaan->{$metode}('/'.ltrim($path, '/'), $data);
        } catch (ConnectionException $e) {
            throw new ApiException(503, 'Layanan API tidak dapat dihubungi.', [], $e);
        }

        return $this->urai($respons);
    }

    private function urai(Response $respons): array
    {
        $badan = $respons->json();

        if ($respons->successful()) {
            return is_array($badan) ? $badan : ['success' => true, 'message' => '', 'data' => null];
        }

        $pesan = is_array($badan) && is_string($badan['message'] ?? null) ? $badan['message'] : 'Permintaan ke API gagal.';
        $galat = is_array($badan) && is_array($badan['errors'] ?? null) ? $badan['errors'] : [];

        throw new ApiException($respons->status(), $pesan, $galat);
    }

    private function urlDasar(): string
    {
        return rtrim((string) (config('api_client.base_url') ?: url('/api/v1')), '/');
    }
}
