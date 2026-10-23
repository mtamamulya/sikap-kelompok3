<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rombel: prodi + tingkat + kelas pada satu tahun akademik.
     * Contoh: D4-TI Tingkat 2 Kelas A, Ganjil 2025/2026.
     */
    public function up(): void
    {
        Schema::create('enrollment_tingkat_kelas', function (Blueprint $table) {
            $table->id('id_enrollment_tingkat_kelas');

            $table->foreignId('id_kelas')
                ->constrained('kelas', 'id_kelas')
                ->restrictOnDelete();

            $table->foreignId('id_prodi')
                ->constrained('prodi', 'id_prodi')
                ->restrictOnDelete();

            $table->foreignId('id_tingkat')
                ->constrained('tingkat', 'id_tingkat')
                ->restrictOnDelete();

            $table->foreignId('id_tahun_akademik')
                ->constrained('tahun_akademik', 'id_tahun_akademik')
                ->restrictOnDelete();

            $table->timestamps();

            // Nama diberi manual: nama otomatis Laravel (78 karakter)
            // melebihi batas identifier MySQL (64 karakter).
            $table->unique(
                ['id_prodi', 'id_tingkat', 'id_kelas', 'id_tahun_akademik'],
                'etk_prodi_tingkat_kelas_ta_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_tingkat_kelas');
    }
};
