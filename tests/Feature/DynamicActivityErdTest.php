<?php

namespace Tests\Feature;

use App\Models\Dokumen;
use App\Models\IsianKategori;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DynamicActivityErdTest extends TestCase
{
    use RefreshDatabase;

    private function activity(string $name = 'Penelitian'): Kegiatan
    {
        $role = DB::table('roles')->insertGetId(['nama_role' => 'role-'.uniqid()], 'id_role');
        $user = User::create(['id_role' => $role, 'email' => uniqid().'@test.local', 'password' => 'password']);
        $kind = DB::table('jenis_kegiatan')->value('id_jenis_kegiatan')
            ?? DB::table('jenis_kegiatan')->insertGetId(['nama_jenis_kegiatan' => 'Kegiatan Dosen'], 'id_jenis_kegiatan');
        $category = Kategori::create(['id_jenis_kegiatan' => $kind, 'nama_kategori' => $name]);

        return Kegiatan::create(['id_kategori' => $category->getKey(), 'id_user' => $user->getKey(), 'nama_kegiatan' => 'Kegiatan '.$name]);
    }

    private function field(Kegiatan $activity, string $name, ?int $limit = 1): IsianKategori
    {
        return $activity->kategori->isianKategori()->create([
            'input' => $name, 'jenis_input' => 'teks', 'aturan' => 'NULL', 'jumlah_isian' => $limit,
        ]);
    }

    public function test_one_activity_has_multiple_typed_answers_and_repeated_values(): void
    {
        $activity = $this->activity();
        $skim = $this->field($activity, 'skim');
        $mitra = $this->field($activity, 'mitra', null);
        $activity->detail()->create(['id_isian_kategori' => $skim->getKey(), 'isian' => 'Penelitian Terapan']);
        $activity->detail()->create(['id_isian_kategori' => $mitra->getKey(), 'isian' => 'Desa A']);
        $activity->detail()->create(['id_isian_kategori' => $mitra->getKey(), 'isian' => 'Desa B']);

        $this->assertCount(3, $activity->fresh()->detail);
        $this->assertSame('Penelitian Terapan', $activity->fresh()->nilaiIsian('skim'));
        $this->assertSame($activity->getKey(), $skim->detailKegiatan->first()->kegiatan->getKey());
        $this->assertSame($activity->id_kategori, $skim->kategori->getKey());
        $this->assertSame($activity->getKey(), $activity->user->kegiatan->first()->getKey());
        $this->assertFalse(Schema::hasTable('skim'));
        $this->assertFalse(Schema::hasTable('kelompok_bidang'));
        $this->assertFalse(Schema::hasTable('kegiatan_dosen'));
    }

    public function test_an_answer_cannot_use_a_field_from_another_category(): void
    {
        $activity = $this->activity();
        $foreign = $this->field($this->activity('Pengabdian'), 'mitra');
        $this->expectException(ValidationException::class);
        $activity->detail()->create(['id_isian_kategori' => $foreign->getKey(), 'isian' => 'Salah kategori']);
    }

    public function test_single_answer_limit_is_checked_and_existing_answer_can_be_updated(): void
    {
        $activity = $this->activity();
        $field = $this->field($activity, 'mitra');
        $answer = $activity->detail()->create(['id_isian_kategori' => $field->getKey(), 'isian' => 'Desa A']);
        $answer->update(['isian' => 'Desa B']);
        $this->assertSame('Desa B', $answer->fresh()->isian);
        $this->expectException(ValidationException::class);
        $activity->detail()->create(['id_isian_kategori' => $field->getKey(), 'isian' => 'Duplikat']);
    }

    public function test_invalid_numeric_answer_is_rejected(): void
    {
        $activity = $this->activity();
        $field = $this->field($activity, 'dana');
        $field->update(['jenis_input' => 'angka']);
        $this->expectException(ValidationException::class);
        $activity->detail()->create(['id_isian_kategori' => $field->getKey(), 'isian' => 'bukan angka']);
    }

    public function test_documents_and_multiple_achievements_use_the_correct_parents(): void
    {
        $activity = $this->activity();
        $field = $this->field($activity, 'dokumentasi');
        $detail = $activity->detail()->create(['id_isian_kategori' => $field->getKey()]);
        $kind = DB::table('jenis_dokumen')->insertGetId(['nama_jenis_dokumen' => 'Foto'], 'id_jenis_dokumen');
        $document = $detail->dokumen()->create([
            'id_kegiatan' => $activity->getKey(), 'id_jenis_dokumen' => $kind,
            'nama_file' => 'foto.jpg', 'tipe_dokumen' => 'image/jpeg', 'link_dokumen' => 'foto.jpg',
        ]);
        $first = $activity->prestasi()->create(['jenis_prestasi' => 'akademik', 'capaian' => 'Juara 1']);
        $activity->prestasi()->create(['jenis_prestasi' => 'akademik', 'capaian' => 'Best Paper']);
        $proof = $first->buktiPrestasi()->create(['id_jenis_dokumen' => $kind, 'link' => 'sertifikat.pdf', 'nama_file' => 'sertifikat.pdf']);
        $activity->berita()->create(['judul_berita' => 'Berita kegiatan', 'isi_berita' => 'Isi', 'sampul' => 'sampul.jpg']);

        $this->assertSame($detail->getKey(), $document->detailKegiatan->getKey());
        $this->assertSame($activity->getKey(), $document->kegiatan->getKey());
        $this->assertCount(2, $activity->fresh()->prestasi);
        $this->assertSame($first->getKey(), $proof->prestasi->getKey());
        $this->assertSame($kind, $proof->jenisDokumen->getKey());
        $this->assertCount(1, $activity->fresh()->berita);
        $detail->delete();
        $this->assertNull($document->fresh()->id_detail_kegiatan);
        $this->assertSame('foto.jpg', $document->fresh()->nama_file);
    }

    public function test_document_cannot_point_to_an_answer_of_another_activity(): void
    {
        $activity = $this->activity();
        $other = $this->activity();
        $field = $this->field($other, 'dokumen');
        $detail = $other->detail()->create(['id_isian_kategori' => $field->getKey()]);
        $kind = DB::table('jenis_dokumen')->insertGetId(['nama_jenis_dokumen' => 'Foto'], 'id_jenis_dokumen');
        $this->expectException(ValidationException::class);
        Dokumen::create([
            'id_kegiatan' => $activity->getKey(), 'id_detail_kegiatan' => $detail->getKey(), 'id_jenis_dokumen' => $kind,
            'nama_file' => 'foto.jpg', 'tipe_dokumen' => 'image/jpeg', 'link_dokumen' => 'foto.jpg',
        ]);
    }

    public function test_foreign_key_rejects_an_orphan_even_when_bypassing_eloquent(): void
    {
        $activity = $this->activity();
        $this->expectException(QueryException::class);
        DB::table('detail_kegiatan')->insert(['id_kegiatan' => $activity->getKey(), 'id_isian_kategori' => 999999, 'isian' => 'orphan']);
    }

    public function test_upgrade_transfers_legacy_values_and_rollback_restores_original_tables(): void
    {
        $this->withIsolatedMigrationDatabase(function () {
            $migration = require database_path('migrations/2026_11_04_000001_apply_dynamic_activity_erd.php');
            $migration->down();
            $activity = $this->activity();
            $activity->update(['lokasi' => 'Semarang', 'tanggal_mulai' => '2026-11-04']);
            $skim = DB::table('skim')->insertGetId(['nama_skim' => 'Penelitian Terapan', 'ranah' => 'penelitian'], 'id_skim');
            $bidang = DB::table('kelompok_bidang')->insertGetId(['nama_kelompok_bidang' => 'Teknologi Informasi'], 'id_kelompok_bidang');
            DB::table('kegiatan_dosen')->insert([
                'id_kegiatan' => $activity->getKey(), 'id_skim' => $skim, 'id_kelompok_bidang' => $bidang,
                'afiliasi' => 'Polines', 'tahun_anggaran' => 2026, 'tahun_pelaksanaan_ke' => 1, 'lama_kegiatan_tahun' => 2,
                'dana_dikti' => 0, 'dana_pt' => 1234567.89, 'dana_institusi_lain' => 0, 'mitra' => 'Desa A',
            ]);
            DB::table('detail_kegiatan')->insert(['id_kegiatan' => $activity->getKey(), 'penyelenggara' => 'LPPM', 'tingkat_kejuaraan' => 'nasional']);
            $old = (array) DB::table('kegiatan_dosen')->first();
            $migration->up();

            $activity = $activity->fresh();
            $this->assertSame('Penelitian Terapan', $activity->nilaiIsian('skim'));
            $this->assertSame('Teknologi Informasi', $activity->nilaiIsian('kelompok_bidang'));
            $this->assertSame('1234567.89', $activity->nilaiIsian('dana_pt'));
            $this->assertSame((string) $old['dana_dikti'], $activity->nilaiIsian('dana_dikti'));
            $this->assertSame('LPPM', $activity->nilaiIsian('penyelenggara'));
            $this->assertSame('Semarang', $activity->nilaiIsian('lokasi'));
            $this->assertSame('2026-11-04', $activity->nilaiIsian('tanggal_mulai'));
            $this->assertEquals($old, (array) DB::table('legacy_kegiatan_dosen')->first());
            $this->assertDatabaseCount('legacy_detail_kegiatan', 1);

            $migration->down();
            $this->assertEquals($old, (array) DB::table('kegiatan_dosen')->first());
            $this->assertDatabaseHas('detail_kegiatan', ['id_kegiatan' => $activity->getKey(), 'penyelenggara' => 'LPPM']);
            $this->assertDatabaseHas('skim', ['id_skim' => $skim]);
            $this->assertDatabaseHas('kelompok_bidang', ['id_kelompok_bidang' => $bidang]);
        });

        $this->assertTrue(Schema::hasColumn('detail_kegiatan', 'id_detail_kegiatan'));
        $this->assertTrue(DB::connection()->getPdo()->inTransaction());
    }

    private function withIsolatedMigrationDatabase(callable $assertions): void
    {
        $original = DB::getDefaultConnection();
        $source = DB::connection();
        $config = $source->getConfig();
        unset($config['name']);
        $database = null;
        $admin = null;
        if ($config['driver'] === 'sqlite') {
            $config['database'] = ':memory:';
        } else {
            $this->assertContains($config['driver'], ['mysql', 'mariadb']);
            $database = 'sikap_erd_test_'.bin2hex(random_bytes(8));
            // CREATE/DROP DATABASE implicitly commit MySQL transactions. Use a
            // separate admin connection, never the RefreshDatabase transaction.
            $admin = DB::build($config);
            $admin->statement('CREATE DATABASE `'.$database.'` CHARACTER SET utf8mb4');
            $config['database'] = $database;
        }
        config(['database.connections.erd_migration_test' => $config]);
        DB::setDefaultConnection('erd_migration_test');

        try {
            $this->assertSame(0, Artisan::call('migrate', ['--database' => 'erd_migration_test', '--force' => true]));
            $assertions();
        } finally {
            DB::setDefaultConnection($original);
            DB::purge('erd_migration_test');
            if ($database !== null) {
                // Only drop the uniquely named database created by this test.
                $admin->statement('DROP DATABASE `'.$database.'`');
                $admin->disconnect();
            }
        }
    }
}
