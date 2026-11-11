<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MahasiswaPrestasiDriveLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert([
            'id_role' => 4,
            'nama_role' => 'mahasiswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $id = DB::table('users')->insertGetId([
            'id_role' => 4,
            'email' => 'prestasi-student@test.local',
            'password' => bcrypt('password'),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs(User::findOrFail($id));
    }

    private function dataPrestasi(array $dokumen): array
    {
        return [
            'sumber' => 'mandiri',
            'nama_lomba' => 'Kompetisi Inovasi',
            'penyelenggara' => 'Penyelenggara',
            'lokasi' => 'Semarang',
            'tingkat' => 'Nasional',
            'capaian' => 'Juara 1',
            'jenis' => 'Akademik',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-01-02',
            'bentuk' => 'Individu',
            'dokumen' => $dokumen,
        ];
    }

    public function test_prestasi_form_uses_drive_links_instead_of_file_uploads(): void
    {
        $this->get(route('mahasiswa.prestasi.tambah'))
            ->assertOk()
            ->assertSee('Google Drive atau OneDrive')
            ->assertSee('Tambah')
            ->assertSee('id="capaian" name="capaian"', false)
            ->assertDontSee('<select id="capaian"', false)
            ->assertDontSee('type="file"', false);
    }

    public function test_prestasi_accepts_a_custom_capaian(): void
    {
        $this->post(route('mahasiswa.prestasi.simpan'), array_merge(
            $this->dataPrestasi([
                ['url' => 'https://drive.google.com/file/d/example/view', 'jenis' => 'Sertifikat'],
            ]),
            ['capaian' => 'Best Paper']
        ))->assertRedirect(route('mahasiswa.prestasi'))
            ->assertSessionHasNoErrors();
    }

    public function test_prestasi_form_lists_dummy_and_session_submitted_activities(): void
    {
        $this->withSession(['pengajuan.items' => [[
            'id' => 101,
            'title' => 'Pengajuan Lomba dari sesi',
            'category' => 'Lomba',
            'status' => 'Diajukan',
            'submitted_at' => now()->toIso8601String(),
            'data' => ['organizer' => 'Penyelenggara sesi'],
            'documents' => [],
        ]]])
            ->get(route('mahasiswa.prestasi.tambah'))
            ->assertOk()
            ->assertSee('Kompetisi Robotics &amp; Automation • Lomba', false)
            ->assertSee('Pengajuan Lomba dari sesi • Lomba');
    }

    public function test_prestasi_edit_form_keeps_existing_documents_and_new_types(): void
    {
        $this->get(route('mahasiswa.prestasi.edit', 1))
            ->assertOk()
            ->assertSee('Dokumentasi kegiatan')
            ->assertSee('sertifikat-iot.pdf')
            ->assertSee('foto-iot.jpg');
    }

    public function test_eye_action_lists_proof_links_without_image_or_file_previews(): void
    {
        $this->get(route('mahasiswa.prestasi'))
            ->assertOk()
            ->assertSeeInOrder(['Informasi prestasi', 'Tautan bukti prestasi'])
            ->assertSee('max-w-xl', false)
            ->assertSee('Belum ada tautan bukti yang ditambahkan.')
            ->assertDontSee('title="Pratinjau dokumen Google Drive"', false)
            ->assertDontSee('Pratinjau foto kegiatan');
    }

    public function test_prestasi_accepts_a_google_drive_sharing_link(): void
    {
        $this->post(route('mahasiswa.prestasi.simpan'), $this->dataPrestasi([
            ['url' => 'https://drive.google.com/file/d/example/view', 'jenis' => 'Sertifikat'],
        ]))->assertRedirect(route('mahasiswa.prestasi'))
            ->assertSessionHasNoErrors();
    }

    public function test_prestasi_accepts_a_onedrive_sharing_link(): void
    {
        $this->post(route('mahasiswa.prestasi.simpan'), $this->dataPrestasi([
            ['url' => 'https://1drv.ms/u/s!example', 'jenis' => 'Sertifikat'],
        ]))->assertRedirect(route('mahasiswa.prestasi'))
            ->assertSessionHasNoErrors();
    }

    public function test_prestasi_rejects_non_google_drive_links(): void
    {
        $this->from(route('mahasiswa.prestasi.tambah'))
            ->post(route('mahasiswa.prestasi.simpan'), $this->dataPrestasi([
                ['url' => 'https://example.com/certificate.pdf', 'jenis' => 'Sertifikat'],
            ]))->assertRedirect(route('mahasiswa.prestasi.tambah'))
            ->assertSessionHasErrors(['dokumen.0.url']);
    }

    public function test_prestasi_requires_a_document_type_for_each_drive_link(): void
    {
        $this->from(route('mahasiswa.prestasi.tambah'))
            ->post(route('mahasiswa.prestasi.simpan'), $this->dataPrestasi([
                ['url' => 'https://drive.google.com/file/d/example/view', 'jenis' => ''],
            ]))->assertRedirect(route('mahasiswa.prestasi.tambah'))
            ->assertSessionHasErrors(['dokumen.0.jenis']);
    }

    public function test_prestasi_requires_at_least_one_proof_link(): void
    {
        $this->from(route('mahasiswa.prestasi.tambah'))
            ->post(route('mahasiswa.prestasi.simpan'), $this->dataPrestasi([]))
            ->assertRedirect(route('mahasiswa.prestasi.tambah'))
            ->assertSessionHasErrors(['dokumen']);
    }
}
