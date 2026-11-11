<?php

namespace Tests\Feature;

use App\Models\JenisDokumen;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KaprodiJenisDokumenTest extends TestCase
{
    use DatabaseMigrations;

    private function masukSebagaiKaprodi(): User
    {
        DB::table('roles')->insertOrIgnore(['id_role' => 2, 'nama_role' => 'kaprodi']);

        return User::firstOrCreate(
            ['email' => 'kaprodi@trk.ac.id'],
            ['id_role' => 2, 'password' => 'password', 'status' => true]
        );
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('kaprodi.kegiatan.jenis-dokumen'));

        $response->assertRedirect(route('login'));
    }

    public function test_kaprodi_can_view_seeded_document_types_from_database(): void
    {
        $this->seed(JenisDokumenSeeder::class);

        $response = $this->actingAs($this->masukSebagaiKaprodi())
            ->withSession(['kaprodi' => ['nama' => 'Kaprodi']])
            ->get(route('kaprodi.kegiatan.jenis-dokumen'));

        $response->assertOk();
        $response->assertViewIs('kaprodi.jenis-dokumen');
        $response->assertViewHas('rows');

        $rows = $response->viewData('rows');
        $this->assertCount(5, $rows);
        $this->assertSame('Sertifikat', $rows[0]['name']);
        $this->assertSame('Sertifikat keikutsertaan atau penghargaan kegiatan', $rows[0]['description']);
    }

    public function test_kaprodi_view_reflects_custom_database_document_type(): void
    {
        JenisDokumen::create([
            'nama_jenis_dokumen' => 'Surat Rekomendasi Lomba',
            'keterangan' => 'Surat rekomendasi resmi dari pimpinan jurusan',
        ]);

        $response = $this->actingAs($this->masukSebagaiKaprodi())
            ->withSession(['kaprodi' => ['nama' => 'Kaprodi']])
            ->get(route('kaprodi.kegiatan.jenis-dokumen'));

        $response->assertOk();
        $rows = $response->viewData('rows');

        $this->assertCount(1, $rows);
        $this->assertSame('Surat Rekomendasi Lomba', $rows[0]['name']);
        $this->assertSame('Surat rekomendasi resmi dari pimpinan jurusan', $rows[0]['description']);
    }
}
