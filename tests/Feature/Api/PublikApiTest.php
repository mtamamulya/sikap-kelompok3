<?php

namespace Tests\Feature\Api;

use App\Repositories\AkreditasiDbStore;
use App\Repositories\KurikulumDbStore;
use App\Repositories\MataKuliahDbStore;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** API baca publik: kurikulum, mata kuliah, akreditasi. */
class PublikApiTest extends TestCase
{
    use RefreshDatabase;

    private function isiData(): array
    {
        $this->seed(DatabaseSeeder::class);
        $k = app(KurikulumDbStore::class)->create(['name' => 'Kurikulum 2024']);
        app(MataKuliahDbStore::class)->create([
            'kurikulum' => $k['id'], 'semester' => 2, 'kode' => 'TRK201', 'nama' => 'Basis Data',
            'sks_teori' => 2, 'sks_praktek' => 1, 'jam_teori' => 2, 'jam_praktek' => 2,
        ]);
        app(AkreditasiDbStore::class)->create(['lembaga' => 'BAN-PT', 'year' => 2024], true);

        return $k;
    }

    public function test_daftar_kurikulum_berformat_standar_dengan_paginasi(): void
    {
        $this->isiData();

        $this->getJson('/api/v1/kurikulum')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['id_kurikulum', 'nama_kurikulum', 'status', 'link']], 'meta' => ['current_page', 'per_page', 'total', 'last_page']])
            ->assertJsonPath('data.0.nama_kurikulum', 'Kurikulum 2024');
    }

    public function test_detail_dan_mata_kuliah_per_kurikulum(): void
    {
        $k = $this->isiData();

        $this->getJson('/api/v1/kurikulum/'.$k['id'])->assertOk()->assertJsonPath('data.status', true);
        $this->getJson('/api/v1/kurikulum/'.$k['id'].'/mata-kuliah')
            ->assertOk()
            ->assertJsonPath('data.0.kode_mk', 'TRK201');
        $this->getJson('/api/v1/kurikulum/'.$k['id'].'/mata-kuliah?semester=1')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_id_tidak_ada_mengembalikan_404_berformat_gagal(): void
    {
        $this->getJson('/api/v1/kurikulum/999999')->assertStatus(404)->assertJsonPath('success', false);
        $this->getJson('/api/v1/mata-kuliah/999999')->assertStatus(404)->assertJsonPath('success', false);
        $this->getJson('/api/v1/akreditasi/999999')->assertStatus(404)->assertJsonPath('success', false);
    }

    public function test_filter_semester_tidak_valid_422(): void
    {
        $this->getJson('/api/v1/mata-kuliah?semester=99')->assertStatus(422);
    }

    public function test_akreditasi_aktif(): void
    {
        $this->getJson('/api/v1/akreditasi/aktif')->assertStatus(404);

        $this->isiData();

        $this->getJson('/api/v1/akreditasi/aktif')
            ->assertOk()
            ->assertJsonPath('data.lembaga', 'BAN-PT')
            ->assertJsonPath('data.status', true);
    }
}
