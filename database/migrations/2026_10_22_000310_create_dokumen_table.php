<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('id_dokumen');
            
            // Foreign Keys
            $table->foreignId('id_jenis_dokumen')->constrained('jenis_dokumen', 'id_jenis_dokumen')->onDelete('cascade');
            $table->foreignId('id_kegiatan')->constrained('kegiatan', 'id_kegiatan')->onDelete('cascade');
            $table->foreignId('id_detail_kegiatan')->nullable()->constrained('detail_kegiatan', 'id_detail_kegiatan')->onDelete('cascade');
            
            // Data Input dari UI Frontend
            $table->text('link_dokumen');
            
            // Field Tambahan (Set Nullable karena UI hanya input link Drive/Cloud)
            $table->string('nama_file')->nullable();
            $table->string('tipe_dokumen')->nullable();
            $table->boolean('is_lampiran')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};