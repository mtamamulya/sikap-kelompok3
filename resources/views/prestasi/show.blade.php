@extends('layouts.app')

@section('title', 'Detail Prestasi')

@section('content')

@php
    // DATA TIRUAN. $id berasal dari alamat (/prestasi/{id}).
    // Nanti diganti data prestasi dari controller / database oleh tim backend.
    $p = [
        'sumber'        => 'Tanpa kegiatan sebelumnya', // atau nama kegiatan yang dipilih
        'nama'          => 'Juara 1 Lomba Karya Tulis Ilmiah Nasional 2026',
        'penyelenggara' => 'Institut Teknologi Sepuluh Nopember',
        'tingkat'       => 'Nasional',
        'jenis'         => 'Akademik',
        'mulai'         => '12 September 2026',
        'selesai'       => '14 September 2026',
        'capaian'       => 'Juara 1',
        'kategori'      => 'Teknologi',
        'bentuk'        => 'Tim', // Individu | Tim
        'deskripsi'     => 'Karya tulis tentang deteksi hoaks dengan machine learning.',
    ];

    $anggota = [
        ['nama' => 'Kuwat Santoso , S.T., M.Kom.', 'peran' => 'Ketua',   'status' => 'Dosen'],
        ['nama' => 'Ivan Sigit Santoso',           'peran' => 'Anggota', 'status' => 'Mahasiswa'],
        ['nama' => 'Anisa Aulia Nurusyifa',        'peran' => 'Anggota', 'status' => 'Mahasiswa'],
    ];

    // Dokumen. 'url' = alamat file untuk dilihat / diunduh ('#' = belum ada file sungguhan).
    $dokumen = [
        ['file' => 'Sertifikat juara 1.pdf', 'url' => '#', 'jenis' => 'Sertifikat'],
    ];

    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
@endphp

    {{-- Judul halaman + tombol kembali --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Detail Prestasi</h1>
            <p class="text-2xl text-gray-400">Dashboard / Prestasi / Detail Prestasi</p>
        </div>
        <a href="{{ route('prestasi.index') }}"
           class="mt-2 flex h-11 items-center gap-2 rounded-md bg-blue-100 px-4 text-base font-medium text-brand hover:bg-blue-200">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Halaman ini hanya untuk membaca, jadi tidak ada formulir atau tombol simpan --}}
    <div class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">

        {{-- 1. SUMBER KEGIATAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-sumber">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <h2 id="h-sumber" class="text-[15px] font-semibold">Sumber kegiatan</h2>
            </div>
            <dl><x-detail-field label="Sumber" :value="$p['sumber']" /></dl>
        </section>

        {{-- 2. INFORMASI PRESTASI --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-info">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">2</span>
                <h2 id="h-info" class="text-[15px] font-semibold">Informasi prestasi</h2>
            </div>

            <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="md:col-span-2"><x-detail-field label="Nama prestasi" :value="$p['nama']" /></div>
                <div class="md:col-span-2"><x-detail-field label="Penyelenggara" :value="$p['penyelenggara']" /></div>
                <x-detail-field label="Tingkat prestasi" :value="$p['tingkat']" icon="chevron" />
                <x-detail-field label="Jenis prestasi" :value="$p['jenis']" icon="chevron" />
                <x-detail-field label="Tanggal mulai" :value="$p['mulai']" icon="kalender" />
                <x-detail-field label="Tanggal selesai" :value="$p['selesai']" icon="kalender" />
                <x-detail-field label="Capaian / peringkat" :value="$p['capaian']" />
                <x-detail-field label="Kategori / bidang (opsional)" :value="$p['kategori']" />
                <x-detail-field label="Bentuk partisipasi" :value="$p['bentuk']" />
                <div class="md:col-span-2"><x-detail-field label="Deskripsi (opsional)" :value="$p['deskripsi']" multiline /></div>
            </dl>
        </section>

        {{-- 3. ANGGOTA TIM (hanya kalau bentuk partisipasi = Tim) --}}
        @if ($p['bentuk'] === 'Tim')
            <section class="{{ $sectionClass }}" aria-labelledby="h-anggota">
                <div class="flex items-center gap-2.5">
                    <span class="{{ $numClass }}">3</span>
                    <h2 id="h-anggota" class="text-[15px] font-semibold">Anggota tim</h2>
                </div>

                <div class="overflow-x-auto rounded-md border border-gray-300">
                    <table class="w-full min-w-[480px] border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th scope="col" class="w-[40%] px-2.5 py-2.5 font-medium">Nama</th>
                                <th scope="col" class="w-[30%] px-2.5 py-2.5 font-medium">Peran</th>
                                <th scope="col" class="w-[30%] px-2.5 py-2.5 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($anggota as $a)
                                <tr class="border-t border-gray-300">
                                    <td class="px-2.5 py-2.5">{{ $a['nama'] }}</td>
                                    <td class="px-2.5 py-2.5">{{ $a['peran'] }}</td>
                                    <td class="px-2.5 py-2.5">{{ $a['status'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- 4/3. DOKUMEN (nomor 4 kalau ada bagian Anggota tim, kalau tidak 3)
             Di halaman Detail tidak ada kotak unggah. File ditampilkan sebagai baris dengan tombol Lihat dan Unduh.
             Dokumen ditambah / diganti di halaman Edit. --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-dokumen">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">{{ $p['bentuk'] === 'Tim' ? 4 : 3 }}</span>
                <h2 id="h-dokumen" class="text-[15px] font-semibold">Dokumen</h2>
            </div>

            @forelse ($dokumen as $d)
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="bg-[#02215c] px-3.5 py-3 text-white">
                        <h3 class="text-sm font-semibold">Dokumen {{ $loop->iteration }}</h3>
                    </div>

                    <div class="flex flex-col gap-4 p-3.5">
                        {{-- File: nama file + tombol Lihat dan Unduh --}}
                        <div class="flex flex-col gap-2">
                            <span class="text-[15px] font-medium">File</span>
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-brand">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7.5A2.5 2.5 0 0 0 5 5.5v13A2.5 2.5 0 0 0 7.5 21h9a2.5 2.5 0 0 0 2.5-2.5V8zM14 3v5h5M9 13h6M9 17h6"/></svg>
                                    </span>
                                    <span class="truncate text-[15px]" title="{{ $d['file'] }}">{{ $d['file'] }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" x-data
                                       x-on:click="$dispatch('buka-preview', { file: @js($d['file']), url: @js($d['url']), jenis: @js($d['jenis'] ?? '') })"
                                       class="cursor-pointer flex h-9 items-center gap-1.5 rounded-lg bg-blue-100 px-3.5 text-sm font-medium text-brand hover:bg-blue-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Lihat
                                    </button>
                                    <a href="{{ $d['url'] }}" download="{{ $d['file'] }}"
                                       class="flex h-9 items-center gap-1.5 rounded-lg border border-brand bg-white px-3.5 text-sm font-medium text-brand hover:bg-blue-50">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11M7.5 10.5L12 15l4.5-4.5M5 19.5h14"/></svg>
                                        Unduh
                                    </a>
                                </div>
                            </div>
                        </div>

                        <dl><x-detail-field label="Jenis dokumen" :value="$d['jenis']" icon="chevron" /></dl>
                    </div>
                </div>
            @empty
                <p class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">Belum ada dokumen pendukung.</p>
            @endforelse
        </section>
    </div>

@endsection