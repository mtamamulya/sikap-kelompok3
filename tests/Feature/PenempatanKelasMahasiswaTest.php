<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Repositories\PenempatanKelasMahasiswa;
use App\Support\NimMahasiswa;
use App\Support\SemesterAkademik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Aturan NIM, perhitungan semester/tingkat, dan penempatan kelas otomatis. Database tes, bukan database bersama. */
class PenempatanKelasMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    private function siapkanReferensi(): void
    {
        Prodi::query()->create(['kode_prodi' => 'TRK', 'nama_prodi' => 'Teknologi Rekayasa Komputer', 'jenjang' => 'D4']);
        DB::table('roles')->insert(['nama_role' => 'mahasiswa']);

        foreach (['A', 'B', 'C', 'D'] as $k) {
            DB::table('kelas')->insert(['jenis_kelas' => $k, 'keterangan' => "Kelas {$k}"]);
        }
        foreach ([1, 2, 3, 4] as $t) {
            DB::table('tingkat')->insert(['tingkat' => $t, 'keterangan' => "Tingkat {$t}"]);
        }
        foreach ([['Ganjil 2025/2026', 0], ['Genap 2025/2026', 0], ['Ganjil 2026/2027', 1], ['Genap 2026/2027', 0]] as [$nama, $status]) {
            DB::table('tahun_akademik')->insert(['tahun_akademik' => $nama, 'status' => $status]);
        }
    }

    private function buatMahasiswa(string $nim, int $tahunMasuk, bool $akunAktif = true): Mahasiswa
    {
        $u = new User;
        $u->forceFill([
            'id_role' => DB::table('roles')->where('nama_role', 'mahasiswa')->value('id_role'),
            'email' => 'uji-'.uniqid().'@example.test',
            'password' => 'rahasia123',
            'status' => $akunAktif,
        ])->save();

        $m = new Mahasiswa;
        $m->forceFill(['id_user' => $u->id_user, 'nim' => $nim, 'nama' => 'UJI '.$nim, 'tahun_masuk' => $tahunMasuk])->save();

        return $m;
    }

    private function ta(string $nama): object
    {
        return DB::table('tahun_akademik')->where('tahun_akademik', $nama)->first();
    }

    public function test_nim_diuraikan_dan_divalidasi(): void
    {
        $this->assertSame(
            ['jenjang' => 'D4', 'kode_prodi' => '33', 'tahun_masuk' => 2025, 'kelas' => 'B', 'absen' => '09'],
            NimMahasiswa::parse('4.33.25.1.09')
        );
        $this->assertSame('A', NimMahasiswa::parse('4.33.25.0.01')['kelas']);
        $this->assertSame('C', NimMahasiswa::parse('4.33.25.2.01')['kelas']);

        $this->assertNull(NimMahasiswa::parse('43325119'), 'tanpa titik');
        $this->assertNull(NimMahasiswa::parse('4.33.25.1.9'), 'absen harus 2 digit');
        $this->assertNull(NimMahasiswa::parse('4.33.25.3.09'), 'TRK hanya punya 3 kelas (0-2)');
        $this->assertNull(NimMahasiswa::parse('4.34.25.1.09'), 'kode prodi lain');
        $this->assertNull(NimMahasiswa::parse('5.33.25.1.09'), 'jenjang tidak dikenal');

        $this->assertNull(NimMahasiswa::kesalahan('4.33.25.1.09', 2025));
        $this->assertStringContainsString('angkatan', NimMahasiswa::kesalahan('4.33.25.1.09', 2026));
        $this->assertStringContainsString('Format NIM', NimMahasiswa::kesalahan('43325119'));

        $this->assertSame('TI-2B', NimMahasiswa::label(2, 'B'));
    }

    public function test_semester_dan_tingkat_mengikuti_tahun_akademik(): void
    {
        // Ridho: masuk 2025. [tahun akademik, semester, tingkat]
        $tabel = [
            ['Ganjil 2025/2026', 1, 1], ['Genap 2025/2026', 2, 1],
            ['Ganjil 2026/2027', 3, 2], ['Genap 2026/2027', 4, 2],
            ['Ganjil 2027/2028', 5, 3], ['Ganjil 2028/2029', 7, 4], ['Genap 2028/2029', 8, 4],
        ];
        foreach ($tabel as [$nama, $semester, $tingkat]) {
            $s = SemesterAkademik::semester(2025, SemesterAkademik::parse($nama));
            $this->assertSame($semester, $s, $nama);
            $this->assertSame($tingkat, SemesterAkademik::tingkat($s), $nama);
        }

        $this->assertNull(SemesterAkademik::parse('2026/2027'));
        $this->assertNull(SemesterAkademik::parse('Ganjil 2026/2028'));
    }

    public function test_buat_untuk_semester_menempatkan_mahasiswa_sesuai_aturan_dan_aman_diulang(): void
    {
        $this->siapkanReferensi();
        $svc = app(PenempatanKelasMahasiswa::class);

        $ridho = $this->buatMahasiswa('4.33.25.1.09', 2025);       // semester 3 -> tingkat 2, kelas B
        $baru = $this->buatMahasiswa('4.33.26.0.01', 2026);        // semester 1 -> tingkat 1, kelas A
        $this->buatMahasiswa('4.33.20.2.05', 2020);
        $this->buatMahasiswa('4.33.25.2.10', 2025, false);         // akun nonaktif -> dilewati
        $this->buatMahasiswa('43325119', 2025);                    // NIM salah format

        $idTa = (int) $this->ta('Ganjil 2026/2027')->id_tahun_akademik;
        $this->assertSame(
            ['dibuat' => 2, 'sudah-ada' => 0, 'lulus' => 1, 'belum-mulai' => 0, 'nim-tidak-valid' => 1],
            $svc->buatUntukSemester($idTa)
        );

        $this->assertSame('TI-2B', $svc->kelasSaatIni($ridho->id_mahasiswa)['label']);
        $this->assertSame('TI-1A', $svc->kelasSaatIni($baru->id_mahasiswa)['label']);

        // diulang: tidak membuat baris ganda
        $this->assertSame(
            ['dibuat' => 0, 'sudah-ada' => 2, 'lulus' => 0, 'belum-mulai' => 0, 'nim-tidak-valid' => 1],
            $svc->buatUntukSemester($idTa)
        );
        $this->assertSame(2, DB::table('enrollment_mahasiswa_tingkat_kelas')->count());

        // semester Genap: tingkat tetap, tetapi baris baru untuk tahun akademik itu
        $genap = $this->ta('Genap 2026/2027');
        $this->assertSame('dibuat', $svc->tempatkan($ridho, $genap));
        $this->assertSame('TI-2B', $svc->kelasSaatIni($ridho->id_mahasiswa, $genap)['label']);
    }

    public function test_mahasiswa_belum_masuk_dan_penempatan_manual_tidak_ditimpa(): void
    {
        $this->siapkanReferensi();
        $svc = app(PenempatanKelasMahasiswa::class);

        $baru = $this->buatMahasiswa('4.33.26.0.01', 2026);
        $this->assertSame('belum-mulai', $svc->tempatkan($baru, $this->ta('Ganjil 2025/2026')));

        // admin memindahkan mahasiswa ke kelas C secara manual
        $m = $this->buatMahasiswa('4.33.25.1.10', 2025);
        $ta = $this->ta('Ganjil 2026/2027');
        $idEtk = DB::table('enrollment_tingkat_kelas')->insertGetId([
            'id_kelas' => DB::table('kelas')->where('jenis_kelas', 'C')->value('id_kelas'),
            'id_prodi' => Prodi::query()->value('id_prodi'),
            'id_tingkat' => DB::table('tingkat')->where('tingkat', 2)->value('id_tingkat'),
            'id_tahun_akademik' => $ta->id_tahun_akademik,
        ]);
        DB::table('enrollment_mahasiswa_tingkat_kelas')->insert(['id_enrollment_tingkat_kelas' => $idEtk, 'id_mahasiswa' => $m->id_mahasiswa]);

        $this->assertSame('sudah-ada', $svc->tempatkan($m, $ta));
        $this->assertSame('TI-2C', $svc->kelasSaatIni($m->id_mahasiswa, $ta)['label']);
    }
}
