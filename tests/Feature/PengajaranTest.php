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

class PengajaranTest extends TestCase
{
    use RefreshDatabase;

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

    /** Kelas (rombel) tingkat $tingkat kelas $huruf pada tahun akademik $ta. */
    private function rombel(int $tingkat = 2, string $huruf = 'A', ?TahunAkademik $ta = null): EnrollmentTingkatKelas
    {
        return EnrollmentTingkatKelas::create([
            'id_kelas' => Kelas::firstOrCreate(['jenis_kelas' => $huruf], ['keterangan' => "Kelas $huruf"])->getKey(),
            'id_prodi' => $this->prodi->getKey(),
            'id_tingkat' => Tingkat::firstOrCreate(['tingkat' => $tingkat], ['keterangan' => "Tingkat $tingkat"])->getKey(),
            'id_tahun_akademik' => ($ta ?? $this->ta)->getKey(),
        ]);
    }

    /** Mata kuliah yang ditawarkan pada tahun akademik $ta. */
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

    public function test_list_only_shows_the_logged_in_lecturers_teaching(): void
    {
        $saya = $this->dosen();
        $lain = $this->dosen('Dosen Lain');
        $this->ampu($saya, $this->rombel(2, 'A'), $this->matkul('Jaringan Komputer', 'TI2101'));
        $this->ampu($lain, $this->rombel(3, 'B'), $this->matkul('Sistem Operasi', 'TI3101'));

        $this->actingAs($saya->user)->get(route('pengajaran.index'))
            ->assertOk()
            ->assertViewIs('dosen.pengajaran.index')
            ->assertSee('class="frontend-shell dosen-shell', false)
            ->assertSee('Jaringan Komputer')
            ->assertDontSee('Sistem Operasi')
            ->assertViewHas('pengajaran', fn ($rows) => count($rows) === 1
                && $rows[0]['kelas'] === '2A' && $rows[0]['tahun'] === '2025/2026' && $rows[0]['semester'] === 'Ganjil');
    }

    public function test_list_and_form_render_without_login_but_stay_empty(): void
    {
        $this->get(route('pengajaran.index'))->assertOk()->assertViewHas('pengajaran', []);
        $this->get(route('pengajaran.create'))->assertOk()->assertSee('class="frontend-shell dosen-shell', false);
    }

    public function test_create_form_offers_classes_and_courses_from_the_database(): void
    {
        $dosen = $this->dosen();
        $this->rombel(2, 'A');
        $this->matkul('Kecerdasan Buatan', 'TI4101');

        $this->actingAs($dosen->user)->get(route('pengajaran.create'))
            ->assertOk()
            ->assertSee('name="id_enrollment_tingkat_kelas"', false)
            ->assertSee('name="id_enrollment_mk"', false)
            ->assertSee("Alpine.data('formPengajaran'", false)
            ->assertSee('Dokumen Pendukung')
            ->assertSee('Tambah dokumen')
            ->assertSee('dokumen belum disimpan ke database')
            ->assertSee('Kuwat Santoso (akun Anda)')
            ->assertSee('Kecerdasan Buatan (TI4101)')
            ->assertSee('Ganjil 2025');
    }

    public function test_lecturer_can_store_teaching_for_themselves(): void
    {
        $dosen = $this->dosen();
        $rombel = $this->rombel();
        $mk = $this->matkul();

        $response = $this->actingAs($dosen->user)->post(route('pengajaran.store'), [
            'id_enrollment_tingkat_kelas' => $rombel->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
            'id_dosen' => 999, // dosen tidak boleh bisa dipilih dari form
        ]);

        $item = EnrollmentKelasMataKuliah::firstOrFail();
        $response->assertRedirect(route('pengajaran.show', $item->getKey()))->assertSessionHas('sukses');
        $this->assertSame($dosen->getKey(), $item->id_dosen);
        $this->assertDatabaseCount('enrollment_kelas_mata_kuliah', 1);
    }

    public function test_store_requires_a_lecturer_account(): void
    {
        $this->post(route('pengajaran.store'), [
            'id_enrollment_tingkat_kelas' => $this->rombel()->getKey(),
            'id_enrollment_mk' => $this->matkul()->getKey(),
        ])->assertForbidden();

        $this->assertDatabaseCount('enrollment_kelas_mata_kuliah', 0);
    }

    public function test_store_validates_the_fields(): void
    {
        $dosen = $this->dosen();

        $this->actingAs($dosen->user)->post(route('pengajaran.store'), [])
            ->assertSessionHasErrors(['id_enrollment_tingkat_kelas', 'id_enrollment_mk']);

        $this->actingAs($dosen->user)->post(route('pengajaran.store'), [
            'id_enrollment_tingkat_kelas' => 9999,
            'id_enrollment_mk' => 9999,
        ])->assertSessionHasErrors(['id_enrollment_tingkat_kelas', 'id_enrollment_mk']);
    }

    public function test_class_and_course_must_be_from_the_same_academic_year(): void
    {
        $dosen = $this->dosen();
        $genap = TahunAkademik::create(['tahun_akademik' => 'Genap 2025/2026', 'status' => false]);

        $this->actingAs($dosen->user)->post(route('pengajaran.store'), [
            'id_enrollment_tingkat_kelas' => $this->rombel()->getKey(),
            'id_enrollment_mk' => $this->matkul('Sistem Operasi', 'TI2102', $genap)->getKey(),
        ])->assertSessionHasErrors('id_enrollment_mk');

        $this->assertDatabaseCount('enrollment_kelas_mata_kuliah', 0);
    }

