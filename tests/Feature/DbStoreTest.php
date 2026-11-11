<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Repositories\AkreditasiDbStore;
use App\Repositories\DosenDbStore;
use App\Repositories\KurikulumDbStore;
use App\Repositories\MahasiswaDbStore;
use App\Repositories\MataKuliahDbStore;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    /** Buat satu kegiatan milik $idUser tanpa memakai tabel skim & kegiatan_dosen. */
    private function buatKegiatan(int $idUser, string $nama, ?string $kategoriNama = 'Seminar', ?string $peran = null, ?string $jenisNama = null): void
    {
        // 1. Jika $kategoriNama dikirim null, berikan nilai default 'Seminar'
        $kategoriNama = $kategoriNama ?: 'Seminar';

        // 2. Jika $jenisNama tidak ditentukan, otomatis cari atau buat jenis kegiatan untuk Mahasiswa
        if ($jenisNama) {
            $jenis = DB::table('jenis_kegiatan')->where('nama_jenis_kegiatan', $jenisNama)->value('id_jenis_kegiatan')
                ?? DB::table('jenis_kegiatan')->insertGetId(['nama_jenis_kegiatan' => $jenisNama]);
        } else {
            $jenis = DB::table('jenis_kegiatan')->where('nama_jenis_kegiatan', 'like', '%Mahasiswa%')->value('id_jenis_kegiatan')
                ?? DB::table('jenis_kegiatan')->insertGetId(['nama_jenis_kegiatan' => 'Kegiatan Mahasiswa']);
        }

        // 3. Cari/buat kategori sesuai jenis kegiatan
        $kategori = DB::table('kategori')->where('nama_kategori', $kategoriNama)->where('id_jenis_kegiatan', $jenis)->value('id_kategori')
            ?? DB::table('kategori')->insertGetId(['id_jenis_kegiatan' => $jenis, 'nama_kategori' => $kategoriNama]);

        $idKegiatan = DB::table('kegiatan')->insertGetId([
            'id_kategori' => $kategori,
            'id_user' => $idUser,
            'nama_kegiatan' => $nama,
            'tanggal_mulai' => '2025-03-01',
        ]);

        if ($peran) {
            DB::table('anggota_kegiatan')->insert([
                'id_kegiatan' => $idKegiatan,
                'id_user' => $idUser,
                'peran' => $peran,
            ]);
        }
    }

    public function test_dosen_aktivitas_kosong_untuk_dosen_baru_dan_terisi_sesuai_kegiatannya(): void
    {
        $this->seed(DatabaseSeeder::class);
        $store = app(DosenDbStore::class);

        $a = $store->create($this->dataDosen());
        $b = $store->create($this->dataDosen());

        // Dosen baru: keempat tab kosong
        foreach ($store->aktivitas($a['id']) as $tab) {
            $this->assertSame([], $tab['items']);
        }

        $idUserA = Dosen::find($a['id'])->id_user;
        $idUserB = Dosen::find($b['id'])->id_user;

        $this->buatKegiatan($idUserA, 'Deteksi hoaks', 'Penelitian', 'Ketua');
        $this->buatKegiatan($idUserA, 'Pelatihan sensor', 'Pengabdian', 'Anggota');
        $this->buatKegiatan($idUserA, 'Seminar Nasional AI', 'Seminar', 'Pemateri');
        $this->buatKegiatan($idUserB, 'Kegiatan dosen lain', 'Penelitian', 'Ketua');

        $tab = $store->aktivitas($a['id']);
        $this->assertCount(3, $tab['kegiatan']['items']);
        $this->assertSame('Deteksi hoaks', $tab['kegiatan']['items'][2]['title']);
        $this->assertSame('Penelitian', $tab['kegiatan']['items'][2]['type']);
        $this->assertSame('Ketua', $tab['kegiatan']['items'][2]['role']);
        $this->assertSame('2025', $tab['kegiatan']['items'][2]['year']);

        $this->assertSame([], $tab['pengajaran']['items']);

        // Kegiatan dosen lain tidak ikut tampil pada dosen A
        $this->assertCount(1, $store->aktivitas($b['id'])['kegiatan']['items']);
    }

    public function test_dosen_aktivitas_pengajaran_dari_enrollment_kelas_mata_kuliah(): void
    {
        $this->seed(DatabaseSeeder::class);
        $dosen = app(DosenDbStore::class);
        $a = $dosen->create($this->dataDosen());
        $b = $dosen->create($this->dataDosen());

        $k = app(KurikulumDbStore::class)->create(['name' => 'K1']);
        $mk = app(MataKuliahDbStore::class)->create([
            'kurikulum' => $k['id'], 'semester' => 3, 'kode' => 'TRK301', 'nama' => 'Basis Data II',
            'sks_teori' => 2, 'sks_praktek' => 1, 'jam_teori' => 2, 'jam_praktek' => 2,
        ]);

        $idTa = DB::table('tahun_akademik')->insertGetId(['tahun_akademik' => 'Ganjil 2026/2027', 'status' => 1]);
        $idKelas = DB::table('kelas')->insertGetId(['jenis_kelas' => 'B', 'keterangan' => 'Kelas B']);
        $idTingkat = DB::table('tingkat')->insertGetId(['tingkat' => 2, 'keterangan' => 'Tingkat 2']);
        $idEtk = DB::table('enrollment_tingkat_kelas')->insertGetId([
            'id_kelas' => $idKelas, 'id_prodi' => Prodi::query()->value('id_prodi'),
            'id_tingkat' => $idTingkat, 'id_tahun_akademik' => $idTa,
        ]);
        $idEmk = DB::table('enrollment_mata_kuliah')->insertGetId(['id_mk' => (int) $mk['id'], 'id_tahun_akademik' => $idTa]);
        DB::table('enrollment_kelas_mata_kuliah')->insert([
            'id_enrollment_tingkat_kelas' => $idEtk, 'id_enrollment_mk' => $idEmk, 'id_dosen' => (int) $a['id'],
        ]);

        $items = $dosen->aktivitas($a['id'])['pengajaran']['items'];
        $this->assertCount(1, $items);
        $this->assertSame('Basis Data II', $items[0]['title']);
        $this->assertSame('Ganjil 2026/2027', $items[0]['year']);
        $this->assertSame('Tingkat 2 Kelas B · 3 SKS', $items[0]['type']);
        $this->assertSame('Pengampu', $items[0]['role']);

        $this->assertSame([], $dosen->aktivitas($b['id'])['pengajaran']['items']);
    }

    /** Data acuan akademik: prodi, kelas A-D, tingkat 1-4, dan tahun akademik (hanya Ganjil 2026/2027 yang aktif). */
    private function siapkanAkademik(): void
    {
        Prodi::query()->firstOrCreate(['kode_prodi' => 'TRK'], ['nama_prodi' => 'Teknologi Rekayasa Komputer', 'jenjang' => 'D4']);

        foreach (['A', 'B', 'C', 'D'] as $k) {
            DB::table('kelas')->insert(['jenis_kelas' => $k, 'keterangan' => "Kelas {$k}"]);
        }
        foreach ([1, 2, 3, 4] as $n) {
            DB::table('tingkat')->insert(['tingkat' => $n, 'keterangan' => "Tingkat {$n}"]);
        }
        foreach ([['Ganjil 2025/2026', 0], ['Genap 2025/2026', 0], ['Ganjil 2026/2027', 1], ['Genap 2026/2027', 0]] as [$nama, $status]) {
            DB::table('tahun_akademik')->insert(['tahun_akademik' => $nama, 'status' => $status]);
        }
    }

    private function dataMahasiswa(array $o = []): array
    {
        return array_merge([
            'nim' => '4.33.25.1.09', 'nama' => 'UJI - Ridho', 'angkatan' => '2025', 'status' => 'Aktif',
            'email' => 'uji-'.uniqid().'@example.test', 'password' => 'rahasia123',
        ], $o);
    }

    public function test_mahasiswa_create_menempatkan_kelas_dan_update_tidak_memindahkannya(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->siapkanAkademik();
        $store = app(MahasiswaDbStore::class);

        $in = $this->dataMahasiswa();
        $row = $store->create($in);

        $this->assertSame('Tingkat 2', $row['level']);   // masuk 2025, tahun akademik aktif Ganjil 2026/2027
        $this->assertSame('TI-2B', $row['kelas']);
        $this->assertSame('Aktif', $row['status']);
        $this->assertSame('2025', $row['angkatan']);
        $this->assertTrue(Hash::check($in['password'], User::where('email', $in['email'])->first()->password));

        $baru = $store->update($row['id'], [
            'nama' => 'UJI - Ridho Baru', 'nim' => $in['nim'], 'angkatan' => 2025, 'status' => 'Non-aktif',
            'email' => $in['email'], 'password' => 'sandibaru123', 'foto' => 'mahasiswa-foto/uji.jpg',
        ]);
        $this->assertSame('UJI - Ridho Baru', $baru['nama']);
        $this->assertSame('Non-aktif', $baru['status']);
        $this->assertSame('mahasiswa-foto/uji.jpg', $baru['foto']);
        $this->assertSame('TI-2B', $baru['kelas']);
        $this->assertTrue(Hash::check('sandibaru123', User::where('email', $in['email'])->first()->password));

        $this->assertCount(1, array_filter($store->all(), fn ($r) => $r['id'] === $row['id']));
        $this->assertNull($store->update('999999', []));
        $this->assertNull($store->find('abc'));
    }

    public function test_mahasiswa_riwayat_kelas_dan_kegiatan_untuk_halaman_detail(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->siapkanAkademik();
        $store = app(MahasiswaDbStore::class);
        $row = $store->create($this->dataMahasiswa());

        $riwayat = $store->riwayatKelas($row['id']);
        $this->assertCount(3, $riwayat);
        $this->assertSame('2026/2027', $riwayat[0]['year']);
        $this->assertSame('Ganjil', $riwayat[0]['sem']);
        $this->assertSame('Tingkat 2', $riwayat[0]['level']);
        $this->assertSame('2B', $riwayat[0]['cls']);
        $this->assertTrue($riwayat[0]['current']);
        $this->assertSame([], $store->kegiatan($row['id']), 'mahasiswa baru belum punya kegiatan');

        $this->buatKegiatan(Mahasiswa::find($row['id'])->id_user, 'Lomba Web', null, 'Ketua Tim');
        $kegiatan = $store->kegiatan($row['id']);
        $this->assertCount(1, $kegiatan);
        $this->assertSame('Lomba Web', $kegiatan[0]['title']);
        $this->assertSame('Seminar', $kegiatan[0]['type']);
        $this->assertSame('Ketua Tim', $kegiatan[0]['role']);
        $this->assertSame('2025', $kegiatan[0]['year']);
    }

    public function test_mahasiswa_cek_unik_nim_dan_email(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->siapkanAkademik();
        $store = app(MahasiswaDbStore::class);
        $in = $this->dataMahasiswa();
        $row = $store->create($in);

        $this->assertTrue($store->usedByOther('nim', $in['nim'], ''));
        $this->assertTrue($store->usedByOther('email', $in['email'], ''));
        $this->assertFalse($store->usedByOther('nim', $in['nim'], $row['id']));
        $this->assertFalse($store->usedByOther('email', $in['email'], $row['id']));
        $this->assertFalse($store->usedByOther('nim', '4.33.99.0.01', ''));
    }

    public function test_mahasiswa_hapus_ikut_menghapus_akun_dan_riwayat_kelas(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->siapkanAkademik();
        $store = app(MahasiswaDbStore::class);
        $in = $this->dataMahasiswa();
        $row = $store->create($in);

        $this->assertSame(3, DB::table('enrollment_mahasiswa_tingkat_kelas')->where('id_mahasiswa', (int) $row['id'])->count());

        $this->assertSame('ok', $store->delete($row['id']));
        $this->assertNull($store->find($row['id']));
        $this->assertDatabaseMissing('users', ['email' => $in['email']]);
        $this->assertSame(0, DB::table('enrollment_mahasiswa_tingkat_kelas')->where('id_mahasiswa', (int) $row['id'])->count());
        $this->assertSame('tidak-ada', $store->delete('999999'));
    }

    public function test_dosen_foto_saat_tambah_saklar_status_dan_daftar_landing_hanya_yang_aktif(): void
    {
        $this->seed(DatabaseSeeder::class);
        $store = app(DosenDbStore::class);

        $a = $store->create($this->dataDosen(['foto' => 'foto-dosen/a.jpg']));
        $b = $store->create($this->dataDosen());
        $this->assertSame('foto-dosen/a.jpg', $a['foto']);
        $this->assertNull($b['foto']);

        $this->assertContains($a['id'], array_column($store->publik(), 'id'));
        $this->assertContains($b['id'], array_column($store->publik(), 'id'));

        // nonaktifkan A: hilang dari landing, datanya tetap ada
        $this->assertSame('nonaktif', $store->toggleStatus($a['id'])['status']);
        $this->assertNotContains($a['id'], array_column($store->publik(), 'id'));
        $this->assertContains($b['id'], array_column($store->publik(), 'id'));
        $this->assertNotNull($store->find($a['id']));

        // aktifkan lagi
        $this->assertSame('aktif', $store->toggleStatus($a['id'])['status']);
        $this->assertContains($a['id'], array_column($store->publik(), 'id'));

        $item = collect($store->publik())->firstWhere('id', $b['id']);
        $this->assertSame('Lektor', $item['jabatan']);
        $this->assertNull($store->toggleStatus('999999'));
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
