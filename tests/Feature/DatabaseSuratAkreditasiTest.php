<?php

namespace Tests\Feature;

use App\Models\Akreditasi;
use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSuratAkreditasiTest extends TestCase
{
    use RefreshDatabase;

    private function records(): array
    {
        $role = DB::table('roles')->insertGetId(['nama_role' => 'kaprodi'], 'id_role');
        $user = User::create(['id_role' => $role, 'email' => 'kaprodi@example.test', 'password' => 'password']);
        $jenis = DB::table('jenis_kegiatan')->insertGetId(['nama_jenis_kegiatan' => 'Kegiatan Dosen'], 'id_jenis_kegiatan');
        $kategori = Kategori::create(['id_jenis_kegiatan' => $jenis, 'nama_kategori' => 'Penelitian']);
        $kegiatan = Kegiatan::create(['id_user' => $user->getKey(), 'id_kategori' => $kategori->getKey(), 'nama_kegiatan' => 'Penelitian uji surat']);
        $jenisDokumen = DB::table('jenis_dokumen')->insertGetId(['nama_jenis_dokumen' => 'Proposal'], 'id_jenis_dokumen');
        $dokumen = Dokumen::create([
            'id_jenis_dokumen' => $jenisDokumen, 'id_kegiatan' => $kegiatan->getKey(),
            'nama_file' => 'proposal.pdf', 'tipe_dokumen' => 'application/pdf', 'link_dokumen' => 'dokumen/proposal.pdf',
        ]);
        $akreditasi = Akreditasi::create(['id_user' => $user->getKey(), 'keterangan' => 'Baik Sekali']);

        return compact('user', 'kategori', 'kegiatan', 'dokumen', 'akreditasi');
    }

    public function test_new_fields_can_be_saved_and_cast_without_changing_their_meaning(): void
    {
        ['user' => $user, 'kategori' => $kategori, 'dokumen' => $dokumen, 'akreditasi' => $akreditasi] = $this->records();

        $this->assertNull($kategori->fresh()->template_surat);
        $this->assertFalse($kategori->fresh()->lampiran_peserta);
        $this->assertFalse($dokumen->fresh()->is_lampiran);
        $this->assertNull($user->fresh()->tanda_tangan_path);
        $this->assertNull($akreditasi->fresh()->status);

        $kategori->update(['template_surat' => 'Surat untuk {{ nama_kegiatan }}', 'lampiran_peserta' => true]);
        $user->update(['tanda_tangan_path' => 'tanda-tangan/kaprodi.png']);
        $dokumen->update(['is_lampiran' => true]);
        $akreditasi->update(['tahun_terbit' => 2026, 'berlaku_sampai' => '2031-11-02', 'status' => true]);

        $this->assertSame('Surat untuk {{ nama_kegiatan }}', $kategori->fresh()->template_surat);
        $this->assertTrue($kategori->fresh()->lampiran_peserta);
        $this->assertSame('tanda-tangan/kaprodi.png', $user->fresh()->tanda_tangan_path);
        $this->assertTrue($dokumen->fresh()->is_lampiran);
        $this->assertSame(2026, $akreditasi->fresh()->tahun_terbit);
        $this->assertSame('2031-11-02', $akreditasi->fresh()->berlaku_sampai->toDateString());
        $this->assertTrue($akreditasi->fresh()->status);
        $akreditasi->update(['status' => false]);
        $this->assertFalse($akreditasi->fresh()->status);
    }

    public function test_timeline_retains_every_status_and_exposes_latest_status_and_parent(): void
    {
        ['kegiatan' => $kegiatan] = $this->records();
        $diajukan = $kegiatan->timeline()->create(['status' => 'diajukan', 'tanggal_status' => '2026-11-01 09:00:00']);
        $direvisi = $kegiatan->timeline()->create(['status' => 'direvisi', 'tanggal_status' => '2026-11-02 10:00:00', 'catatan_revisi' => 'Lengkapi lampiran']);
        $diajukanUlang = $kegiatan->timeline()->create(['status' => 'diajukan', 'tanggal_status' => '2026-11-03 08:00:00']);
        $other = Kegiatan::create(['id_user' => $kegiatan->id_user, 'id_kategori' => $kegiatan->id_kategori, 'nama_kegiatan' => 'Kegiatan lain']);
        $other->timeline()->create(['status' => 'selesai', 'tanggal_status' => '2026-12-01 10:00:00']);

        $this->assertSame([$diajukan->getKey(), $direvisi->getKey(), $diajukanUlang->getKey()], $kegiatan->timeline->modelKeys());
        $this->assertSame($kegiatan->getKey(), $direvisi->kegiatan->getKey());
        $this->assertSame('Lengkapi lampiran', $kegiatan->timeline[1]->catatan_revisi);
        $this->assertSame($diajukanUlang->getKey(), $kegiatan->fresh()->statusTerkini->getKey());
        $this->assertDatabaseHas('timeline', ['id_timeline' => $diajukan->getKey(), 'status' => 'diajukan']);
        $this->assertDatabaseCount('timeline', 4);
    }

    public function test_upgrade_and_rollback_keep_existing_rows_and_original_columns(): void
    {
        $migrations = array_map(fn ($name) => require database_path('migrations/'.$name), [
            '2026_11_03_000001_add_surat_fields_to_kategori_table.php',
            '2026_11_03_000002_add_tanda_tangan_path_to_users_table.php',
            '2026_11_03_000003_add_is_lampiran_to_dokumen_table.php',
            '2026_11_03_000004_add_validity_fields_to_akreditasi_table.php',
        ]);

        foreach (array_reverse($migrations) as $migration) {
            $migration->down();
        }
        ['user' => $user, 'kategori' => $kategori, 'dokumen' => $dokumen, 'akreditasi' => $akreditasi] = $this->records();
        foreach ($migrations as $migration) {
            $migration->up();
        }

        $this->assertSame('Penelitian', $kategori->fresh()->nama_kategori);
        $this->assertFalse($kategori->fresh()->lampiran_peserta);
        $this->assertNull($kategori->fresh()->template_surat);
        $this->assertSame('kaprodi@example.test', $user->fresh()->email);
        $this->assertNull($user->fresh()->tanda_tangan_path);
        $this->assertSame('proposal.pdf', $dokumen->fresh()->nama_file);
        $this->assertFalse($dokumen->fresh()->is_lampiran);
        $this->assertSame('Baik Sekali', $akreditasi->fresh()->keterangan);
        $this->assertNull($akreditasi->fresh()->tahun_terbit);
        $this->assertNull($akreditasi->fresh()->berlaku_sampai);
        $this->assertNull($akreditasi->fresh()->status);

        foreach (array_reverse($migrations) as $migration) {
            $migration->down();
        }
        $this->assertFalse(Schema::hasColumn('kategori', 'template_surat'));
        $this->assertFalse(Schema::hasColumn('kategori', 'lampiran_peserta'));
        $this->assertFalse(Schema::hasColumn('users', 'tanda_tangan_path'));
        $this->assertFalse(Schema::hasColumn('dokumen', 'is_lampiran'));
        $this->assertFalse(Schema::hasColumn('akreditasi', 'tahun_terbit'));
        $this->assertFalse(Schema::hasColumn('akreditasi', 'berlaku_sampai'));
        $this->assertFalse(Schema::hasColumn('akreditasi', 'status'));
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('kategori', 1);
        $this->assertDatabaseCount('dokumen', 1);
        $this->assertDatabaseCount('akreditasi', 1);
    }
}