    public function test_a_course_in_a_class_cannot_have_two_lecturers_or_be_added_twice(): void
    {
        $saya = $this->dosen();
        $lain = $this->dosen('Dosen Lain');
        $rombel = $this->rombel();
        $mk = $this->matkul();
        $this->ampu($lain, $rombel, $mk);

        $this->actingAs($saya->user)->post(route('pengajaran.store'), [
            'id_enrollment_tingkat_kelas' => $rombel->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
        ])->assertSessionHasErrors(['id_enrollment_mk' => 'Mata kuliah ini di kelas tersebut sudah diampu dosen lain.']);

        $this->actingAs($lain->user)->post(route('pengajaran.store'), [
            'id_enrollment_tingkat_kelas' => $rombel->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
        ])->assertSessionHasErrors(['id_enrollment_mk' => 'Anda sudah mengampu mata kuliah ini di kelas tersebut.']);

        $this->assertDatabaseCount('enrollment_kelas_mata_kuliah', 1);
    }

    public function test_detail_shows_only_own_teaching_and_hides_others_with_404(): void
    {
        $saya = $this->dosen();
        $lain = $this->dosen('Dosen Lain');
        $milik = $this->ampu($saya, $this->rombel(2, 'B'), $this->matkul('Jaringan Komputer', 'TI2101'));
        $orangLain = $this->ampu($lain, $this->rombel(3, 'A'), $this->matkul('Sistem Operasi', 'TI3101'));

        $this->actingAs($saya->user)->get(route('pengajaran.show', $milik->getKey()))
            ->assertOk()
            ->assertSee('class="frontend-shell dosen-shell', false)
            ->assertSee('Detail Pengajaran')
            ->assertSee('Jaringan Komputer')
            ->assertSee('Kelas 2B')
            ->assertSee('TI2101')
            ->assertSee('Belum ada dokumen pendukung.');

        $this->actingAs($saya->user)->get(route('pengajaran.show', $orangLain->getKey()))->assertNotFound();
        $this->actingAs($saya->user)->get(route('pengajaran.edit', $orangLain->getKey()))->assertNotFound();
    }

    public function test_detail_and_edit_are_hidden_when_not_logged_in(): void
    {
        $item = $this->ampu($this->dosen(), $this->rombel(), $this->matkul());

        $this->get(route('pengajaran.show', $item->getKey()))->assertNotFound();
        $this->get(route('pengajaran.edit', $item->getKey()))->assertNotFound();
    }

    public function test_lecturer_can_edit_own_teaching(): void
    {
        $saya = $this->dosen();
        $item = $this->ampu($saya, $this->rombel(2, 'A'), $this->matkul('Jaringan Komputer', 'TI2101'));
        $rombelBaru = $this->rombel(2, 'B');
        $mkBaru = $this->matkul('Sistem Basis Data', 'TI2103');

        $this->actingAs($saya->user)->get(route('pengajaran.edit', $item->getKey()))
            ->assertOk()
            ->assertSee('Edit Pengajaran')
            ->assertSee('name="_method" value="PUT"', false);

        $this->actingAs($saya->user)->put(route('pengajaran.update', $item->getKey()), [
            'id_enrollment_tingkat_kelas' => $rombelBaru->getKey(),
            'id_enrollment_mk' => $mkBaru->getKey(),
        ])->assertRedirect(route('pengajaran.show', $item->getKey()))->assertSessionHas('sukses');

        $item->refresh();
        $this->assertSame($rombelBaru->getKey(), $item->id_enrollment_tingkat_kelas);
        $this->assertSame($mkBaru->getKey(), $item->id_enrollment_mk);
        $this->assertSame($saya->getKey(), $item->id_dosen);
    }

    public function test_saving_edit_without_changes_is_not_rejected_as_duplicate(): void
    {
        $saya = $this->dosen();
        $rombel = $this->rombel();
        $mk = $this->matkul();
        $item = $this->ampu($saya, $rombel, $mk);

        $this->actingAs($saya->user)->put(route('pengajaran.update', $item->getKey()), [
            'id_enrollment_tingkat_kelas' => $rombel->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
        ])->assertSessionHasNoErrors()->assertRedirect(route('pengajaran.show', $item->getKey()));
    }

    public function test_lecturer_cannot_update_another_lecturers_teaching(): void
    {
        $saya = $this->dosen();
        $lain = $this->dosen('Dosen Lain');
        $rombel = $this->rombel();
        $mk = $this->matkul();
        $orangLain = $this->ampu($lain, $rombel, $mk);

        $this->actingAs($saya->user)->put(route('pengajaran.update', $orangLain->getKey()), [
            'id_enrollment_tingkat_kelas' => $this->rombel(3, 'C')->getKey(),
            'id_enrollment_mk' => $mk->getKey(),
        ])->assertNotFound();

        $this->assertSame($rombel->getKey(), $orangLain->fresh()->id_enrollment_tingkat_kelas);
    }
}