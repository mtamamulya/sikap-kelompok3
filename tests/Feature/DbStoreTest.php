<?php

namespace Tests\Feature;

use App\Models\Prodi;
use App\Models\User;
use App\Repositories\AkreditasiDbStore;
use App\Repositories\DosenDbStore;
use App\Repositories\KurikulumDbStore;
use App\Repositories\MataKuliahDbStore;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

/** Menguji versi database dari store (Kurikulum, Mata Kuliah, Akreditasi, Dosen). Memakai database tes, bukan database bersama. */
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

    /** Data dosen valid; $o menimpa sebagian isinya. */
    private function dataDosen(array $o = []): array
    {
        return array_merge([
            'nama' => 'UJI - Dosen '.uniqid(),
            'nip' => (string) random_int(1000000000, 9999999999),
            'status' => 'aktif',
            'email' => 'uji-'.uniqid().'@example.test',
            'role' => 'dosen',
            'password' => 'rahasia123',
            'pangkat' => 'Lektor',
            'golongan' => 'III/c',
            'sk' => '001/UJI/2026',
            'tanggal' => '2026-01-15',
        ], $o);
    }

    public function test_dosen_create_membuat_user_dosen_dan_riwayat_pertama(): void
    {
        $this->seed(DatabaseSeeder::class);
        $in = $this->dataDosen();

        $row = app(DosenDbStore::class)->create($in);

        $this->assertSame($in['nama'], $row['nama']);
        $this->assertSame('dosen', $row['role']);
        $this->assertSame('aktif', $row['status']);
        $this->assertSame('Lektor', $row['jabatan']);
        $this->assertSame('III/c', $row['golongan']);
        $this->assertCount(1, $row['riwayat']);
        $this->assertDatabaseHas('histori_pangkat_golongan', ['id_dosen' => (int) $row['id'], 'golongan' => '3C']);

        $user = User::where('email', $in['email'])->first();
        $this->assertTrue(Hash::check($in['password'], $user->password), 'sandi harus ter-hash satu kali saja');
    }

    public function test_dosen_role_kaprodi_diterima_dan_role_lain_ditolak_tanpa_sisa_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $store = app(DosenDbStore::class);

        $this->assertSame('kaprodi', $store->create($this->dataDosen(['role' => 'kaprodi']))['role']);

        $in = $this->dataDosen(['role' => 'admin']);
        try {
            $store->create($in);
            $this->fail('Seharusnya melempar RuntimeException.');
        } catch (RuntimeException) {
            $this->assertDatabaseMissing('users', ['email' => $in['email']]);
            $this->assertDatabaseMissing('dosen', ['nip' => $in['nip']]);
        }
    }

    public function test_dosen_update_menambah_riwayat_dan_menjadi_pangkat_saat_ini(): void
    {
        $this->seed(DatabaseSeeder::class);
        $store = app(DosenDbStore::class);
        $row = $store->create($this->dataDosen());

        $baru = $store->update($row['id'], [
            'nama' => $row['nama'], 'nip' => $row['nip'], 'status' => 'nonaktif',
            'email' => $row['email'], 'role' => 'dosen', 'status_akun' => 'aktif',
            'password' => 'sandibaru123', 'foto' => 'foto-dosen/uji.jpg',
        ], ['pangkat' => 'Lektor Kepala', 'golongan' => 'IV/a', 'sk' => '002/UJI/2027', 'tanggal' => '2027-02-01']);

        $this->assertSame('nonaktif', $baru['status']);
        $this->assertSame('Lektor Kepala', $baru['jabatan']);
        $this->assertSame('IV/a', $baru['golongan']);
        $this->assertCount(2, $baru['riwayat']);
        $this->assertSame('foto-dosen/uji.jpg', $baru['foto']);
        $this->assertTrue(Hash::check('sandibaru123', User::where('email', $row['email'])->first()->password));
        $this->assertNull($store->update('999999', []));
    }

    public function test_dosen_pilihan_dropdown_berasal_dari_enum_dan_semuanya_bisa_disimpan(): void
    {
        $this->seed(DatabaseSeeder::class);
        $store = app(DosenDbStore::class);

        $this->assertNotEmpty($store->opsiPangkat());
        $this->assertNotEmpty($store->opsiGolongan());

        foreach ($store->opsiGolongan() as $golongan) {
            $row = $store->create($this->dataDosen(['golongan' => $golongan, 'pangkat' => $store->opsiPangkat()[0]]));
            $this->assertSame($golongan, $row['golongan'], "golongan {$golongan} harus bisa disimpan dan dibaca kembali");
        }
    }

    public function test_dosen_cek_unik_nip_dan_email(): void
    {
        $this->seed(DatabaseSeeder::class);
        $store = app(DosenDbStore::class);
        $row = $store->create($this->dataDosen());

        $this->assertTrue($store->exists('nip', $row['nip']));
        $this->assertTrue($store->exists('email', $row['email']));
        $this->assertFalse($store->usedByOther('nip', $row['nip'], $row['id']));
        $this->assertFalse($store->usedByOther('email', $row['email'], $row['id']));
        $this->assertFalse($store->exists('nip', '0000000000'));
        $this->assertNull($store->find('abc'));
    }
}