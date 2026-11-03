<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FrontendLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_existing_student_and_lecturer_pages_render_directly_without_login(): void
    {
        $this->get('/dashboard')->assertOk()->assertViewIs('mahasiswa.dashboard')->assertSee('mahasiswa-shell', false)->assertDontSee('dosen-shell', false);
        $this->get('/kegiatan')->assertOk()->assertSee('mahasiswa-shell', false)->assertDontSee('dosen-shell', false);
        foreach (['/dosen/dashboard', '/dosen/kegiatan', '/dosen/kegiatan/create', '/profil', '/notifikasi', '/pengajuan/dosen', '/pengajuan/mahasiswa', '/pengajuan/dosen/1', '/pengajuan/mahasiswa/1', '/verifikasi/1'] as $path) {
            $this->get($path)->assertOk()->assertSee('class="frontend-shell dosen-shell', false);
        }
        foreach (['penelitian', 'pengabdian', 'pengajaran', 'prestasi'] as $module) {
            foreach (['', '/create', '/1', '/1/edit'] as $suffix) {
                $this->get('/'.$module.$suffix)->assertOk()->assertSee('class="frontend-shell dosen-shell', false);
            }
        }
        $this->assertFalse(Route::has('login'));
        $this->assertFalse(Route::has('logout'));
        $this->get('/login')->assertNotFound();
        foreach (['/admin/dashboard', '/kaprodi/dashboard', '/alur/berita', '/alur/template-surat'] as $path) {
            $this->get($path)->assertNotFound();
        }
    }

    public function test_existing_database_activity_data_and_owner_scope_are_preserved_in_both_layouts(): void
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
        $this->get('/dashboard')->assertOk()->assertSee('Kegiatan Milik Saya')->assertDontSee('Kegiatan Milik Orang Lain');
        $this->get('/dosen/dashboard')->assertOk()->assertSee('Kegiatan Milik Saya')->assertDontSee('Kegiatan Milik Orang Lain')->assertViewHas('stats', fn ($stats) => $stats[1]['value'] === 1 && $stats[3]['value'] === 1);
        $this->get('/dosen/kegiatan')->assertOk()
            ->assertSee('class="frontend-shell dosen-shell', false)
            ->assertDontSee('class="frontend-shell mahasiswa-shell', false)
            ->assertSee('Kegiatan Milik Saya')->assertDontSee('Kegiatan Milik Orang Lain')
            ->assertViewHas('allActivities', fn ($activities) => $activities->every(fn ($activity) => $activity['detail_url'] === route('dosen.kegiatan.show', $activity['id'])));
        $own = DB::table('kegiatan')->where('id_user', $ownerId)->value('id_kegiatan');
        $other = DB::table('kegiatan')->where('id_user', $otherId)->value('id_kegiatan');

        // BARIS BARU:
        $this->get('/dosen/kegiatan/'.$own)->assertOk()->assertSee('Detail Kegiatan');
        $this->get('/dosen/kegiatan/'.$other)->assertNotFound();
        $this->assertDatabaseCount('kegiatan', 2);
    }
}
