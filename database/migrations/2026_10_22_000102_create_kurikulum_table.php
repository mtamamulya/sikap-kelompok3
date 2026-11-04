<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id('id_kurikulum');
            $table->foreignId('id_prodi')
                ->constrained('prodi', 'id_prodi')
                ->restrictOnDelete();
            $table->string('nama_kurikulum', 100);
            $table->boolean('status')->default(true); // true = kurikulum aktif
            $table->string('link', 255)->nullable();  // file PDF
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum');
    }
};
