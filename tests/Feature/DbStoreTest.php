<?php

namespace Tests\Feature;

use App\Models\Prodi;
use App\Repositories\AkreditasiDbStore;
use App\Repositories\KurikulumDbStore;
use App\Repositories\MataKuliahDbStore;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Menguji versi database dari store (Kurikulum, Mata Kuliah, Akreditasi). Memakai database tes, bukan database bersama. */
class DbStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_kurikulum_membuat_prodi_trk_otomatis_bila_kosong(): void
    {
        $this->assertSame(0, Prodi::count());

        $row = app(KurikulumDbStore::class)->create(['name' => 'Kurikulum 2024', 'year' => '2024', 'description' => 'Uji']);

        $this->assertSame(1, Prodi::count());
        $this->assertSame('Kurikulum 2024', $row['name']);
        $this->assertSame('aktif', $row['status']);
    }

    public function test_kurikulum_ubah_status_dan_hapus(): void
    {
        $store = app(KurikulumDbStore::class);
        $row = $store->create(['name' => 'K1']);

        $this->assertSame('nonaktif', $store->toggleStatus($row['id'])['status']);
        $this->assertSame('aktif', $store->toggleStatus($row['id'])['status']);
        $this->assertTrue($store->delete($row['id']));
        $this->assertNull($store->find($row['id']));
        $this->assertFalse($store->delete('999999'));
    }

    public function test_mata_kuliah_crud_dan_kode_unik_per_kurikulum(): void
    {
        $k = app(KurikulumDbStore::class)->create(['name' => 'K1']);
        $mk = app(MataKuliahDbStore::class);

        $data = [
            'kurikulum' => $k['id'], 'semester' => 1, 'kode' => 'TRK101', 'nama' => 'Algoritma',
            'sks_teori' => 2, 'sks_praktek' => 1, 'jam_teori' => 2, 'jam_praktek' => 2,
        ];
        $row = $mk->create($data);

        $this->assertSame('TRK101', $row['kode']);
        $this->assertSame(1, $row['sks_praktek']);
        $this->assertTrue($mk->kodeExists($k['id'], 'TRK101'));
        $this->assertFalse($mk->kodeExists($k['id'], 'TRK101', $row['id']));
        $this->assertSame(1, $mk->count($k['id']));

        $baru = $mk->update($row['id'], ['nama' => 'Algoritma Lanjut'] + $data);
        $this->assertSame('Algoritma Lanjut', $baru['nama']);

        $this->assertTrue($mk->delete($row['id']));
        $this->assertSame(0, $mk->count($k['id']));
    }

    public function test_akreditasi_hanya_satu_yang_aktif_dan_yang_aktif_tidak_bisa_dihapus(): void
    {
        $this->seed(DatabaseSeeder::class);
        $store = app(AkreditasiDbStore::class);

        $a = $store->create(['lembaga' => 'BAN-PT', 'year' => 2020], false);
        $this->assertSame('aktif', $a['status'], 'akreditasi pertama otomatis aktif');
        $this->assertSame('BAN-PT', $a['lembaga']);

        $b = $store->create(['lembaga' => 'LAM INFOKOM', 'year' => 2024, 'berlaku_sampai' => '2029-12-31'], true);
        $this->assertSame('aktif', $b['status']);
        $this->assertSame('2029-12-31', $b['berlaku_sampai']);
        $this->assertSame('nonaktif', $store->find($a['id'])['status']);

        $this->assertSame('aktif', $store->delete($b['id']));
        $this->assertSame('ok', $store->delete($a['id']));
        $this->assertSame('tidak-ada', $store->delete('999999'));

        $store->activate($b['id']);
        $this->assertSame('aktif', $store->find($b['id'])['status']);
    }
}
