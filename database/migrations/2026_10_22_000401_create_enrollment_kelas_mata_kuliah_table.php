<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_kelas_mata_kuliah', function (Blueprint $table) {
            $table->id('id_enrollment_kelas_mk');
            $table->foreignId('id_enrollment_tingkat_kelas')
                ->constrained('enrollment_tingkat_kelas', 'id_enrollment_tingkat_kelas')
                ->cascadeOnDelete();
            $table->foreignId('id_enrollment_mk')
                ->constrained('enrollment_mata_kuliah', 'id_enrollment_mk')
                ->restrictOnDelete();
            $table->foreignId('id_dosen')
                ->constrained('dosen', 'id_dosen')
                ->restrictOnDelete();
            $table->timestamps();

            $table->unique(
                ['id_enrollment_tingkat_kelas', 'id_enrollment_mk'],
                'ekmk_tingkat_kelas_mk_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_kelas_mata_kuliah');
    }
};
