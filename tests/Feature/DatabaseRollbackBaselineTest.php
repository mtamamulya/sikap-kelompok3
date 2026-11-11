<?php

namespace Tests\Feature;

use App\Models\JenisKegiatan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseRollbackBaselineTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_and_repeated_seeding_keep_the_database_before_november_nine_changes(): void
    {
        $this->assertFileDoesNotExist(database_path('migrations/2026_11_09_000001_align_jenis_kegiatan_with_latest_erd.php'));
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'dosen@trk.ac.id')->firstOrFail();
        $user->update(['password' => 'changed-password', 'status' => false]);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('roles', 4);
        $this->assertDatabaseCount('users', 4);
        foreach (['jenis_kegiatan', 'kategori', 'isian_kategori', 'dosen', 'mahasiswa'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
        $this->assertTrue(Hash::check('changed-password', $user->fresh()->password));
        $this->assertFalse($user->fresh()->status);

        // The original enum value remains valid after all active migrations.
        $kind = JenisKegiatan::create(['nama_jenis_kegiatan' => 'Kegiatan Prodi & Mahasiswa']);
        $this->assertSame('Kegiatan Prodi & Mahasiswa', $kind->fresh()->nama_jenis_kegiatan);
    }
}
