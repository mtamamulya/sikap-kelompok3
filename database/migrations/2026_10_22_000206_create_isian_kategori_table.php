<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isian_kategori', function (Blueprint $table) {
            $table->id('id_isian_kategori');
            $table->foreignId('id_kategori')
                ->constrained('kategori', 'id_kategori')
                ->restrictOnDelete();

            $table->string('input', 100);
            $table->string('jenis_input', 30)->default('teks');
            $table->enum('aturan', ['NULL', 'NOT NULL'])->default('NULL');
            $table->unsignedTinyInteger('jumlah_isian')->nullable()->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isian_kategori');
    }
};