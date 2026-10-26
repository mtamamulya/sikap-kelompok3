<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_kegiatan', function (Blueprint $table) {
            $table->id('id_anggota_kegiatan');
            $table->foreignId('id_kegiatan')
                ->constrained('kegiatan', 'id_kegiatan')
                ->cascadeOnDelete();
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
            $table->string('peran', 50);
            $table->timestamps();

            // UNIQUE gabungan untuk mencegah duplikasi anggota di kegiatan yang sama
            $table->unique(['id_kegiatan', 'id_user']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kegiatan');
    }
};
