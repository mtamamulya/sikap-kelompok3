<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PengajuanFormTest extends TestCase
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
            'email' => 'pengajuan-student@test.local',
            'password' => bcrypt('password'),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs(User::findOrFail($id));
    }

    public function test_new_forms_do_not_prefill_team_members(): void
    {
        foreach ([route('pengajuan.create'), route('pengajuan.dosen.create')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertDontSee('Ivan Sigit Santoso')
                ->assertDontSee('Anisa Aulia Nurusyifa');
        }
    }

    public function test_form_offers_requested_categories_and_link_document_types(): void
    {
        $response = $this->get(route('pengajuan.create'));

        $response->assertOk()
            ->assertViewIs('mahasiswa.pengajuan.create')
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

    public function test_lomba_forms_offer_course_instructors_and_reject_invalid_names(): void
    {
        foreach ([route('pengajuan.create'), route('pengajuan.dosen.create')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('Cari nama dosen...')
                ->assertSee('Tambah')
                ->assertSee('Angga Wahyu Wibowo')
                ->assertSee('Kecerdasan Buatan')
                ->assertSee('Kuwat Santoso')
                ->assertSee('Jaringan Komputer')
                ->assertSee('Wiktasari')
                ->assertSee('Sistem Basis Data')
                ->assertSee('Tahan Prahara')
                ->assertSee('Internet of Things')
                ->assertSee('Prayitno')
                ->assertSee('Computer Vision')
                ->assertSee('Arif Setyawan')
                ->assertSee('Sistem Operasi');
        }

        $submission = [
            'category' => 'Lomba',
            'title' => 'Lomba Kampus',
            'participation' => 'Individu',
            'level' => 'Kabupaten',
            'organizer' => 'Panitia Kampus',
            'advisors' => ['Angga Wahyu Wibowo', 'Kuwat Santoso'],
            'location' => 'Semarang',
            'start_date' => '2026-10-30',
            'end_date' => '2026-10-31',
        ];

        $this->post(route('pengajuan.store'), $submission)
            ->assertRedirect(route('pengajuan.show', 101))
            ->assertSessionHas('pengajuan.items.0.data.advisors', ['Angga Wahyu Wibowo', 'Kuwat Santoso'])
            ->assertSessionHas('pengajuan.items.0.data.advisor', 'Angga Wahyu Wibowo');

        $submission['advisors'] = ['Angga Wahu Wibowo'];

        $this->from(route('pengajuan.create'))
            ->post(route('pengajuan.store'), $submission)
            ->assertRedirect(route('pengajuan.create'))
            ->assertSessionHasErrors('advisors.0');
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

    public function test_new_submission_is_also_listed_as_an_activity(): void
    {
        $this->post(route('pengajuan.store'), [
            'category' => 'Pengabdian',
            'title' => 'Pengajuan yang menjadi kegiatan',
            'organizer' => 'Program Studi',
            'location' => 'Semarang',
            'start_date' => '2026-10-30',
            'end_date' => '2026-10-31',
            'documents' => [[
                'name' => 'Sertifikat Kegiatan',
                'type' => 'Dokumentasi',
                'url' => 'https://example.com/sertifikat',
            ]],
        ])->assertRedirect(route('pengajuan.show', 101));

        $this->get(route('kegiatan.index'))
            ->assertOk()
            ->assertViewIs('mahasiswa.kegiatan.index')
            ->assertSee('Pengajuan yang menjadi kegiatan')
            ->assertSee('Buka tautan')
            ->assertDontSee('Unduh Sertifikat')
            ->assertViewHas('allActivities', fn ($activities) => $activities->contains(
                fn ($activity) => $activity['name'] === 'Pengajuan yang menjadi kegiatan'
                    && $activity['documents'][0]['url'] === 'https://example.com/sertifikat'
            ));
    }

    public function test_activity_list_includes_activities_tagged_to_the_current_user(): void
    {
        $this->withSession(['pengajuan.items' => [[
            'id' => 101,
            'title' => 'Lomba yang ditag',
            'category' => 'Lomba',
            'status' => 'Diajukan',
            'submitted_at' => now()->toIso8601String(),
            'data' => ['members' => [['name' => 'Mahasiswa yang ditag', 'role' => 'Anggota']]],
            'documents' => [],
        ]]])
            ->get(route('kegiatan.index'))
            ->assertOk()
            ->assertViewHas('allActivities', fn ($activities) => $activities->contains(
                fn ($activity) => $activity['name'] === 'Lomba yang ditag'
                    && $activity['members'][0]['name'] === 'Mahasiswa yang ditag'
            ));
    }

    public function test_detail_page_does_not_show_surat_preview(): void
    {
        $this->get(route('pengajuan.show', 1))
            ->assertOk()
            ->assertViewIs('mahasiswa.pengajuan.show')
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
            ->assertViewIs('mahasiswa.pengajuan.index')
            ->assertDontSee('Pengajuan status Diajukan');

        $this->withSession(['success' => 'Pengajuan berhasil dihapus.'])
            ->get(route('pengajuan.index'))
            ->assertOk()
            ->assertViewIs('mahasiswa.pengajuan.index')
            ->assertSee('Pengajuan berhasil dihapus.');
    }

    public function test_existing_dosen_and_mahasiswa_pages_remain_available(): void
    {
        // $this->withoutExceptionHandling();
        $this->get(route('pengajuan.dosen'))
            ->assertOk()
            ->assertViewIs('dosen.pengajuan.dosen')
            ->assertSee('<title>Pengajuan Dosen | DOSEN</title>', false)
            ->assertSee('Penyuluhan Literasi Digital')
            ->assertSee('Seminar Nasional AI')
            ->assertSee('Cari kegiatan atau nama dosen...')
            ->assertSee('/pengajuan/dosen')
            ->assertSee('${k.id}', false)
            ->assertDontSee('Seminar Nasional Transformasi Digital Kampus')
            ->assertDontSee('Tambah pengajuan')
            ->assertDontSee('Halaman sementara');
        // $this->get(route('pengajuan.mahasiswa'))
        //     ->assertOk()
        //     ->assertViewIs('dosen.pengajuan.mahasiswa')
        //     ->assertSee('Volunteer Bakti Sosial');
        $this->get('/mahasiswa/pengajuan-segera')->assertNotFound();
        $this->get('/pengajuan-segera')->assertNotFound();
    }

    public function test_dashboard_renders_with_develop_pengajuan_routes(): void
    {
        $latestSubmission = [
            'id' => 999,
            'title' => 'Pengabdian Uji Dashboard Terbaru',
            'category' => 'Pengabdian',
            'type' => 'Kegiatan Pengabdian',
            'status' => 'Diajukan',
            'submitted_at' => now()->toIso8601String(),
            'data' => [],
            'documents' => [],
        ];

        $response = $this->withSession(['pengajuan.items' => [$latestSubmission]])
            ->get(route('pengajuan.dashboard'));

        $response
            ->assertOk()
            ->assertViewIs('mahasiswa.pengajuan.dashboard')
            ->assertSee('TOTAL PENGAJUAN')
            ->assertSee('id="desktop-mahasiswa-submenu"', false)
            ->assertSee('id="mobile-pengajuan-nav"', false)
            ->assertSee('ti-calendar-event')
            ->assertSee('ti-news')
            ->assertSee('x-data="{ open: true }"', false)
            ->assertSee(config('mahasiswa.nama'))
            ->assertSee(config('mahasiswa.nim'))
            ->assertSee(now()->timezone(config('app.timezone'))->translatedFormat('d F Y, H:i'))
            ->assertDontSee('12 Maret 2026')
            ->assertDontSee('Tambah Kegiatan')
            ->assertSee('Pilih kategori kegiatan')
            ->assertSee('Lengkapi data kegiatan')
            ->assertSee('Tambahkan tautan dokumen')
            ->assertSee('Periksa dan kirim pengajuan')
            ->assertSee('Pengabdian Uji Dashboard Terbaru')
            ->assertSee('Diajukan')
            ->assertSee('href="'.route('pengajuan.show', $latestSubmission['id']).'"', false)
            ->assertDontSee('proposal penelitian internal');
        $this->assertLessThan(
            strpos($response->getContent(), 'Seminar Nasional Transformasi Digital Kampus'),
            strpos($response->getContent(), 'Pengabdian Uji Dashboard Terbaru')
        );
    }

    public function test_teammate_mahasiswa_pages_are_available(): void
    {
        $mahasiswaResponse = $this->get(route('mahasiswa.dashboard'));
        $mahasiswaResponse
            ->assertOk()
            ->assertViewIs('mahasiswa.dashboard.dashboard')
            ->assertSee(config('mahasiswa.nama'))
            ->assertSee(config('mahasiswa.nim'))
            ->assertSee('Buat pengajuan dokumen baru')
            ->assertSee('href="'.route('pengajuan.index').'"', false)
            ->assertDontSee('Unduh KHS')
            ->assertSee('href="'.route('mahasiswa.dashboard').'"', false)
            ->assertSee('href="'.route('mahasiswa.index').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('aria-label="Buka atau tutup submenu Mahasiswa"', false)
            ->assertSee('Mahasiswa')
            ->assertSee('cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js', false)
            ->assertSee('cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css', false)
            ->assertSee('Menu navigasi')
            ->assertSee('bg-white text-[#1f66e5]', false)
            ->assertSee('id="mobile-mahasiswa-submenu"', false)
            ->assertSee('href="'.route('mahasiswa.mata-kuliah').'"', false)
            ->assertSee('href="'.route('mahasiswa.prestasi').'"', false);
        $this->assertSame(1, preg_match('/<div id="mobile-mahasiswa-submenu"[^>]*>(.*?)<\/div>/s', $mahasiswaResponse->getContent(), $mobileMahasiswaSubmenu));
        $this->assertStringNotContainsString('Dashboard', $mobileMahasiswaSubmenu[1]);
        $this->assertSame(2, substr_count($mobileMahasiswaSubmenu[1], '<a '));
        foreach ([
            ['route' => 'mahasiswa.mata-kuliah', 'icon' => 'ti-book-2'],
            ['route' => 'mahasiswa.prestasi', 'icon' => 'ti-trophy'],
        ] as $submenuItem) {
            $submenuResponse = $this->get(route($submenuItem['route']));
            $submenuResponse
                ->assertOk()
                ->assertSee($submenuItem['icon'])
                ->assertSee(config('mahasiswa.nama'))
                ->assertSee('aria-current="page"', false);
            preg_match_all(
                '/<a\b(?=[^>]*href="'.preg_quote(route('mahasiswa.index'), '/').'")[^>]*class="([^"]+)"/',
                $submenuResponse->getContent(),
                $mahasiswaLinkClasses
            );
            $this->assertCount(2, $mahasiswaLinkClasses[1]);
            foreach ($mahasiswaLinkClasses[1] as $class) {
                $this->assertDoesNotMatchRegularExpression('/(?:^|\s)bg-white(?:\s|$)/', $class);
            }
        }
        $this->get(route('mahasiswa.index'))
            ->assertOk()
            ->assertViewIs('mahasiswa.mahasiswa.dashboard-mhs');
        $this->get(route('mahasiswa.mata-kuliah'))
            ->assertOk()
            ->assertViewIs('mahasiswa.mahasiswa.mata-kuliah');
        $this->get(route('mahasiswa.mata-kuliah', ['tab' => 'berjalan']))->assertOk();
        $this->get(route('mahasiswa.mata-kuliah', ['tab' => 'riwayat']))->assertOk();
        $this->get(route('mahasiswa.prestasi'))
            ->assertOk()
            ->assertViewIs('mahasiswa.mahasiswa.prestasi')
            ->assertSee('md:hidden', false)
            ->assertDontSee('Catat prestasi Anda dengan bukti yang lengkap');
        $this->get(route('mahasiswa.prestasi.tambah'))
            ->assertOk()
            ->assertViewIs('mahasiswa.mahasiswa.prestasi-tambah');
        $kegiatanResponse = $this->get(route('kegiatan.index'));
        $kegiatanResponse
            ->assertOk()
            ->assertSee('href="'.route('kegiatan.index').'"', false)
            ->assertSee('aria-current="page"', false);
        $this->assertDoesNotMatchRegularExpression(
            '/<a\b[^>]*href="'.preg_quote(route('mahasiswa.index'), '/').'"[^>]*aria-current="page"/s',
            $kegiatanResponse->getContent()
        );
    }

    public function test_berita_pages_and_draft_workflow_are_available(): void
    {
        $this->get(route('pengajuan.berita.index'))
            ->assertOk()
            ->assertViewIs('mahasiswa.pengajuan.berita.index')
            ->assertSee('Ajukan Berita')
            ->assertSee('Kegiatan Mahasiswa');
        $this->get(route('pengajuan.berita.create'))
            ->assertOk()
            ->assertViewIs('mahasiswa.pengajuan.berita.form')
            ->assertSee('Dari kegiatan sebelumnya')
            ->assertSee('Tanpa kegiatan sebelumnya')
            ->assertSee('Kategori berita')
            ->assertSee('Foto sampul berita')
            ->assertSee('Simpan Draft');

        $this->post(route('pengajuan.berita.store'), ['intent' => 'draft'])
            ->assertRedirect(route('pengajuan.berita.show', 1));
        $this->get(route('pengajuan.berita.show', 1))
            ->assertOk()
            ->assertViewIs('mahasiswa.pengajuan.berita.show')
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
