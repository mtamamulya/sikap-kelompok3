<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akreditasi', function (Blueprint $table) {
            $table->year('tahun_terbit')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->boolean('status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('akreditasi', function (Blueprint $table) {
            $table->dropColumn(['tahun_terbit', 'berlaku_sampai', 'status']);
        });
    }
};
