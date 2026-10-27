<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penempatan mahasiswa ke rombel.
     */
    public function up(): void
    {
        Schema::create('enrollment_mahasiswa_tingkat_kelas', function (Blueprint $table) {
            $table->id('id_enrollment_mahasiswa_tingkat_kelas');

            // Nama foreign key diberi manual: nama otomatis Laravel
            // (70 karakter) melebihi batas identifier MySQL (64 karakter).
            $table->foreignId('id_enrollment_tingkat_kelas')
                ->constrained(
                    'enrollment_tingkat_kelas',
                    'id_enrollment_tingkat_kelas',
                    'emtk_id_enrollment_tingkat_kelas_foreign'
                )
                ->cascadeOnDelete();

            $table->foreignId('id_mahasiswa')
                ->constrained('mahasiswa', 'id_mahasiswa')
                ->restrictOnDelete();

            $table->timestamps();

            // Nama unique juga diberi manual (alasan sama dengan di atas).
            $table->unique(
                ['id_enrollment_tingkat_kelas', 'id_mahasiswa'],
                'emtk_rombel_mahasiswa_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_mahasiswa_tingkat_kelas');
    }
};
