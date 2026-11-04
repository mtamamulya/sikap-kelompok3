<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timeline', function (Blueprint $table) {
            $table->id('id_timeline');
            $table->foreignId('id_kegiatan')
                ->constrained('kegiatan', 'id_kegiatan')
                ->cascadeOnDelete();
            $table->enum('status', ['diajukan', 'direvisi', 'ditolak', 'direview', 'diterima', 'selesai']);
            $table->datetime('tanggal_status');
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline');
    }
};
