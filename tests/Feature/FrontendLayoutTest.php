<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FrontendLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function studentUser(): User
    {
        DB::table('roles')->insert([
            'id_role' => 4,
            'nama_role' => 'mahasiswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $id = DB::table('users')->insertGetId([
            'id_role' => 4,
            'email' => 'student-layout@test.local',
            'password' => bcrypt('password'),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_existing_student_and_lecturer_pages_render_directly_without_login(): void
    {
        $this->actingAs($this->studentUser());

        $this->get('/mahasiswa/dashboard')
            ->assertOk()
            ->assertViewIs('mahasiswa.dashboard.dashboard')
            ->assertSee('fixed inset-y-0 left-0 z-30 hidden w-60', false)
            ->assertSee('lg:ml-60', false)
            ->assertSee('class="frontend-main min-w-0 px-3 py-4', false)
            ->assertSee('bg-[#2277FF]" aria-hidden="true"', false)
            ->assertSee('active:-translate-y-1 active:shadow-lg', false)
            ->assertSee('bottom-4 left-4 top-4 z-0 w-[3px] bg-[#93C5FD]', false)
            ->assertSee('href="'.route('mahasiswa.dashboard').'"', false)
            ->assertDontSee('href="'.route('mahasiswa.index').'" aria-current="page"', false)
            ->assertSee('aria-current="page"', false)
            ->assertDontSee('dosen-shell', false);
        $this->get('/mahasiswa/kegiatan')
            ->assertOk()
            ->assertViewIs('mahasiswa.kegiatan.index')
            ->assertSee('class="frontend-main min-w-0 px-3 py-4', false)
            ->assertSee('FKBSE 2025')
            ->assertSee('href="'.route('kegiatan.index').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertDontSee('dosen-shell', false);
        $this->get('/dashboard')->assertRedirect('/mahasiswa/dashboard');
        $this->get('/kegiatan')->assertRedirect('/mahasiswa/kegiatan');
        $this->get('/pengajuan')->assertRedirect('/mahasiswa/pengajuan');
        $this->get('/mahasiswa')->assertRedirect('/mahasiswa/mahasiswa/dashboard');
        $this->get('/mahasiswa/mahasiswa/dashboard')
            ->assertOk()
            ->assertSee('Mahasiswa')
            ->assertSee('grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4', false);
        $this->get('/mahasiswa/pengajuan')
            ->assertOk()
            ->assertSee('fixed inset-y-0 left-0 z-30 hidden w-60', false)
            ->assertSee('lg:ml-60', false);
        $this->get('/mahasiswa/akademik')->assertRedirect('/mahasiswa/mahasiswa/dashboard');
        foreach (['/dosen/dashboard', '/dosen/kegiatan', '/dosen/kegiatan/create', '/profil', '/notifikasi', '/pengajuan/dosen', '/pengajuan/mahasiswa', '/pengajuan/dosen/1', '/pengajuan/mahasiswa/1', '/verifikasi/1'] as $path) {
            $this->get($path)->assertOk()->assertSee('class="frontend-shell dosen-shell', false);
        }
        foreach (['penelitian', 'pengabdian', 'prestasi'] as $module) {
            foreach (['', '/create', '/1', '/1/edit'] as $suffix) {
                $this->get('/'.$module.$suffix)->assertOk()->assertSee('class="frontend-shell dosen-shell', false);
            }
        }
        // Pengajaran sudah memakai database: daftar & form bisa dibuka, detail/edit dicek di PengajaranTest.
        foreach (['/pengajaran', '/pengajaran/create'] as $path) {
            $this->get($path)->assertOk()->assertSee('class="frontend-shell dosen-shell', false);
        }
    }

    public function test_local_root_opens_public_program_landing_and_service_cards_open_login(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewIs('home')
            ->assertSee('Teknologi Rekayasa Komputer')
            ->assertSee('href="'.route('login').'"', false)
            ->assertSee('Mahasiswa')
            ->assertSee('Dosen');
    }

    public function test_lecturer_pages_keep_their_owner_scope_and_students_cannot_access_them(): void
    {
        $role = DB::table('roles')->insertGetId(['nama_role' => 'dosen'], 'id_role');
        $kind = DB::table('jenis_kegiatan')->insertGetId(['nama_jenis_kegiatan' => 'Kegiatan Dosen'], 'id_jenis_kegiatan');
        $category = DB::table('kategori')->insertGetId(['nama_kategori' => 'Penelitian', 'id_jenis_kegiatan' => $kind], 'id_kategori');
        $ownerId = DB::table('users')->insertGetId(['id_role' => $role, 'email' => 'owner@test.local', 'password' => 'test', 'status' => true], 'id_user');
        $otherId = DB::table('users')->insertGetId(['id_role' => $role, 'email' => 'other@test.local', 'password' => 'test', 'status' => true], 'id_user');
        foreach ([$ownerId => 'Kegiatan Milik Saya', $otherId => 'Kegiatan Milik Orang Lain'] as $user => $title) {
            $id = DB::table('kegiatan')->insertGetId(['id_kategori' => $category, 'id_user' => $user, 'nama_kegiatan' => $title, 'tanggal_mulai' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()], 'id_kegiatan');
            DB::table('timeline')->insert(['id_kegiatan' => $id, 'status' => 'diajukan', 'tanggal_status' => now()]);
        }

        $this->actingAs(User::findOrFail($ownerId));

        $this->get('/mahasiswa/dashboard')->assertForbidden();
        $this->get('/mahasiswa/kegiatan')->assertForbidden();
        $this->get('/dosen/dashboard')->assertOk()->assertSee('Kegiatan Milik Saya')->assertDontSee('Kegiatan Milik Orang Lain')->assertViewHas('stats', fn ($stats) => $stats[1]['value'] === 1 && $stats[3]['value'] === 1);
        $this->get('/dosen/kegiatan')->assertOk()
            ->assertOk()
            ->assertSee('class="frontend-shell dosen-shell', false)
            ->assertDontSee('class="frontend-shell mahasiswa-shell', false)
            // ->assertSee('Kegiatan Milik Saya')->assertDontSee('Kegiatan Milik Orang Lain')
            ->assertViewHas('allActivities', fn ($activities) => $activities->every(fn ($activity) => $activity['detail_url'] === route('dosen.kegiatan.show', $activity['id'])));

        $own = DB::table('kegiatan')->where('id_user', $ownerId)->value('id_kegiatan');
        $other = DB::table('kegiatan')->where('id_user', $otherId)->value('id_kegiatan');

        $this->get('/dosen/kegiatan/'.$own)->assertOk()->assertSee('Detail Kegiatan');
        $this->get('/dosen/kegiatan/'.$other)->assertNotFound();
        $this->assertDatabaseCount('kegiatan', 2);
    }
}
