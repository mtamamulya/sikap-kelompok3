@extends('layouts.app')

@section('title', 'Profil')

@section('content')

@php
    // DATA TIRUAN. Nanti diganti data user yang login dari controller / database oleh tim backend.
    $user = [
        'nama'    => 'Kuwat Santoso',
        'inisial' => 'KS',
        'nip'     => '198407192019031008',
        'status'  => 'Aktif',
        'email'   => 'kuwatsantoso@polines.ac.id',
        'role'    => 'Dosen',
        'pangkat' => 'Lektor Kepala, IV/a',
        'akun'    => 'Aktif',
    ];

    // Foto: dipakai kalau file public/images/kuwat.jpg ada, kalau tidak tampil inisial.
    $foto = file_exists(public_path('images/kuwat.jpg')) ? asset('images/kuwat.jpg') : null;

    $info = [
        'Email akun'               => $user['email'],
        'Role'                     => $user['role'],
        'Pangkat / golongan saat ini' => $user['pangkat'],
        'Akun login'               => $user['akun'],
    ];

    $riwayat = [
        ['pangkat' => 'Ketua Prodi',  'golongan' => 'IV/a', 'sk' => '1042/UN/KP/2022', 'tanggal' => '12 Januari 2022', 'sekarang' => true],
        ['pangkat' => 'Lektor',       'golongan' => 'III/d', 'sk' => '0788/UN/KP/2018', 'tanggal' => '1 Oktober 2018',  'sekarang' => false],
        ['pangkat' => 'Lektor',       'golongan' => 'III/c', 'sk' => '0415/UN/KP/2015', 'tanggal' => '1 April 2015',    'sekarang' => false],
        ['pangkat' => 'Asisten Ahli', 'golongan' => 'III/b', 'sk' => '0120/UN/KP/2012', 'tanggal' => '1 Maret 2012',    'sekarang' => false],
    ];

    // Aktivitas per tab. 'peran' tampil sebagai badge di kanan.
    $aktivitas = [
        'Penelitian' => [
            ['judul' => 'Deteksi hoaks dengan machine learning',  'meta' => '2026 • Penelitian Terapan',       'peran' => 'Ketua'],
            ['judul' => 'Analisis sentimen ulasan aplikasi',      'meta' => '2025 • Penelitian Dasar',         'peran' => 'Anggota'],
            ['judul' => 'Sistem rekomendasi mata kuliah',         'meta' => '2025 • Penelitian Pengembangan',  'peran' => 'Anggota'],
        ],
        'Pengabdian' => [
            ['judul' => 'Pelatihan Industri',                     'meta' => '2026 • Pengabdian Masyarakat',    'peran' => 'Ketua'],
            ['judul' => 'Seminar Nasional AI',                    'meta' => '2025 • Pengabdian Masyarakat',    'peran' => 'Anggota'],
        ],
        'Pengajaran' => [
            ['judul' => 'Kecerdasan Buatan',                      'meta' => '2025/2026 Ganjil • Kelas 2A',     'peran' => 'Pengampu'],
            ['judul' => 'Sistem Basis Data',                      'meta' => '2025/2026 Genap • Kelas 3A',      'peran' => 'Pengampu'],
        ],
        'Kegiatan' => [
            ['judul' => 'Kuliah Tamu',                            'meta' => '10 Juli 2026 • Pengajaran',       'peran' => 'Peserta'],
        ],
    ];

    // TUJUAN "Simpan Kata Sandi": web master milik kelompok lain.
    // Isi dengan alamatnya kalau sudah diberi tahu, misalnya 'https://alamat-web-master/ubah-sandi'.
    // Selama masih '#', tombolnya hanya simulasi dan tidak mengirim apa-apa.
    $urlSimpanSandi = '#';

    // Email disamarkan untuk teks "Kode OTP akan dikirim ke ..."
    [$emailNama, $emailDomain] = explode('@', $user['email']);
    $emailSamar = substr($emailNama, 0, 1) . str_repeat('*', 11) . '@' . $emailDomain;

    $badge = 'rounded-full border border-blue-200 bg-blue-100 px-2.5 py-0.5 text-[13px] text-brand';
