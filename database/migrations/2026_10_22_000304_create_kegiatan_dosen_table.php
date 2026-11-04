<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_dosen', function (Blueprint $table) {
            $table->foreignId('id_kegiatan')
                ->primary()
                ->constrained('kegiatan', 'id_kegiatan')
                ->cascadeOnDelete();
            $table->foreignId('id_skim')
                ->nullable()
                ->constrained('skim', 'id_skim')
                ->restrictOnDelete();
            $table->foreignId('id_kelompok_bidang')
                ->nullable()
                ->constrained('kelompok_bidang', 'id_kelompok_bidang')
                ->restrictOnDelete();
            $table->string('afiliasi', 150);
            $table->year('tahun_anggaran');
            $table->unsignedTinyInteger('tahun_pelaksanaan_ke');
            $table->unsignedTinyInteger('lama_kegiatan_tahun');
            $table->decimal('dana_dikti', 15, 2)->default(0);
            $table->decimal('dana_pt', 15, 2)->default(0);
            $table->decimal('dana_institusi_lain', 15, 2)->default(0);
            $table->string('in_kind', 255)->nullable();
            $table->string('mitra', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_dosen');
    }
};
