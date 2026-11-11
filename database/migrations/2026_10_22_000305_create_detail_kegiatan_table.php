<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_kegiatan', function (Blueprint $table) {
            $table->foreignId('id_kegiatan')
                ->primary()
                ->constrained('kegiatan', 'id_kegiatan')
                ->cascadeOnDelete();
            $table->foreignId('id_dosen')
                ->nullable()
                ->constrained('dosen', 'id_dosen')
                ->nullOnDelete();

            $table->enum('tingkat_kejuaraan', ['kabupaten/kota', 'provinsi', 'nasional', 'internasional'])->nullable();
            $table->string('penyelenggara', 150)->nullable();
            $table->string('link_website', 255)->nullable();
            $table->string('posisi_dalam_kegiatan', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatan');
    }
};
