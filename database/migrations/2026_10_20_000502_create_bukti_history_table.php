<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_history', function (Blueprint $table) {
            $table->id('id_bukti_history'); 
            
            $table->unsignedBigInteger('id_dokumen'); 
            $table->unsignedBigInteger('id_timeline'); 
            
            $table->timestamps();

            $table->foreign('id_dokumen')->references('id_dokumen')->on('dokumen')->restrictOnDelete();
            $table->foreign('id_timeline')->references('id_timeline')->on('timeline')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_history');
    }
};
