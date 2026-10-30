<?php

namespace Tests\Feature;

use Tests\TestCase;

class PengajuanFormTest extends TestCase
{
    public function test_form_offers_requested_categories_and_link_document_types(): void
    {
        $response = $this->get(route('pengajuan.create'));

        $response->assertOk()
            ->assertSee('Pengabdian')
            ->assertSee('Dokumentasi')
            ->assertSee('Surat Mitra')
            ->assertSee('Surat Mahasiswa')
            ->assertSee('Surat Pengabdian')
            ->assertSee('Tambah dokumen')
            ->assertDontSee('KKL')
            ->assertDontSee('Kunjungan Industri')
            ->assertDontSee('Lainnya')
            ->assertDontSee('Pilih berkas')
            ->assertDontSee('Preview surat');
    }

    public function test_each_category_accepts_link_documents(): void
    {
        $categories = [
            ['category' => 'Lomba', 'title' => 'Lomba Kampus', 'participation' => 'Individu', 'level' => 'Kabupaten', 'organizer' => 'Panitia Kampus'],
            ['category' => 'Volunteer', 'title' => 'Relawan Mengajar', 'organizer' => 'Komunitas', 'role' => 'Relawan'],
            ['category' => 'Magang', 'organizer' => 'Perusahaan', 'role' => 'Developer', 'duration_months' => 3],
            ['category' => 'Pengabdian', 'title' => 'Pengabdian Masyarakat', 'organizer' => 'Program Studi'],
        ];

        foreach ($categories as $index => $category) {
            $response = $this->post(route('pengajuan.store'), array_merge([
                'location' => 'Semarang',
                'start_date' => '2026-10-30',
                'end_date' => '2026-10-31',
                'documents' => [[
                    'name' => 'Dokumen pendukung',
                    'type' => 'Surat Mitra',
                    'url' => 'https://example.com/dokumen',
                ]],
            ], $category));

            $response->assertRedirect(route('pengajuan.show', 101 + $index));
            $this->get(route('pengajuan.show', 101 + $index))
                ->assertOk()
                ->assertSee('Dokumen pendukung')
                ->assertSee('Surat Mitra')
                ->assertSee('https://example.com/dokumen');
        }
    }

    public function test_documents_are_optional_and_have_no_count_limit(): void
    {
        $submission = [
            'category' => 'Pengabdian',
            'title' => 'Pengabdian Kegiatan',
            'organizer' => 'Program Studi',
            'location' => 'Semarang',
            'start_date' => '2026-10-30',
            'end_date' => '2026-10-31',
        ];

        $this->post(route('pengajuan.store'), $submission)
            ->assertRedirect(route('pengajuan.show', 101));
        $this->get(route('pengajuan.show', 101))
            ->assertOk()
            ->assertSee('Tidak ada dokumen pendukung.');

        $submission['documents'] = array_map(fn ($index) => [
            'name' => 'Dokumen '.$index,
            'type' => 'Dokumentasi',
            'url' => 'https://example.com/dokumen-'.$index,
        ], range(1, 7));

        $this->post(route('pengajuan.store'), $submission)
            ->assertRedirect(route('pengajuan.show', 102));
        $this->get(route('pengajuan.show', 102))
            ->assertOk()
            ->assertSee('Dokumen 7')
            ->assertSee('https://example.com/dokumen-7');
    }

    public function test_detail_page_does_not_show_surat_preview(): void
    {
        $this->get(route('pengajuan.show', 1))
            ->assertOk()
            ->assertSee('Hapus pengajuan')
            ->assertDontSee('Preview surat')
            ->assertDontSee('Preview surat pengajuan');
    }

