<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isian_kategori', function (Blueprint $table) {
            $table->id('id_isian_kategori');
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->restrictOnDelete();
            $table->string('input', 150);
            $table->string('jenis_input', 40)->default('teks');
            $table->enum('aturan', ['NULL', 'NOT NULL'])->default('NULL');
            // NULL means no fixed upper limit, e.g. multiple documentation files.
            $table->unsignedInteger('jumlah_isian')->nullable()->default(1);
            $table->timestamps();
            $table->unique(['id_kategori', 'input']);
        });
        Schema::rename('detail_kegiatan', 'legacy_detail_kegiatan');
        Schema::create('detail_kegiatan', function (Blueprint $table) {
            $table->id('id_detail_kegiatan');
            $table->unsignedBigInteger('id_kegiatan');
            $table->unsignedBigInteger('id_isian_kategori');
            $table->text('isian')->nullable();
            $table->timestamps();
            // Explicit new names avoid collisions with archived MySQL constraints.
            $table->foreign('id_kegiatan', 'detail_v2_kegiatan_fk')->references('id_kegiatan')->on('kegiatan')->cascadeOnDelete();
            $table->foreign('id_isian_kategori', 'detail_v2_isian_fk')->references('id_isian_kategori')->on('isian_kategori')->restrictOnDelete();
            $table->index(['id_kegiatan', 'id_isian_kategori']);
        });
        Schema::table('kategori', function (Blueprint $table) {
            $table->enum('kebutuhan', ['kegiatan', 'surat'])->default('kegiatan');
        });
        Schema::table('dokumen', function (Blueprint $table) {
            // This FK makes the detail -> documents connector executable.
            $table->foreignId('id_detail_kegiatan')->nullable()
                ->constrained('detail_kegiatan', 'id_detail_kegiatan')->nullOnDelete();
        });
        Schema::table('prestasi', fn (Blueprint $table) => $table->text('capaian')->nullable());
        Schema::table('berita', fn (Blueprint $table) => $table->string('sampul', 500)->nullable());
        Schema::create('bukti_prestasi', function (Blueprint $table) {
            $table->id('id_bukti_prestasi');
            $table->foreignId('id_prestasi')->constrained('prestasi', 'id_prestasi')->cascadeOnDelete();
            $table->foreignId('id_jenis_dokumen')->constrained('jenis_dokumen', 'id_jenis_dokumen')->restrictOnDelete();
            $table->string('link', 500);
            $table->string('nama_file', 255)->nullable();
            $table->timestamps();
        });
        DB::transaction(function () {
            $this->transferExistingAnswers();
            foreach (DB::table('prestasi')->whereNotNull('catatan')->cursor() as $prestasi) {
                DB::table('prestasi')->where('id_prestasi', $prestasi->id_prestasi)->update(['capaian' => $prestasi->catatan]);
            }
        });
        // Keep originals for audit/rollback; selected names are dynamic answers now.
        Schema::rename('kegiatan_dosen', 'legacy_kegiatan_dosen');
        Schema::rename('skim', 'legacy_skim');
        Schema::rename('kelompok_bidang', 'legacy_kelompok_bidang');
    }

    private function definition(int $category, string $input, string $type): int
    {
        $existing = DB::table('isian_kategori')->where('id_kategori', $category)->where('input', $input)->value('id_isian_kategori');

        return $existing ?? DB::table('isian_kategori')->insertGetId([
            'id_kategori' => $category, 'input' => $input, 'jenis_input' => $type,
            'aturan' => 'NULL', 'jumlah_isian' => 1, 'created_at' => now(), 'updated_at' => now(),
        ], 'id_isian_kategori');
    }

    private function answer(object $activity, string $input, string $type, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }
        if ($type === 'tanggal') {
            $value = substr((string) $value, 0, 10);
        }
        DB::table('detail_kegiatan')->insert([
            'id_kegiatan' => $activity->id_kegiatan,
            'id_isian_kategori' => $this->definition($activity->id_kategori, $input, $type),
            'isian' => (string) $value,
            'created_at' => $activity->created_at ?? now(), 'updated_at' => $activity->updated_at ?? now(),
        ]);
    }

    private function transferExistingAnswers(): void
    {
        $common = ['lokasi' => 'teks', 'deskripsi' => 'teks', 'tanggal_mulai' => 'tanggal', 'tanggal_selesai' => 'tanggal'];
        $previousDetail = [
            'id_dosen' => 'angka', 'tingkat_kejuaraan' => 'teks', 'penyelenggara' => 'teks',
            'link_website' => 'tautan', 'posisi_dalam_kegiatan' => 'teks',
        ];
        $lecturer = [
            'afiliasi' => 'teks', 'tahun_anggaran' => 'angka', 'tahun_pelaksanaan_ke' => 'angka',
            'lama_kegiatan_tahun' => 'angka', 'dana_dikti' => 'angka', 'dana_pt' => 'angka',
            'dana_institusi_lain' => 'angka', 'in_kind' => 'teks', 'mitra' => 'teks',
        ];
        foreach (DB::table('kategori')->cursor() as $category) {
            foreach ($common as $input => $type) {
                $this->definition($category->id_kategori, $input, $type);
            }
            if (preg_match('/penelitian|pengabdian/iu', $category->nama_kategori)) {
                foreach (['skim' => 'pilihan', 'kelompok_bidang' => 'pilihan'] + $lecturer as $input => $type) {
                    $this->definition($category->id_kategori, $input, $type);
                }
            }
        }
        DB::table('kegiatan')->orderBy('id_kegiatan')->chunkById(200, function ($activities) use ($common, $previousDetail, $lecturer) {
            foreach ($activities as $activity) {
                foreach ($common as $input => $type) {
                    $this->answer($activity, $input, $type, $activity->{$input});
                }
                $old = DB::table('legacy_detail_kegiatan')->where('id_kegiatan', $activity->id_kegiatan)->first();
                if ($old) {
                    foreach ($previousDetail as $input => $type) {
                        $this->answer($activity, $input, $type, $old->{$input});
                    }
                }
                $old = DB::table('kegiatan_dosen')->where('id_kegiatan', $activity->id_kegiatan)->first();
                if ($old) {
                    foreach ($lecturer as $input => $type) {
                        $this->answer($activity, $input, $type, $old->{$input});
                    }
                    $this->answer($activity, 'skim', 'pilihan', DB::table('skim')->where('id_skim', $old->id_skim)->value('nama_skim'));
                    $this->answer($activity, 'kelompok_bidang', 'pilihan', DB::table('kelompok_bidang')->where('id_kelompok_bidang', $old->id_kelompok_bidang)->value('nama_kelompok_bidang'));
                }
            }
        }, 'id_kegiatan');
    }

    public function down(): void
    {
        Schema::rename('legacy_skim', 'skim');
        Schema::rename('legacy_kelompok_bidang', 'kelompok_bidang');
        Schema::rename('legacy_kegiatan_dosen', 'kegiatan_dosen');
        Schema::dropIfExists('bukti_prestasi');
        Schema::table('berita', fn (Blueprint $table) => $table->dropColumn('sampul'));
        Schema::table('prestasi', fn (Blueprint $table) => $table->dropColumn('capaian'));
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropForeign(['id_detail_kegiatan']);
            $table->dropColumn('id_detail_kegiatan');
        });
        Schema::table('kategori', fn (Blueprint $table) => $table->dropColumn('kebutuhan'));
        Schema::drop('detail_kegiatan');
        Schema::rename('legacy_detail_kegiatan', 'detail_kegiatan');
        Schema::drop('isian_kategori');
    }
};