@endphp

    {{-- Judul halaman + tombol --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-4xl font-bold tracking-tight">Profil</h1>
            <p class="text-2xl text-gray-400">Dashboard / Profil</p>
        </div>
        <div class="mt-2 flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="flex h-11 items-center gap-2 rounded-md bg-blue-100 px-4 text-base font-medium text-brand hover:bg-blue-200">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                Kembali
            </a>
            {{-- Membuka modal Ubah Kata Sandi (ada di bagian bawah halaman ini) --}}
            <button type="button" x-data x-on:click="$dispatch('buka-sandi')"
                    class="flex h-11 cursor-pointer items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                Ubah Kata Sandi
            </button>
        </div>
    </div>

    {{-- Kartu identitas --}}
    <section class="flex flex-col gap-8 rounded-lg bg-white p-5 shadow-sm">
        <div class="flex items-center gap-5">
            {{-- Foto ikut berubah kalau modal "Ubah foto profil" menyimpan foto baru (simulasi) --}}
            <div class="relative shrink-0" x-data="{ src: @js($foto) }" x-on:foto-diganti.window="src = $event.detail">
                <img x-show="src" :src="src" alt="Foto {{ $user['nama'] }}" class="size-[88px] rounded-full object-cover" @if (! $foto) x-cloak @endif>
                <span x-show="!src" @if ($foto) x-cloak @endif
                      class="flex size-[88px] items-center justify-center rounded-full bg-red-600 text-3xl font-semibold text-white" aria-hidden="true">{{ $user['inisial'] }}</span>
                {{-- Tombol pensil: membuka modal Ubah foto profil (di bagian bawah halaman ini) --}}
                <button type="button" aria-label="Ganti foto profil" x-on:click="$dispatch('buka-foto')"
                        class="absolute right-0 bottom-0 flex size-9 cursor-pointer items-center justify-center rounded-full border-2 border-white bg-blue-200 text-brand hover:bg-blue-300">
                    <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                </button>
            </div>

            <div class="flex min-w-0 flex-col gap-1">
                <div class="flex flex-wrap items-center gap-4">
                    <h2 class="text-2xl font-semibold">{{ $user['nama'] }}</h2>
                    <span class="rounded-full border border-green-200 bg-green-100 px-2.5 py-0.5 text-[13px] text-green-700">{{ $user['status'] }}</span>
                </div>
                <p class="text-xl text-gray-400">NIP {{ $user['nip'] }}</p>
            </div>
        </div>

        <dl class="grid grid-cols-1 gap-5 rounded-lg bg-blue-50 px-5 py-5 sm:grid-cols-2 lg:grid-cols-[1.8fr_0.8fr_1.4fr_0.8fr]">
            @foreach ($info as $label => $nilai)
                <div class="flex min-w-0 flex-col gap-1.5">
                    <dt class="text-lg text-gray-400">{{ $label }}</dt>
                    <dd class="text-xl font-medium break-words">{{ $nilai }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Riwayat pangkat dan golongan --}}
    <section class="flex flex-col gap-8 rounded-lg bg-white p-5 shadow-sm">
        <h2 class="text-2xl font-medium">Riwayat Pangkat dan Golongan</h2>

        <div class="overflow-x-auto rounded-md border border-gray-300">
            <table class="w-full min-w-[720px] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th scope="col" class="w-1/4 border-b border-gray-300 px-2.5 py-3 font-medium">Pangkat</th>
                        <th scope="col" class="w-1/4 border-b border-gray-300 px-2.5 py-3 font-medium">Golongan</th>
                        <th scope="col" class="w-1/4 border-b border-gray-300 px-2.5 py-3 font-medium">Nomor SK</th>
                        <th scope="col" class="w-1/4 border-b border-gray-300 px-2.5 py-3 font-medium">Tanggal SK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($riwayat as $r)
                        <tr class="border-b border-gray-300 last:border-b-0">
                            <td class="px-2.5 py-3">
                                <span class="flex flex-wrap items-center gap-2">
                                    {{ $r['pangkat'] }}
                                    @if ($r['sekarang'])
                                        <span class="{{ $badge }} py-px text-xs">Saat ini</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-2.5 py-3">{{ $r['golongan'] }}</td>
                            <td class="px-2.5 py-3">{{ $r['sk'] }}</td>
                            <td class="px-2.5 py-3">{{ $r['tanggal'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- Aktivitas dosen: tab berpindah dengan Alpine.js --}}
    <section class="flex flex-col gap-6 rounded-lg bg-white p-5 shadow-sm"
             x-data="{ tab: 'Penelitian', data: @js($aktivitas) }">
        <h2 class="text-2xl font-medium">Aktivitas Dosen</h2>

        <div role="tablist" aria-label="Jenis aktivitas" class="flex flex-wrap gap-x-2 border-b border-gray-200">
            @foreach (array_keys($aktivitas) as $nama)
                <button type="button" role="tab" id="tab-{{ $nama }}"
                        x-on:click="tab = '{{ $nama }}'"
                        :aria-selected="tab === '{{ $nama }}'"
                        :class="tab === '{{ $nama }}' ? 'border-brand text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-700'"
                        class="-mb-px flex cursor-pointer items-center gap-2 border-b-2 px-3 py-2.5 text-sm">
                    {{ $nama }}
                    <span x-show="tab === '{{ $nama }}'" x-cloak
                          class="flex size-5 items-center justify-center rounded-full bg-brand text-xs text-white"
                          x-text="data['{{ $nama }}'].length"></span>
                </button>
            @endforeach
        </div>

        <ul role="tabpanel" :aria-labelledby="`tab-${tab}`" class="flex flex-col">
            <template x-for="(a, i) in data[tab]" :key="tab + i">
                <li class="flex items-center justify-between gap-4 border-b border-gray-200 px-4 py-3.5 last:border-b-0">
                    <div class="flex min-w-0 flex-col gap-0.5">
                        <span class="truncate text-[17px]" x-text="a.judul"></span>
                        <span class="text-sm text-gray-500" x-text="a.meta"></span>
                    </div>
                    <span class="{{ $badge }} shrink-0" x-text="a.peran"></span>
                </li>
            </template>
        </ul>
    </section>

    {{-- MODAL UBAH FOTO PROFIL. Muncul saat tombol pensil di foto diklik, bukan halaman baru. --}}
    <div class="contents" x-data="ubahFoto(@js($foto))" x-on:buka-foto.window="buka()">
        <div x-show="terbuka" x-cloak
             x-on:keydown.escape.window="tutup()"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            {{-- klik area gelap = tutup --}}
            <div class="absolute inset-0" x-on:click="tutup()" aria-hidden="true"></div>

            <form role="dialog" aria-modal="true" aria-labelledby="judul-foto"
                  method="POST" action="#" enctype="multipart/form-data"
                  x-on:submit.prevent="simpan()"
                  class="relative flex max-h-full w-full max-w-2xl flex-col gap-6 overflow-y-auto rounded-xl bg-white p-6 shadow-xl sm:p-8">
                @csrf

                <div class="flex items-start justify-between gap-4">
                    <div class="flex flex-col gap-1.5">
                        <h2 id="judul-foto" class="text-3xl font-medium">Ubah foto profil</h2>
                        <p class="text-base text-gray-400">Pratinjau di bawah menunjukkan tampilan foto di profil Anda</p>
                    </div>
                    <button type="button" x-ref="tombolTutup" x-on:click="tutup()" aria-label="Tutup"
                            class="flex size-9 shrink-0 cursor-pointer items-center justify-center rounded-lg hover:bg-gray-100">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>
                    </button>
                </div>

                {{-- Pratinjau --}}
                <div class="flex justify-center py-2">
                    <template x-if="tampil">
                        <img :src="tampil" alt="Pratinjau foto profil" class="size-[200px] rounded-full object-cover">
                    </template>
                    <template x-if="!tampil">
                        <span class="flex size-[200px] items-center justify-center rounded-full bg-red-600 text-6xl font-semibold text-white" aria-hidden="true">{{ $user['inisial'] }}</span>
                    </template>
                </div>

                {{-- Pilih file --}}
                <div class="flex flex-col gap-2">
                    <label class="relative flex cursor-pointer flex-col items-center gap-2 rounded-lg border border-dashed border-brand bg-white px-4 py-8 text-center hover:bg-blue-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand">
                        <input type="file" name="foto" class="sr-only" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                               x-ref="berkas" x-on:change="pilih($event)">
                        <span class="flex items-center gap-2 text-base font-medium text-gray-500">
                            <svg class="size-6 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 18a4.5 4.5 0 0 1-.6-8.96A6 6 0 0 1 18 8.5a4 4 0 0 1-.5 7.97M12 20v-8M9 15l3-3 3 3"/></svg>
                            <span x-text="namaFile || 'Pilih foto dari perangkat'"></span>
                        </span>
                        <span class="text-sm text-gray-400">JPG atau PNG, maksimal 2 MB. Disarankan rasio 1:1</span>
                    </label>
                    <template x-if="error">
                        <p role="alert" class="text-sm text-red-600" x-text="error"></p>
                    </template>
                </div>

                {{-- Tombol: Simpan baru aktif setelah ada foto baru yang valid --}}
                <div class="flex flex-wrap items-center justify-end gap-3">
                    <button type="button" x-on:click="tutup()"
                            :class="adaFile ? 'border-red-500 bg-red-500 text-white hover:bg-red-600' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'"
                            class="h-12 cursor-pointer rounded-lg border px-6 text-base">Batal</button>
                    <button type="submit" :disabled="!adaFile"
                            class="h-12 rounded-lg bg-brand px-6 text-base text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400 disabled:hover:bg-gray-200">Simpan foto</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL UBAH KATA SANDI. Muncul saat tombol "Ubah Kata Sandi" diklik. Dua langkah: sandi baru, lalu kode OTP. --}}
    <div class="contents" x-data="ubahSandi(@js($urlSimpanSandi))" x-on:buka-sandi.window="buka()">
        <div x-show="terbuka" x-cloak x-on:keydown.escape.window="tutup()"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">

            {{-- Tidak ditutup lewat klik area gelap, supaya isian tidak hilang tanpa sengaja. --}}
            {{-- Tidak ada @csrf: formulir ini dikirim ke web master kelompok lain, bukan ke aplikasi ini. --}}
            <form x-ref="form" role="dialog" aria-modal="true" aria-label="Ubah Kata Sandi"
                  method="POST" action="{{ $urlSimpanSandi }}" autocomplete="off"
                  x-on:submit.prevent="langkah === 1 ? (bisaKirim && kirimOtp()) : (bisaSimpan && simpan())"
                  class="grid max-h-full w-full max-w-4xl overflow-x-hidden overflow-y-auto rounded-2xl bg-white shadow-2xl md:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">

                {{-- KIRI: judul + langkah --}}
                <div class="flex flex-col justify-between gap-10 bg-blue-50 p-6 sm:p-8">
                    <div class="flex flex-col gap-8">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium tracking-[0.2em] text-[#02215c] uppercase">Keamanan akun</p>
                            <h2 class="text-3xl font-medium">Ubah Kata Sandi</h2>
                        </div>

                        <ol class="flex flex-col">
                            <li class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full border-2 border-brand text-sm"
                                          :class="langkah > 1 ? 'bg-brand text-white' : 'bg-white text-brand'">
                                        <span x-show="langkah === 1">01</span>
                                        <svg x-show="langkah > 1" x-cloak class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                    </span>
                                    <span class="my-1 h-8 w-0.5 bg-brand"></span>
                                </div>
                                <div class="flex flex-col gap-1 pt-1.5">
                                    <span class="text-lg font-medium">Kata sandi baru</span>
                                    <span class="text-base">Buat kata sandi yang kuat</span>
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full border-2 bg-white text-sm"
                                      :class="langkah === 2 ? 'border-brand text-brand' : 'border-gray-300 text-gray-500'">02</span>
                                <div class="flex flex-col gap-1 pt-1.5" :class="langkah === 2 ? '' : 'text-gray-400'">
                                    <span class="text-lg font-medium">Verifikasi OTP</span>
                                    <span class="text-base">Konfirmasi dengan kode dari email Anda.</span>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 size-6 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v5.5c0 4.2-2.9 7.7-7 9.5-4.1-1.8-7-5.3-7-9.5V6z"/><path d="M9 12l2.2 2.2L15.5 10"/></svg>
                        <p class="text-base">Kata sandi lama tidak diperlukan. Identitas Anda dipastikan lewat kode OTP.</p>
                    </div>
                </div>

                {{-- KANAN --}}
                <div class="flex min-h-[420px] min-w-0 flex-col gap-5 p-6 sm:p-8">

                    {{-- LANGKAH 1: kata sandi baru --}}
                    <div x-show="langkah === 1" class="flex flex-1 flex-col gap-5">
                        <div class="flex flex-col gap-2">
                            <h3 class="text-3xl font-medium">Buat kata sandi baru</h3>
                            <p class="text-base text-gray-400">Gunakan kata sandi yang berbeda dari sebelumnya dan belum pernah dipakai di tempat lain.</p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="sandi-baru" class="text-base font-medium">Kata sandi baru</label>
                            <div class="relative">
                                <input id="sandi-baru" name="password" x-ref="sandi" x-model="sandi"
                                       :type="lihat1 ? 'text' : 'password'" autocomplete="new-password"
                                       placeholder="Masukkan kata sandi baru"
                                       class="h-12 w-full rounded-lg border border-gray-200 bg-white pr-12 pl-4 text-base placeholder:text-gray-400">
                                <button type="button" x-on:click="lihat1 = !lihat1"
                                        :aria-label="lihat1 ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" :aria-pressed="lihat1"
                                        class="absolute top-1/2 right-3 flex size-8 -translate-y-1/2 cursor-pointer items-center justify-center rounded-md text-gray-900 hover:bg-gray-100">
                                    <svg x-show="!lihat1" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/><path d="M4 4l16 16"/></svg>
                                    <svg x-show="lihat1" x-cloak class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>

                            {{-- Kekuatan sandi: 4 batang, terisi sesuai jumlah syarat yang terpenuhi --}}
                            <div class="grid grid-cols-4 gap-2" role="img" :aria-label="`Kekuatan kata sandi: ${skor} dari 4`">
                                <template x-for="n in 4" :key="n">
                                    <span class="h-1.5 rounded-full" :class="n <= skor ? warna : 'bg-gray-200'"></span>
                                </template>
                            </div>

                            <ul class="grid grid-cols-1 gap-x-6 gap-y-3 pt-2 sm:grid-cols-2">
                                <template x-for="s in syarat" :key="s.teks">
                                    <li class="flex items-center gap-3 text-base" :class="s.ok ? 'text-gray-900' : 'text-gray-400'">
                                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2"
                                              :class="s.ok ? 'border-green-500 bg-green-500 text-white' : 'border-gray-300'">
                                            <svg x-show="s.ok" class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                        </span>
                                        <span x-text="s.teks"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="sandi-konfirmasi" class="text-base font-medium">Konfirmasi kata sandi baru</label>
                            <div class="relative">
                                <input id="sandi-konfirmasi" name="password_confirmation" x-model="konfirmasi"
                                       :type="lihat2 ? 'text' : 'password'" autocomplete="new-password"
                                       placeholder="Masukkan kata sandi baru"
                                       class="h-12 w-full rounded-lg border border-gray-200 bg-white pr-12 pl-4 text-base placeholder:text-gray-400">
                                <button type="button" x-on:click="lihat2 = !lihat2"
                                        :aria-label="lihat2 ? 'Sembunyikan konfirmasi kata sandi' : 'Tampilkan konfirmasi kata sandi'" :aria-pressed="lihat2"
                                        class="absolute top-1/2 right-3 flex size-8 -translate-y-1/2 cursor-pointer items-center justify-center rounded-md text-gray-900 hover:bg-gray-100">
                                    <svg x-show="!lihat2" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/><path d="M4 4l16 16"/></svg>
                                    <svg x-show="lihat2" x-cloak class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            <p x-show="konfirmasi !== '' && !cocok" x-cloak role="alert" class="text-sm text-red-600">Konfirmasi belum sama dengan kata sandi baru.</p>
                        </div>

                        <p class="text-base text-gray-400">Kode OTP akan dikirim ke {{ $emailSamar }}.</p>
                    </div>

                    {{-- LANGKAH 2: kode OTP --}}
                    <div x-show="langkah === 2" x-cloak class="flex flex-1 flex-col gap-5">
                        <div class="flex flex-col gap-2">
                            <h3 class="text-3xl font-medium">Masukkan kode OTP</h3>
                            <p class="text-base text-gray-400">Kami mengirim kode 6 digit ke {{ $emailSamar }}. Kode berlaku 5 menit.</p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <span id="label-otp" class="text-base font-medium">Kode OTP</span>
                                <span class="text-base tabular-nums" :class="kedaluwarsa ? 'text-red-600' : ''" x-text="waktu" aria-live="off"></span>
                            </div>

                            <div class="flex gap-2 sm:gap-3" role="group" aria-labelledby="label-otp">
                                <template x-for="(d, i) in otp" :key="i">
                                    <input :id="`otp-${i}`" type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code"
                                           :aria-label="`Digit ${i + 1}`" :value="d"
                                           x-on:input="masuk($event, i)"
                                           x-on:keydown.backspace="hapus($event, i)"
                                           x-on:paste.prevent="tempel($event)"
                                           :class="d ? 'border-brand' : 'border-gray-300 focus:border-brand'"
                                           class="h-14 w-0 min-w-0 flex-1 rounded-xl border-2 bg-white text-center text-3xl font-medium sm:h-16">
                                </template>
                            </div>
                            <input type="hidden" name="otp" :value="otp.join('')">

                            <p x-show="kedaluwarsa" x-cloak role="alert" class="text-sm text-red-600">Kode sudah kedaluwarsa. Silakan kirim ulang kode.</p>
                            <p x-show="info && !kedaluwarsa" x-cloak role="status" class="text-sm text-gray-500" x-text="info"></p>
                        </div>

                        <div>
                            <button type="button" x-on:click="kirimUlang()"
                                    class="cursor-pointer text-base text-brand hover:underline">Kirim ulang kode</button>
                        </div>

                    </div>

                    {{-- Tombol --}}
                    <div class="mt-auto flex flex-wrap items-center justify-end gap-3 pt-4">
                        <button type="button" x-on:click="tutup()"
                                class="h-12 cursor-pointer rounded-lg bg-red-500 px-6 text-base text-white hover:bg-red-600">Batal</button>

                        <button x-show="langkah === 1" type="submit" :disabled="!bisaKirim"
                                class="h-12 rounded-lg bg-brand px-6 text-base text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400 disabled:hover:bg-gray-200">Kirim Kode OTP</button>
                        <button x-show="langkah === 2" x-cloak type="submit" :disabled="!bisaSimpan"
                                class="h-12 rounded-lg bg-brand px-6 text-base text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400 disabled:hover:bg-gray-200">Simpan Kata Sandi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('ubahSandi', (action) => ({
            terbuka: false,
            langkah: 1,                 // 1 = kata sandi baru, 2 = kode OTP
            sandi: '', konfirmasi: '',
            lihat1: false, lihat2: false,

            // Syarat kata sandi
            get syarat() {
                return [
                    { teks: 'Minimal 8 karakter', ok: this.sandi.length >= 8 },
                    { teks: 'Mengandung huruf',   ok: /[A-Za-z]/.test(this.sandi) },
                    { teks: 'Mengandung angka',   ok: /\d/.test(this.sandi) },
                    { teks: 'Mengandung simbol',  ok: /[^A-Za-z0-9]/.test(this.sandi) },
                ];
            },
            get skor() { return this.syarat.filter(s => s.ok).length; },
            get warna() { return ['', 'bg-red-500', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'][this.skor]; },
            get cocok() { return this.konfirmasi !== '' && this.konfirmasi === this.sandi; },
            get bisaKirim() { return this.skor === 4 && this.cocok; },

            // OTP
            otp: ['', '', '', '', '', ''],
            sisa: 300,                  // detik (5 menit)
            timer: null,
            info: '',
            selesai: false,
            get waktu() {
                const m = String(Math.floor(this.sisa / 60)).padStart(2, '0');
                const s = String(this.sisa % 60).padStart(2, '0');
                return `${m}:${s}`;
            },
            get kedaluwarsa() { return this.sisa === 0; },
            get bisaSimpan() { return this.otp.every(d => d !== '') && !this.kedaluwarsa && !this.selesai; },

            buka() {
                this.terbuka = true;
                this.$nextTick(() => this.$refs.sandi.focus());
            },
            tutup() {
                this.terbuka = false;
                clearInterval(this.timer);
                this.langkah = 1;
                this.sandi = ''; this.konfirmasi = '';
                this.lihat1 = false; this.lihat2 = false;
                this.otp = ['', '', '', '', '', ''];
                this.sisa = 300; this.info = ''; this.selesai = false;
            },

            mulaiTimer() {
                clearInterval(this.timer);
                this.sisa = 300;
                this.timer = setInterval(() => {
                    if (this.sisa > 0) this.sisa--;
                    if (this.sisa === 0) clearInterval(this.timer);
                }, 1000);
            },
            // Kirim kode: nanti backend / web master yang benar-benar mengirim email.
            kirimOtp() {
                this.langkah = 2;
                this.info = '';
                this.otp = ['', '', '', '', '', ''];
                this.mulaiTimer();
                this.$dispatch('tampil-alert', { tipe: 'info', judul: 'Kode verifikasi dikirim', pesan: 'Periksa email kamu. Kode berlaku 5 menit. (Simulasi: belum ada email yang terkirim.)' });
                this.$nextTick(() => document.getElementById('otp-0').focus());
            },
            kirimUlang() {
                this.otp = ['', '', '', '', '', ''];
                this.selesai = false;
                this.mulaiTimer();
                this.$dispatch('tampil-alert', { tipe: 'info', judul: 'Kode baru dikirim', pesan: 'Kode sebelumnya tidak berlaku lagi. (Simulasi.)' });
                this.$nextTick(() => document.getElementById('otp-0').focus());
            },

            // Kotak OTP: pindah otomatis, backspace, dan tempel 6 digit sekaligus
            masuk(event, i) {
                const digit = event.target.value.replace(/\D/g, '').slice(-1);
                this.otp[i] = digit;
                event.target.value = digit;
                if (digit && i < 5) document.getElementById(`otp-${i + 1}`).focus();
            },
            hapus(event, i) {
                if (this.otp[i] === '' && i > 0) {
                    this.otp[i - 1] = '';
                    document.getElementById(`otp-${i - 1}`).focus();
                }
            },
            tempel(event) {
                const angka = (event.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                if (!angka) return;
                angka.split('').forEach((d, i) => { this.otp[i] = d; });
                document.getElementById(`otp-${Math.min(angka.length, 5)}`).focus();
            },

            // Simpan Kata Sandi.
            // Kalau alamat web master sudah diisi ($urlSimpanSandi di atas), formulir dikirim ke sana
            // berisi password, password_confirmation, dan otp. Kalau masih '#', hanya simulasi.
            simpan() {
                if (action === '#') {
                    // Simulasi: modal ditutup, lalu alert sukses muncul
                    this.tutup();
                    this.$dispatch('tampil-alert', { tipe: 'sukses', judul: 'Kata sandi berhasil diubah', pesan: 'Simulasi: pengiriman ke web master belum tersambung.' });
                    return;
                }
                this.$refs.form.submit();
            },
        }));

        Alpine.data('ubahFoto', (fotoSekarang) => ({
            terbuka: false,
            fotoSekarang: fotoSekarang,   // foto yang sedang dipakai (null = inisial)
            pratinjau: '',                // foto baru yang dipilih
            namaFile: '',
            error: '',

            get adaFile() { return this.pratinjau !== ''; },
            get tampil() { return this.pratinjau || this.fotoSekarang; },

            buka() {
                this.terbuka = true;
                this.$nextTick(() => this.$refs.tombolTutup.focus());
            },
            tutup() {
                this.terbuka = false;
                this.reset();
            },
            reset() {
                if (this.pratinjau) URL.revokeObjectURL(this.pratinjau);
                this.pratinjau = '';
                this.namaFile = '';
                this.error = '';
                this.$refs.berkas.value = '';
            },
            pilih(event) {
                const file = event.target.files[0];
                if (!file) return;
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    this.reset();
                    this.error = 'Format harus JPG atau PNG.';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    this.reset();
                    this.error = 'Ukuran foto melebihi 2 MB.';
                    return;
                }
                if (this.pratinjau) URL.revokeObjectURL(this.pratinjau);
                this.error = '';
                this.namaFile = file.name;
                this.pratinjau = URL.createObjectURL(file);
            },

            // Simpan (simulasi): foto di halaman ikut berganti, tapi belum tersimpan ke server.
            // Setelah di-refresh, foto kembali seperti semula.
            simpan() {
                if (!this.adaFile) return;
                this.fotoSekarang = this.pratinjau;
                window.dispatchEvent(new CustomEvent('foto-diganti', { detail: this.pratinjau }));
                this.pratinjau = '';          // jangan di-revoke: URL ini sekarang dipakai foto profil
                this.namaFile = '';
                this.$refs.berkas.value = '';
                this.terbuka = false;
                this.$dispatch('tampil-alert', { tipe: 'sukses', judul: 'Foto profil berhasil diubah', pesan: 'Simulasi: setelah halaman di-refresh, foto kembali seperti semula.' });
            },
        }));
    });
</script>
@endpush