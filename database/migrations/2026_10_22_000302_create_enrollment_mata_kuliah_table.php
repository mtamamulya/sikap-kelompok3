<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penawaran mata kuliah pada satu tahun akademik.
     */
    public function up(): void
    {
        Schema::create('enrollment_mata_kuliah', function (Blueprint $table) {
            $table->id('id_enrollment_mk');

            $table->foreignId('id_mk')
                ->constrained('mata_kuliah', 'id_mk')
                ->restrictOnDelete();

            $table->foreignId('id_tahun_akademik')
                ->constrained('tahun_akademik', 'id_tahun_akademik')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(['id_mk', 'id_tahun_akademik']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_mata_kuliah');
    }
};
