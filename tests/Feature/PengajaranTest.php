<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\EnrollmentKelasMataKuliah;
use App\Models\EnrollmentMataKuliah;
use App\Models\EnrollmentTingkatKelas;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pengajaran dosen lewat API (/api/v1/pengajaran, dijaga auth:sanctum).
 * Halaman webnya (/pengajaran, /pengajaran/create) hanya cangkang yang mengambil data dari API.
 */
class PengajaranTest extends TestCase
{
    use RefreshDatabase;

    private const API = '/api/v1/pengajaran';

    private Prodi $prodi;

    private TahunAkademik $ta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->prodi = Prodi::create(['kode_prodi' => 'TRK', 'nama_prodi' => 'Teknologi Rekayasa Komputer', 'jenjang' => 'D4']);
        $this->ta = TahunAkademik::create(['tahun_akademik' => 'Ganjil 2025/2026', 'status' => true]);
    }

    private function dosen(string $nama = 'Kuwat Santoso'): Dosen
    {
        $role = DB::table('roles')->where('nama_role', 'dosen')->value('id_role')
            ?? DB::table('roles')->insertGetId(['nama_role' => 'dosen'], 'id_role');
        $user = User::create(['id_role' => $role, 'email' => uniqid().'@test.local', 'password' => 'password']);

        return Dosen::create(['id_user' => $user->getKey(), 'nip' => (string) random_int(10 ** 9, 10 ** 10 - 1), 'nama' => $nama]);
    }

    /** Login sebagai $dosen lewat guard sanctum (sama seperti request ber-token). */
    private function sebagai(Dosen $dosen): static
    {
        return $this->actingAs($dosen->user, 'sanctum');
    }

    private function rombel(int $tingkat = 2, string $huruf = 'A', ?TahunAkademik $ta = null): EnrollmentTingkatKelas
    {
        return EnrollmentTingkatKelas::create([
            'id_kelas' => Kelas::firstOrCreate(['jenis_kelas' => $huruf], ['keterangan' => "Kelas $huruf"])->getKey(),
            'id_prodi' => $this->prodi->getKey(),
            'id_tingkat' => Tingkat::firstOrCreate(['tingkat' => $tingkat], ['keterangan' => "Tingkat $tingkat"])->getKey(),
            'id_tahun_akademik' => ($ta ?? $this->ta)->getKey(),
        ]);
    }

    private function matkul(string $nama = 'Jaringan Komputer', string $kode = 'TI2101', ?TahunAkademik $ta = null): EnrollmentMataKuliah
    {
        $kurikulum = DB::table('kurikulum')->value('id_kurikulum')
            ?? DB::table('kurikulum')->insertGetId(['id_prodi' => $this->prodi->getKey(), 'nama_kurikulum' => 'K2024', 'status' => true], 'id_kurikulum');
        $mk = MataKuliah::create([
            'id_kurikulum' => $kurikulum, 'semester' => 3, 'kode_mk' => $kode, 'nama_mk' => $nama,
            'sks_teori' => 2, 'sks_praktik' => 1, 'jam_teori' => 2, 'jam_praktek' => 2,
        ]);

        return EnrollmentMataKuliah::create(['id_mk' => $mk->getKey(), 'id_tahun_akademik' => ($ta ?? $this->ta)->getKey()]);
    }

    private function ampu(Dosen $dosen, EnrollmentTingkatKelas $rombel, EnrollmentMataKuliah $mk): EnrollmentKelasMataKuliah
    {
        return EnrollmentKelasMataKuliah::create([
            'id_enrollment_tingkat_kelas' => $rombel->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
            'id_dosen' => $dosen->getKey(),
        ]);
    }

    // ---------- Halaman web ----------

    public function test_page_routes_point_to_web_pages_not_to_the_api(): void
    {
        // Penjaga bentrok nama: sidebar memakai route('pengajaran.index'), harus tetap /pengajaran.
        $this->assertSame(url('/pengajaran'), route('pengajaran.index'));
        $this->assertSame(url('/pengajaran/create'), route('pengajaran.create'));
        $this->assertSame(url('/api/v1/pengajaran'), route('api.pengajaran.index'));
    }

    public function test_web_pages_render_without_hitting_login(): void
    {
        $this->get(route('pengajaran.index'))->assertOk()->assertSee('Tambah Pengajaran');
        $this->get(route('pengajaran.create'))->assertOk()->assertSee('Dokumen Pendukung')->assertSee('Tambah dokumen');
    }

    // ---------- API: login ----------

    public function test_api_requires_login(): void
    {
        $this->getJson(self::API)->assertUnauthorized();
        $this->getJson(self::API.'/opsi')->assertUnauthorized();
        $this->postJson(self::API, [])->assertUnauthorized();
    }

    public function test_api_rejects_accounts_that_are_not_lecturers(): void
    {
        $role = DB::table('roles')->insertGetId(['nama_role' => 'mahasiswa'], 'id_role');
        $user = User::create(['id_role' => $role, 'email' => 'mhs@test.local', 'password' => 'password']);

        $this->actingAs($user, 'sanctum')->getJson(self::API)->assertForbidden();
        $this->actingAs($user, 'sanctum')->postJson(self::API, [])->assertForbidden();
    }

    // ---------- API: daftar & opsi ----------

    public function test_list_only_shows_the_logged_in_lecturers_teaching(): void
    {
        $saya = $this->dosen();
        $lain = $this->dosen('Dosen Lain');
        $this->ampu($saya, $this->rombel(2, 'A'), $this->matkul('Jaringan Komputer', 'TI2101'));
        $this->ampu($lain, $this->rombel(3, 'B'), $this->matkul('Sistem Operasi', 'TI3101'));

        $this->sebagai($saya)->getJson(self::API)
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data.pengajaran')
            ->assertJsonPath('data.pengajaran.0.matkul', 'Jaringan Komputer')
            ->assertJsonPath('data.pengajaran.0.kelas', '2A')
            ->assertJsonPath('data.pengajaran.0.tahun', '2025/2026')
            ->assertJsonPath('data.pengajaran.0.semester', 'Ganjil')
            ->assertJsonPath('data.daftarKelas', ['2A']);
    }

    public function test_form_options_come_from_the_database_with_the_lecturer_name(): void
    {
        $dosen = $this->dosen();
        $this->rombel(2, 'A');
        $this->matkul('Kecerdasan Buatan', 'TI4101');

        $this->sebagai($dosen)->getJson(self::API.'/opsi')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.dosen', 'Kuwat Santoso')
            ->assertJsonPath('data.tahun.0.label', 'Ganjil 2025/2026')
            ->assertJsonPath('data.tahun.0.semester', 'Ganjil')
            ->assertJsonPath('data.rombel.0.kelas', '2A')
            ->assertJsonPath('data.matkul.0.label', 'Kecerdasan Buatan (TI4101)');
    }

    // ---------- API: simpan ----------

    public function test_lecturer_can_store_teaching_for_themselves(): void
    {
        $dosen = $this->dosen();
        $rombel = $this->rombel();
        $mk = $this->matkul();

        $this->sebagai($dosen)->postJson(self::API, [
            'id_enrollment_tingkat_kelas' => $rombel->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
            'id_dosen' => 999, // dosen tidak boleh bisa dipilih dari luar
        ])->assertCreated()->assertJsonPath('status', 'success');

        $item = EnrollmentKelasMataKuliah::firstOrFail();
        $this->assertSame($dosen->getKey(), $item->id_dosen);
        $this->assertDatabaseCount('enrollment_kelas_mata_kuliah', 1);
    }

    public function test_store_validates_the_fields(): void
    {
        $dosen = $this->dosen();

        $this->sebagai($dosen)->postJson(self::API, [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['id_enrollment_tingkat_kelas', 'id_enrollment_mk']);

        $this->sebagai($dosen)->postJson(self::API, [
            'id_enrollment_tingkat_kelas' => 9999,
            'id_enrollment_mk' => 9999,
        ])->assertStatus(422)->assertJsonValidationErrors(['id_enrollment_tingkat_kelas', 'id_enrollment_mk']);

        $this->assertDatabaseCount('enrollment_kelas_mata_kuliah', 0);
    }

    public function test_class_and_course_must_be_from_the_same_academic_year(): void
    {
        $dosen = $this->dosen();
        $genap = TahunAkademik::create(['tahun_akademik' => 'Genap 2025/2026', 'status' => false]);

        $this->sebagai($dosen)->postJson(self::API, [
            'id_enrollment_tingkat_kelas' => $this->rombel()->getKey(),
            'id_enrollment_mk' => $this->matkul('Sistem Operasi', 'TI2102', $genap)->getKey(),
        ])->assertStatus(422)->assertJsonValidationErrors('id_enrollment_mk');

        $this->assertDatabaseCount('enrollment_kelas_mata_kuliah', 0);
    }

    public function test_a_course_in_a_class_cannot_have_two_lecturers_or_be_added_twice(): void
    {
        $saya = $this->dosen();
        $lain = $this->dosen('Dosen Lain');
        $rombel = $this->rombel();
        $mk = $this->matkul();
        $this->ampu($lain, $rombel, $mk);

        $data = ['id_enrollment_tingkat_kelas' => $rombel->getKey(), 'id_enrollment_mk' => $mk->getKey()];

        $this->sebagai($saya)->postJson(self::API, $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.id_enrollment_mk.0', 'Mata kuliah ini di kelas tersebut sudah diampu dosen lain.');

        $this->sebagai($lain)->postJson(self::API, $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.id_enrollment_mk.0', 'Anda sudah mengampu mata kuliah ini di kelas tersebut.');

        $this->assertDatabaseCount('enrollment_kelas_mata_kuliah', 1);
    }

    // ---------- API: detail & ubah ----------

    public function test_detail_shows_only_own_teaching_and_hides_others_with_404(): void
    {
        $saya = $this->dosen();
        $lain = $this->dosen('Dosen Lain');
        $milik = $this->ampu($saya, $this->rombel(2, 'B'), $this->matkul('Jaringan Komputer', 'TI2101'));
        $orangLain = $this->ampu($lain, $this->rombel(3, 'A'), $this->matkul('Sistem Operasi', 'TI3101'));

        $this->sebagai($saya)->getJson(self::API.'/'.$milik->getKey())
            ->assertOk()
            ->assertJsonPath('data.detail.matkul', 'Jaringan Komputer')
            ->assertJsonPath('data.detail.kelas', 'Kelas 2B')
            ->assertJsonPath('data.detail.kode', 'TI2101')
            ->assertJsonPath('data.dokumen', []);

        $this->sebagai($saya)->getJson(self::API.'/'.$orangLain->getKey())->assertNotFound();
    }

    public function test_lecturer_can_update_own_teaching(): void
    {
        $saya = $this->dosen();
        $item = $this->ampu($saya, $this->rombel(2, 'A'), $this->matkul('Jaringan Komputer', 'TI2101'));
        $rombelBaru = $this->rombel(2, 'B');
        $mkBaru = $this->matkul('Sistem Basis Data', 'TI2103');

        $this->sebagai($saya)->putJson(self::API.'/'.$item->getKey(), [
            'id_enrollment_tingkat_kelas' => $rombelBaru->getKey(),
            'id_enrollment_mk' => $mkBaru->getKey(),
        ])->assertOk()->assertJsonPath('status', 'success');

        $item->refresh();
        $this->assertSame($rombelBaru->getKey(), $item->id_enrollment_tingkat_kelas);
        $this->assertSame($mkBaru->getKey(), $item->id_enrollment_mk);
        $this->assertSame($saya->getKey(), $item->id_dosen);
    }

    public function test_saving_update_without_changes_is_not_rejected_as_duplicate(): void
    {
        $saya = $this->dosen();
        $rombel = $this->rombel();
        $mk = $this->matkul();
        $item = $this->ampu($saya, $rombel, $mk);

        $this->sebagai($saya)->putJson(self::API.'/'.$item->getKey(), [
            'id_enrollment_tingkat_kelas' => $rombel->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
        ])->assertOk();
    }

    public function test_lecturer_cannot_update_another_lecturers_teaching(): void
    {
        $saya = $this->dosen();
        $lain = $this->dosen('Dosen Lain');
        $rombel = $this->rombel();
        $mk = $this->matkul();
        $orangLain = $this->ampu($lain, $rombel, $mk);

        $this->sebagai($saya)->putJson(self::API.'/'.$orangLain->getKey(), [
            'id_enrollment_tingkat_kelas' => $this->rombel(3, 'C')->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
        ])->assertNotFound();

        $this->assertSame($rombel->getKey(), $orangLain->fresh()->id_enrollment_tingkat_kelas);
    }
}
