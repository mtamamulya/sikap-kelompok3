<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jenis_dokumen', function (Blueprint $table) {
            // Menambahkan kolom keterangan setelah nama_jenis_dokumen
            $table->string('keterangan')->nullable()->after('nama_jenis_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_dokumen', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
