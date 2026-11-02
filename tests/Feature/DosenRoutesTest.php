<?php

namespace Tests\Feature;

use Tests\TestCase;

class DosenRoutesTest extends TestCase
{
    public function test_dosen_dashboard_has_its_own_route_and_view(): void
    {
        $this->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertViewIs('dosen.dashboard')
            ->assertSee('href="'.route('dosen.dashboard').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('Kuwat Santoso')
            ->assertSee('Dosen')
            ->assertSee('aria-label="Notifikasi"', false)
            ->assertSee('Penelitian')
            ->assertSee('Pengabdian')
            ->assertDontSee('Arya Bima')
            ->assertDontSee('Mahasiswa</span>', false);
    }

    public function test_dosen_navigation_routes_render_their_dosen_views(): void
    {
        $routes = [
            ['kegiatan.index', 'dosen.kegiatan.index'],
            ['kegiatan.create', 'dosen.kegiatan.create'],
            ['kegiatan.show', 'dosen.kegiatan.show', ['id' => 1]],
            ['kegiatan.edit', 'dosen.kegiatan.edit', ['id' => 1]],
            ['penelitian.index', 'dosen.penelitian.index'],
            ['penelitian.create', 'dosen.penelitian.create'],
            ['penelitian.show', 'dosen.penelitian.show', ['id' => 1]],
            ['penelitian.edit', 'dosen.penelitian.edit', ['id' => 1]],
            ['pengabdian.index', 'dosen.pengabdian.index'],
            ['pengabdian.create', 'dosen.pengabdian.create'],
            ['pengabdian.show', 'dosen.pengabdian.show', ['id' => 1]],
            ['pengabdian.edit', 'dosen.pengabdian.edit', ['id' => 1]],
            ['pengajaran.index', 'dosen.pengajaran.index'],
            ['pengajaran.create', 'dosen.pengajaran.create'],
            ['pengajaran.show', 'dosen.pengajaran.show', ['id' => 1]],
            ['pengajaran.edit', 'dosen.pengajaran.edit', ['id' => 1]],
            ['prestasi.index', 'dosen.prestasi.index'],
            ['prestasi.create', 'dosen.prestasi.create'],
            ['prestasi.show', 'dosen.prestasi.show', ['id' => 1]],
            ['prestasi.edit', 'dosen.prestasi.edit', ['id' => 1]],
            ['berita.index', 'dosen.berita.index'],
            ['berita.create', 'dosen.berita.create'],
            ['berita.show', 'dosen.berita.show', ['id' => 1]],
            ['berita.edit', 'dosen.berita.edit', ['id' => 1]],
            ['pengajuan.dosen.create', 'dosen.pengajuan.create'],
            ['pengajuan.dosen', 'dosen.pengajuan.dosen'],
            ['pengajuan.mahasiswa', 'dosen.pengajuan.mahasiswa'],
            ['pengajuan.mahasiswa.show', 'dosen.pengajuan.show', ['id' => 1]],
            ['notifikasi', 'dosen.notifikasi.index'],
            ['profil', 'dosen.profil.show'],
        ];

        foreach ($routes as $route) {
            [$routeName, $view] = $route;
            $parameters = $route[2] ?? [];

            $this->get(route($routeName, $parameters))
                ->assertOk()
                ->assertViewIs($view);
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
        $this->get(route('kegiatan.index'))
            ->assertOk()
            ->assertSee("Alpine.data('kegiatan'", false);

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
