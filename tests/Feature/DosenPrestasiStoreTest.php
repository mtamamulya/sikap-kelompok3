<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\AnggotaKegiatan;
use App\Models\Kegiatan;
use App\Models\Kategori;
use App\Models\JenisKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DosenPrestasiStoreTest extends TestCase
{
    use RefreshDatabase;

    private function dosen(string $email, string $nama): User
    {
        $roleId = DB::table('roles')->where('id_role', 3)->value('id_role')
            ?? DB::table('roles')->insertGetId([
                'id_role' => 3,
                'nama_role' => 'dosen',
            ], 'id_role');
        $user = User::create([
            'id_role' => $roleId,
            'email' => $email,
            'password' => 'password',
        ]);

        Dosen::create([
            'id_user' => $user->getKey(),
            'nip' => uniqid(),
            'nama' => $nama,
        ]);

        return $user;
    }

    private function dosenTanpaProfil(string $email): User
    {
        $roleId = DB::table('roles')->where('id_role', 3)->value('id_role')
            ?? DB::table('roles')->insertGetId([
                'id_role' => 3,
                'nama_role' => 'dosen',
            ], 'id_role');

        return User::create([
            'id_role' => $roleId,
            'email' => $email,
            'password' => 'password',
        ]);
    }

    private function prestasiInput(array $overrides = []): array
    {
        return array_replace([
            'sumber' => 'mandiri',
            'nama_prestasi' => 'Juara 1 Lomba Inovasi',
            'penyelenggara' => 'Politeknik Negeri Semarang',
            'tingkat' => 'Nasional',
            'jenis' => 'Akademik',
            'tanggal_mulai' => '2026-10-10',
            'tanggal_selesai' => '2026-10-12',
            'capaian' => 'Juara 1',
            'kategori' => 'Teknologi',
            'bentuk' => 'Individu',
            'deskripsi' => 'Mengembangkan solusi inovatif.',
            'dokumen' => [
                ['jenis' => 'Sertifikat', 'tautan' => 'https://drive.google.com/file/d/sertifikat'],
            ],
        ], $overrides);
    }

    public function test_dosen_can_save_an_individual_achievement_and_its_proof(): void
    {
        $dosen = $this->dosen('dosen-prestasi@test.local', 'Dosen Penguji');

        $response = $this->actingAs($dosen)->post(route('prestasi.store'), $this->prestasiInput());

        $response->assertRedirect(route('prestasi.index'))
            ->assertSessionHas('sukses', 'Prestasi berhasil disimpan.');
        $this->assertDatabaseHas('prestasi', [
            'jenis_prestasi' => 'akademik',
            'bidang_lomba' => 'Teknologi',
            'capaian' => 'Juara 1',
        ]);
        $prestasi = DB::table('prestasi')->first();
        $kegiatan = DB::table('kegiatan')->where('id_kegiatan', $prestasi->id_kegiatan)->first();

        $this->assertSame((int) $dosen->getKey(), (int) $kegiatan->id_user);
        $this->assertSame('Juara 1 Lomba Inovasi', $kegiatan->nama_kegiatan);
        $this->assertSame('2026-10-10', $kegiatan->tanggal_mulai);
        $this->assertDatabaseHas('bukti_prestasi', [
            'id_prestasi' => $prestasi->id_prestasi,
            'link' => 'https://drive.google.com/file/d/sertifikat',
        ]);
        $this->assertDatabaseHas('detail_kegiatan', [
            'id_kegiatan' => $kegiatan->id_kegiatan,
            'isian' => 'Politeknik Negeri Semarang',
        ]);
        $this->get(route('prestasi.index'))
            ->assertOk()
            ->assertSee('Juara 1 Lomba Inovasi')
            ->assertSee('Politeknik Negeri Semarang');
    }

    public function test_seeded_dosen_role_can_save_without_a_dosen_profile_row(): void
    {
        $dosen = $this->dosenTanpaProfil('dosen-tanpa-profil@test.local');

        $this->actingAs($dosen)
            ->post(route('prestasi.store'), $this->prestasiInput())
            ->assertRedirect(route('prestasi.index'));

        $this->assertDatabaseHas('prestasi', ['capaian' => 'Juara 1']);
    }

    public function test_form_shows_example_previous_activities_when_database_has_none(): void
    {
        $dosen = $this->dosenTanpaProfil('dosen-contoh@test.local');

        $this->actingAs($dosen)
            ->get(route('prestasi.create'))
            ->assertOk()
            ->assertSee('KMIPN VIII 2026 · Lomba (contoh)')
            ->assertSee('Seminar nasional AI · Seminar (contoh)')
            ->assertSee('Lomba karya ilmiah · Lomba (contoh)');
    }

    public function test_example_previous_activity_prefills_but_does_not_create_a_fake_link(): void
    {
        $dosen = $this->dosenTanpaProfil('dosen-contoh-simpan@test.local');

        $this->actingAs($dosen)
            ->post(route('prestasi.store'), $this->prestasiInput([
                'sumber' => 'kegiatan',
                'kegiatan_sebelumnya' => 'contoh:kmipn',
            ]))
            ->assertRedirect(route('prestasi.index'));

        $this->assertDatabaseHas('kegiatan', [
            'nama_kegiatan' => 'Juara 1 Lomba Inovasi',
            'id_kegiatan_sebelumnya' => null,
        ]);
        $this->assertDatabaseCount('kegiatan', 1);
    }

    public function test_dosen_can_save_a_team_achievement_linked_to_one_of_their_activities(): void
    {
        $dosen = $this->dosen('ketua-prestasi@test.local', 'Ketua Dosen');
        $member = $this->dosen('anggota-prestasi@test.local', 'Anggota Dosen');
        $jenis = JenisKegiatan::firstOrCreate([
            'nama_jenis_kegiatan' => 'Kegiatan Dosen & Mahasiswa',
        ]);
        $kategori = Kategori::create([
            'id_jenis_kegiatan' => $jenis->getKey(),
            'nama_kategori' => 'Kegiatan Sebelumnya',
        ]);
        $kegiatanSebelumnya = Kegiatan::create([
            'id_user' => $dosen->getKey(),
            'id_kategori' => $kategori->getKey(),
            'nama_kegiatan' => 'Lomba Robotik',
        ]);

        $response = $this->actingAs($dosen)->post(route('prestasi.store'), $this->prestasiInput([
            'sumber' => 'kegiatan',
            'kegiatan_sebelumnya' => $kegiatanSebelumnya->getKey(),
            'bentuk' => 'Tim',
            'anggota' => [
                ['id_user' => $dosen->getKey(), 'peran' => 'Ketua'],
                ['id_user' => $member->getKey(), 'peran' => 'Anggota'],
            ],
        ]));

        $response->assertRedirect(route('prestasi.index'));
        $kegiatanPrestasi = DB::table('kegiatan')
            ->where('nama_kegiatan', 'Juara 1 Lomba Inovasi')
            ->first();

        $this->assertSame((int) $kegiatanSebelumnya->getKey(), (int) $kegiatanPrestasi->id_kegiatan_sebelumnya);
        $this->assertDatabaseHas('anggota_kegiatan', [
            'id_kegiatan' => $kegiatanPrestasi->id_kegiatan,
            'id_user' => $dosen->getKey(),
            'peran' => 'Ketua',
        ]);
        $this->assertDatabaseHas('anggota_kegiatan', [
            'id_kegiatan' => $kegiatanPrestasi->id_kegiatan,
            'id_user' => $member->getKey(),
            'peran' => 'Anggota',
        ]);
    }

    public function test_previous_activity_must_belong_to_the_logged_in_dosen(): void
    {
        $dosen = $this->dosen('pemilik-prestasi@test.local', 'Pemilik');
        $other = $this->dosen('bukan-pemilik-prestasi@test.local', 'Bukan Pemilik');
        $jenis = JenisKegiatan::firstOrCreate([
            'nama_jenis_kegiatan' => 'Kegiatan Dosen & Mahasiswa',
        ]);
        $kategori = Kategori::create([
            'id_jenis_kegiatan' => $jenis->getKey(),
            'nama_kategori' => 'Kegiatan Orang Lain',
        ]);
        $kegiatanOrangLain = Kegiatan::create([
            'id_user' => $other->getKey(),
            'id_kategori' => $kategori->getKey(),
            'nama_kegiatan' => 'Kegiatan milik orang lain',
        ]);

        $this->from(route('prestasi.create'))
            ->actingAs($dosen)
            ->post(route('prestasi.store'), $this->prestasiInput([
                'sumber' => 'kegiatan',
                'kegiatan_sebelumnya' => $kegiatanOrangLain->getKey(),
            ]))
            ->assertRedirect(route('prestasi.create'))
            ->assertSessionHasErrors('kegiatan_sebelumnya');

        $this->assertDatabaseCount('prestasi', 0);
    }

    public function test_previous_activities_include_activities_where_the_dosen_is_a_member(): void
    {
        $owner = $this->dosen('pemilik-kegiatan@test.local', 'Pemilik Kegiatan');
        $member = $this->dosen('anggota-kegiatan@test.local', 'Anggota Kegiatan');
        $jenis = JenisKegiatan::firstOrCreate([
            'nama_jenis_kegiatan' => 'Kegiatan Dosen',
        ]);
        $kategori = Kategori::create([
            'id_jenis_kegiatan' => $jenis->getKey(),
            'nama_kategori' => 'Seminar Bersama',
        ]);
        $kegiatanSebelumnya = Kegiatan::create([
            'id_user' => $owner->getKey(),
            'id_kategori' => $kategori->getKey(),
            'nama_kegiatan' => 'Seminar yang diikuti anggota',
        ]);
        AnggotaKegiatan::create([
            'id_kegiatan' => $kegiatanSebelumnya->getKey(),
            'id_user' => $member->getKey(),
            'peran' => 'Anggota',
        ]);

        $this->actingAs($member)
            ->get(route('prestasi.create'))
            ->assertOk()
            ->assertSee('value="'.$kegiatanSebelumnya->getKey().'"', false)
            ->assertSee('Seminar yang diikuti anggota');

        $this->actingAs($member)
            ->post(route('prestasi.store'), $this->prestasiInput([
                'sumber' => 'kegiatan',
                'kegiatan_sebelumnya' => $kegiatanSebelumnya->getKey(),
            ]))
            ->assertRedirect(route('prestasi.index'));

        $this->assertDatabaseHas('kegiatan', [
            'nama_kegiatan' => 'Juara 1 Lomba Inovasi',
            'id_kegiatan_sebelumnya' => $kegiatanSebelumnya->getKey(),
        ]);
    }
}
