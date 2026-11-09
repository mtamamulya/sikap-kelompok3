<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameKind('Kegiatan Prodi & Mahasiswa', 'Kegiatan Dosen & Mahasiswa');
    }

    public function down(): void
    {
        $this->renameKind('Kegiatan Dosen & Mahasiswa', 'Kegiatan Prodi & Mahasiswa');
    }

    private function renameKind(string $from, string $to): void
    {
        // Widen first so MySQL accepts the replacement without losing row IDs
        // or the categories referencing them. Keep the unique index intact.
        $kinds = ['Kegiatan Mahasiswa', 'Kegiatan Prodi', 'Kegiatan Dosen'];
        Schema::table('jenis_kegiatan', function (Blueprint $table) use ($kinds, $from, $to) {
            $table->enum('nama_jenis_kegiatan', [...$kinds, $from, $to])->change();
        });
        DB::table('jenis_kegiatan')->where('nama_jenis_kegiatan', $from)
            ->update(['nama_jenis_kegiatan' => $to]);
        Schema::table('jenis_kegiatan', function (Blueprint $table) use ($kinds, $to) {
            $table->enum('nama_jenis_kegiatan', [...$kinds, $to])->change();
        });
    }
};
