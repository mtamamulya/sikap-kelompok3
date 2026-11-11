<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_kegiatan', function (Blueprint $table) {
            $table->id('id_detail_kegiatan');
            $table->foreignId('id_kegiatan')
                ->constrained('kegiatan', 'id_kegiatan')
                ->cascadeOnDelete();
            $table->foreignId('id_isian_kategori')
                ->constrained('isian_kategori', 'id_isian_kategori')
                ->restrictOnDelete();

            $table->text('isian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatan');
    }
};