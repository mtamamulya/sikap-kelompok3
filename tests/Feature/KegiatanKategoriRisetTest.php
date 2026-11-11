<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Penelitian dan Pengabdian sekarang kategori di form Ajukan Kegiatan (bukan menu sendiri).
 */
class KegiatanKategoriRisetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_form_offers_penelitian_and_pengabdian_categories(): void
    {
        foreach (['dosen.kegiatan.create'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee('<option>Penelitian</option>', false)
                ->assertSee('<option>Pengabdian</option>', false);
        }
    }

    public function test_category_fields_are_defined_for_both_and_are_well_formed(): void
    {
        $medan = require resource_path('views/dosen/kegiatan/_medan-kategori.php');
        $tipeSah = ['pilihan', 'radio', 'teks', 'angka', 'tanggal', 'rupiah', 'turunan'];

        foreach (['Penelitian', 'Pengabdian'] as $kategori) {
            $this->assertArrayHasKey($kategori, $medan);
            $nama = array_column($medan[$kategori]['medan'], 'nama');
            $this->assertSame(array_unique($nama), $nama, "$kategori: nama isian harus unik");
            foreach (['skim', 'ranah', 'kelompok_bidang', 'nomor_sk', 'tanggal_sk', 'dana_dikti', 'in_kind', 'mitra'] as $wajibAda) {
                $this->assertContains($wajibAda, $nama, "$kategori: isian $wajibAda hilang");
            }
        }

        foreach ($medan as $kategori => $isi) {
            foreach ($isi['medan'] as $f) {
                $this->assertContains($f['tipe'], $tipeSah, "$kategori/{$f['nama']}: tipe tidak dikenal");
                if (in_array($f['tipe'], ['pilihan', 'radio'], true)) {
                    $this->assertNotEmpty($f['opsi'], "$kategori/{$f['nama']}: opsi kosong");
                }
                if ($f['tipe'] === 'turunan') {
                    $this->assertContains($f['dari'], array_column($isi['medan'], 'nama'), "$kategori/{$f['nama']}: sumber turunan tidak ada");
                    $this->assertNotEmpty($f['peta']);
                }
            }
        }

        // Ranah mengikuti skim: setiap skim punya ranah
        $skim = $medan['Penelitian']['medan'][0]['opsi'];
        $peta = $medan['Penelitian']['medan'][1]['peta'];
        $this->assertSame($skim, array_keys($peta));
    }

    public function test_sidebar_no_longer_has_penelitian_and_pengabdian_menus(): void
    {
        // Dicek di halaman Kegiatan (kartu ringkasan di Dashboard punya tautan sendiri ke Penelitian/Pengabdian)
        $this->get(route('dosen.kegiatan.index'))->assertOk()
            ->assertSee('href="'.route('dosen.kegiatan.index').'"', false)
            ->assertSee('href="'.route('pengajaran.index').'"', false)
            ->assertDontSee('href="'.route('penelitian.index').'"', false)
            ->assertDontSee('href="'.route('pengabdian.index').'"', false);
    }

    public function test_kegiatan_list_shows_who_invited_the_lecturer(): void
    {
        $this->get(route('dosen.kegiatan.index'))->assertOk()
            ->assertSee('Diajak oleh', false);
    }
}
