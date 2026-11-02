<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_dosen_dashboard_has_its_own_route_and_view(): void
    {
        $this->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertViewIs('dosen.dashboard')
            ->assertSee('href="'.route('dosen.dashboard').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('dosen-shell', false)
            ->assertSee('Dosen')
            ->assertSee('Penelitian')
            ->assertSee('Pengabdian')
            ->assertDontSee('Arya Bima')
            ->assertDontSee('Mahasiswa</span>', false);
    }

    public function test_dosen_navigation_routes_render_their_dosen_views(): void
    {
        $routes = [
            ['dosen.kegiatan.index'],
            ['dosen.kegiatan.create'],
            ['dosen.kegiatan.edit', ['id' => 1]],
            ['penelitian.index'],
            ['penelitian.create'],
            ['penelitian.show', ['id' => 1]],
            ['penelitian.edit', ['id' => 1]],
            ['pengabdian.index'],
            ['pengabdian.create'],
            ['pengabdian.show', ['id' => 1]],
            ['pengabdian.edit', ['id' => 1]],
            ['pengajaran.index'],
            ['pengajaran.create'],
            ['pengajaran.show', ['id' => 1]],
            ['pengajaran.edit', ['id' => 1]],
            ['prestasi.index'],
            ['prestasi.create'],
            ['prestasi.show', ['id' => 1]],
            ['prestasi.edit', ['id' => 1]],
            ['berita.index'],
            ['berita.create'],
            ['berita.show', ['id' => 1]],
            ['berita.edit', ['id' => 1]],
            ['pengajuan.dosen.create'],
            ['pengajuan.dosen'],
            ['pengajuan.mahasiswa'],
            ['pengajuan.mahasiswa.show', ['id' => 1]],
            ['notifikasi'],
            ['profil'],
        ];

        foreach ($routes as $route) {
            $routeName = $route[0];
            $parameters = $route[1] ?? [];

            $this->get(route($routeName, $parameters))
                ->assertOk()
                ->assertSee('dosen-shell', false);
        }
    }

    public function test_dosen_berita_list_keeps_detail_and_edit_links_in_dosen_routes(): void
    {
        $this->get(route('berita.index'))
            ->assertOk()
            ->assertSee(url('/berita').'/${b.id}', false)
            ->assertDontSee(url('/pengajuan/berita'), false);
    }

    public function test_dosen_pengajuan_form_uses_dosen_store_route(): void
    {
        $this->get(route('pengajuan.dosen.create'))
            ->assertOk()
            ->assertSee('action="'.route('pengajuan.dosen.store').'"', false);

        $this->post(route('pengajuan.dosen.store'), [
            'category' => 'Lomba',
            'title' => 'Lomba Dosen',
            'participation' => 'Individu',
            'level' => 'Nasional',
            'organizer' => 'Politeknik Negeri Semarang',
            'location' => 'Semarang',
            'start_date' => '2026-10-30',
            'end_date' => '2026-10-31',
        ])->assertRedirect(route('pengajuan.dosen.show', 101));
    }

    public function test_dosen_views_load_the_scripts_for_their_interactive_lists(): void
    {
        $this->get(route('berita.index'))
            ->assertOk()
            ->assertSee("Alpine.data('beritaDaftar'", false);
    }

    public function test_dosen_pengajuan_pages_render_their_list_data(): void
    {
        $this->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('pengajuan.dosen').'"', false);

        $this->get(route('pengajuan.dosen'))
            ->assertOk()
            ->assertViewIs('dosen.pengajuan.dosen')
            ->assertSee('<title>Pengajuan Dosen | DOSEN</title>', false)
            ->assertSee('Daftar kegiatan yang diajukan oleh dosen.')
            ->assertSee('Penyuluhan Literasi Digital')
            ->assertSee('Seminar Nasional AI');

        $this->get(route('pengajuan.mahasiswa'))
            ->assertOk()
            ->assertViewIs('dosen.pengajuan.mahasiswa')
            ->assertSee("Alpine.data('pengajuanMahasiswa'", false);
    }
}
