<?php

namespace Tests\Feature;

use App\Models\JenisKegiatan;
use App\Models\Kategori;
use Database\Seeders\FormIsianKategoriSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LatestErdDatabaseTest extends TestCase
{
    use DatabaseMigrations;

    public function test_kind_upgrade_and_rollback_preserve_category_references(): void
    {
        $migration = require database_path('migrations/2026_11_09_000001_align_jenis_kegiatan_with_latest_erd.php');
        $migration->down();
        $kind = JenisKegiatan::create(['nama_jenis_kegiatan' => 'Kegiatan Prodi & Mahasiswa']);
        $category = Kategori::create(['id_jenis_kegiatan' => $kind->getKey(), 'nama_kategori' => 'Kegiatan bersama']);

        $migration->up();
        $this->assertSame('Kegiatan Dosen & Mahasiswa', $kind->fresh()->nama_jenis_kegiatan);
        $this->assertSame($kind->getKey(), $category->fresh()->id_jenis_kegiatan);

        $migration->down();
        $this->assertSame('Kegiatan Prodi & Mahasiswa', $kind->fresh()->nama_jenis_kegiatan);
        $this->assertSame($kind->getKey(), $category->fresh()->id_jenis_kegiatan);
        $migration->up();
    }

    public function test_word_definitions_are_seeded_without_answers_or_overwriting_customizations(): void
    {
        $this->seed(FormIsianKategoriSeeder::class);
        $this->assertDatabaseCount('jenis_kegiatan', 4);
        $this->assertDatabaseCount('kategori', 3);
        $this->assertDatabaseCount('isian_kategori', 6);
        $this->assertDatabaseCount('kegiatan', 0);
        $this->assertDatabaseCount('detail_kegiatan', 0);

        $volunteer = Kategori::where('nama_kategori', 'Volunteer')->firstOrFail();
        $this->assertDatabaseHas('isian_kategori', [
            'id_kategori' => $volunteer->getKey(), 'input' => 'Nama Program Relawan',
            'aturan' => 'NOT NULL', 'jumlah_isian' => 1,
        ]);
        $magang = Kategori::where('nama_kategori', 'Magang')->firstOrFail();
        $this->assertDatabaseHas('isian_kategori', [
            'id_kategori' => $magang->getKey(), 'input' => 'Posisi / Role Magang', 'aturan' => 'NOT NULL',
        ]);
        $documentation = $magang->isianKategori()->where('input', 'dokumentasi')->firstOrFail();
        $this->assertSame('berkas', $documentation->jenis_input);
        $this->assertNull($documentation->jumlah_isian);
        $this->assertCount(0, Kategori::where('nama_kategori', 'SES')->firstOrFail()->isianKategori);

        $documentation->update(['jumlah_isian' => 5]);
        $before = DB::table('isian_kategori')->orderBy('id_isian_kategori')->get()->toJson();
        $this->seed(FormIsianKategoriSeeder::class);
        $this->assertSame($before, DB::table('isian_kategori')->orderBy('id_isian_kategori')->get()->toJson());
        $this->assertDatabaseCount('kategori', 3);
        $this->assertDatabaseCount('jenis_kegiatan', 4);
    }
}