    public function test_pengajuan_can_be_deleted_for_every_status(): void
    {
        $statuses = ['Diajukan', 'Direview', 'Disetujui', 'Selesai', 'Ditolak', 'Perlu revisi'];
        $items = [];

        foreach ($statuses as $index => $status) {
            $items[] = [
                'id' => 101 + $index,
                'title' => 'Pengajuan status '.$status,
                'category' => 'Pengabdian',
                'type' => 'Kegiatan Pengabdian',
                'status' => $status,
                'submitted_at' => '2026-10-30T09:00:00+07:00',
                'data' => [],
                'documents' => [],
            ];
        }

        $this->withSession(['pengajuan.items' => $items]);

        foreach ($statuses as $index => $status) {
            $id = 101 + $index;
            $this->get(route('pengajuan.show', $id))
                ->assertOk()
                ->assertSee('Pengajuan status '.$status)
                ->assertSee('Hapus pengajuan');

            $this->delete(route('pengajuan.destroy', $id))
                ->assertRedirect(route('pengajuan.index'))
                ->assertSessionHas('success', 'Pengajuan berhasil dihapus.');

            $this->get(route('pengajuan.show', $id))->assertNotFound();
        }

        $this->get(route('pengajuan.index'))
            ->assertOk()
            ->assertDontSee('Pengajuan status Diajukan');

        $this->withSession(['success' => 'Pengajuan berhasil dihapus.'])
            ->get(route('pengajuan.index'))
            ->assertOk()
            ->assertSee('Pengajuan berhasil dihapus.');
    }

    public function test_existing_dosen_and_mahasiswa_pages_remain_available(): void
    {
        $this->get(route('pengajuan.dosen'))
            ->assertOk()
            ->assertViewIs('dosen.pengajuan.dosen')
            ->assertSee('<title>Pengajuan Dosen | DOSEN</title>', false)
            ->assertSee('pengajuanDosen')
            ->assertSee('Penyuluhan Literasi Digital')
            ->assertSee('Seminar Nasional AI')
            ->assertSee('/pengajuan/dosen/${k.id}')
            ->assertDontSee('Seminar Nasional Transformasi Digital Kampus')
            ->assertDontSee('Tambah pengajuan')
            ->assertDontSee('Halaman sementara');
        $this->get(route('pengajuan.mahasiswa'))->assertOk();
        $this->get(route('pengajuan-segera'))->assertOk();
    }

    public function test_dashboard_renders_with_develop_pengajuan_routes(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kegiatan Menunggu Verifikasi');
    }

    public function test_berita_pages_and_draft_workflow_are_available(): void
    {
        $this->get(route('pengajuan.berita.index'))
            ->assertOk()
            ->assertSee('Ajukan Berita')
            ->assertSee('Kegiatan Mahasiswa');
        $this->get(route('pengajuan.berita.create'))
            ->assertOk()
            ->assertSee('Dari kegiatan sebelumnya')
            ->assertSee('Tanpa kegiatan sebelumnya')
            ->assertSee('Kategori berita')
            ->assertSee('Foto sampul berita')
            ->assertSee('Simpan Draft');

        $this->post(route('pengajuan.berita.store'), ['intent' => 'draft'])
            ->assertRedirect(route('pengajuan.berita.show', 1));
        $this->get(route('pengajuan.berita.show', 1))
            ->assertOk()
            ->assertSee('Draft');
    }

    public function test_berita_form_lists_existing_and_new_pengajuan_activities(): void
    {
        $this->get(route('pengajuan.berita.create'))
            ->assertOk()
            ->assertSee('Pengajuan Lomba KMPIN VII 2026');

        $this->post(route('pengajuan.store'), [
            'category' => 'Pengabdian',
            'title' => 'Pengabdian Desa Digital',
            'organizer' => 'Program Studi',
            'location' => 'Semarang',
            'start_date' => '2026-10-30',
            'end_date' => '2026-10-31',
        ])->assertRedirect(route('pengajuan.show', 101));

        $this->get(route('pengajuan.berita.create'))
            ->assertOk()
            ->assertSee('Pengajuan Lomba KMPIN VII 2026')
            ->assertSee('Pengabdian Desa Digital')
            ->assertDontSee('Lomba UI/UX Design Festival Mahasiswa 2026');
    }

    public function test_berita_submission_requires_complete_fields_and_cover_photo(): void
    {
        $this->from(route('pengajuan.berita.create'))
            ->post(route('pengajuan.berita.store'), [
                'intent' => 'submit',
                'source_type' => 'new',
                'activity_name' => 'Seminar Nasional',
                'category' => 'Info Akademik',
                'title' => 'Seminar Nasional TRK',
                'content' => 'Informasi kegiatan seminar.',
            ])
            ->assertSessionHasErrors('cover');
    }
}
