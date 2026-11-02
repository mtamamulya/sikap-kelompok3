@extends('layouts.app')

@section('title', 'Detail Pengabdian')

@section('content')

@php
    // DATA TIRUAN. $id berasal dari alamat (/pengabdian/{id}).
    // Nanti diganti data pengabdian dari controller / database oleh tim backend.
    $p = [
        'judul'          => 'Seminar Nasional AI',
        'afiliasi'       => 'Jurusan Teknik Elektro',
        'skim'           => 'Pengabdian Masyarakat',
        'ranah'          => 'Pengabdian Masyarakat',
        'bidang'         => 'Energi',
        'sebelumnya'     => 'Tidak ada, Pengabdian baru',
        'tahun_anggaran' => 2026,
        'tahun_ke'       => 1,
        'lama'           => 1,
        'lokasi'         => 'Kabupaten Kendal',
        'nomor_sk'       => '39/SK-REK/SP/1/2022',
        'tanggal_sk'     => '04/06/2026',
        'dana_dikti'     => 'Rp 1.000.000',
        'dana_pt'        => 'Rp 1.000.000',
        'dana_lain'      => 'Rp 1.000.000',
        'in_kind'        => null,
        'mitra'          => null,
    ];

    $anggota = [
        ['nama' => 'Kuwat Santoso',         'peran' => 'Ketua',   'status' => 'Dosen'],
        ['nama' => 'Wiktasari',             'peran' => 'Anggota', 'status' => 'Dosen'],
        ['nama' => 'Mukhlis Pratama Mulya', 'peran' => 'Anggota', 'status' => 'Mahasiswa'],
    ];

    // Dokumen pendukung. 'url' = alamat file untuk dilihat / diunduh ('#' = belum ada file sungguhan).
    $dokumen = [
        [
            'file'       => 'Proposal pengabdian.pdf',
            'url'        => '#',
            'nama'       => 'Proposal Pengabdian',
            'jenis'      => 'Proposal',
            'keterangan' => 'Ini adalah proposal pengabdian masyarakat',
            'tautan'     => null,
        ],
    ];

    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
@endphp

    {{-- Judul halaman + tombol kembali --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-4xl font-bold tracking-tight">Detail Pengabdian</h1>
            <p class="text-2xl text-gray-400">Dashboard / Pengabdian / Detail Pengabdian</p>
        </div>
        <a href="{{ route('pengabdian.index') }}"
           class="mt-2 flex h-11 items-center gap-2 rounded-md bg-blue-100 px-4 text-base font-medium text-brand hover:bg-blue-200">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Halaman ini hanya untuk membaca, jadi tidak ada formulir atau tombol simpan --}}
    <div class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">

        {{-- 1. INFORMASI PENGABDIAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-info">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <h2 id="h-info" class="text-[15px] font-semibold">Informasi Pengabdian</h2>
            </div>

            <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="md:col-span-2"><x-detail-field label="Judul pengabdian" :value="$p['judul']" /></div>
                <div class="md:col-span-2"><x-detail-field label="Afiliasi" :value="$p['afiliasi']" /></div>

                <x-detail-field label="Skim" :value="$p['skim']" icon="chevron" />
                <x-detail-field label="Ranah" :value="$p['ranah']" />
                <x-detail-field label="Kelompok bidang" :value="$p['bidang']" icon="chevron" />
                <x-detail-field label="Pengabdian sebelumnya (opsional)" :value="$p['sebelumnya']" icon="chevron" />
            </dl>
        </section>

        {{-- 2. PELAKSANAAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-laksana">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">2</span>
                <h2 id="h-laksana" class="text-[15px] font-semibold">Pelaksanaan</h2>
            </div>

            <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-3">
                <x-detail-field label="Tahun anggaran" :value="$p['tahun_anggaran']" icon="chevron" />
                <x-detail-field label="Tahun pelaksanaan ke-" :value="$p['tahun_ke']" />
                <x-detail-field label="Lama kegiatan (tahun)" :value="$p['lama']" />

                <div class="md:col-span-3"><x-detail-field label="Lokasi" :value="$p['lokasi']" /></div>

                <div class="md:col-span-3 md:grid md:grid-cols-2 md:gap-4.5">
                    <x-detail-field label="Nomor SK penugasan" :value="$p['nomor_sk']" />
                    <div class="mt-4.5 md:mt-0"><x-detail-field label="Tanggal SK penugasan" :value="$p['tanggal_sk']" icon="kalender" /></div>
                </div>
            </dl>
        </section>

        {{-- 3. PENDANAAN DAN MITRA --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-dana">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">3</span>
                <h2 id="h-dana" class="text-[15px] font-semibold">Pendanaan dan Mitra</h2>
            </div>

            <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <x-detail-field label="Dana DIKTI" :value="$p['dana_dikti']" />
                <x-detail-field label="Dana perguruan tinggi" :value="$p['dana_pt']" />
                <x-detail-field label="Dana institusi lain" :value="$p['dana_lain']" />
                <x-detail-field label="In kind" :value="$p['in_kind']" />
                <div class="md:col-span-2"><x-detail-field label="Mitra (opsional)" :value="$p['mitra']" /></div>
            </dl>
        </section>

        {{-- 4. ANGGOTA PENGABDIAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-anggota">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">4</span>
                <h2 id="h-anggota" class="text-[15px] font-semibold">Anggota Pengabdian</h2>
            </div>

            <div class="overflow-x-auto rounded-md border border-gray-300">
                <table class="w-full min-w-[520px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">Nama</th>
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">Peran</th>
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">Status</th>
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

        {{-- 5. DOKUMEN PENDUKUNG
             Di halaman Detail tidak ada kotak unggah. File ditampilkan sebagai baris dengan tombol Lihat dan Unduh.
             Dokumen ditambah / diganti di halaman Edit. --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-dokumen">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">5</span>
                <h2 id="h-dokumen" class="text-[15px] font-semibold">Dokumen Pendukung</h2>
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
                                    <a href="{{ $d['url'] }}" target="_blank" rel="noopener noreferrer"
                                       class="flex h-9 items-center gap-1.5 rounded-lg bg-blue-100 px-3.5 text-sm font-medium text-brand hover:bg-blue-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Lihat
                                    </a>
                                    <a href="{{ $d['url'] }}" download="{{ $d['file'] }}"
                                       class="flex h-9 items-center gap-1.5 rounded-lg border border-brand bg-white px-3.5 text-sm font-medium text-brand hover:bg-blue-50">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11M7.5 10.5L12 15l4.5-4.5M5 19.5h14"/></svg>
                                        Unduh
                                    </a>
                                </div>
                            </div>
                        </div>

                        <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                            <x-detail-field label="Nama dokumen" :value="$d['nama']" />
                            <x-detail-field label="Jenis dokumen" :value="$d['jenis']" icon="chevron" />
                            <div class="md:col-span-2"><x-detail-field label="Keterangan" :value="$d['keterangan']" multiline /></div>
                            <div class="md:col-span-2"><x-detail-field label="Tautan dokumen (opsional)" :value="$d['tautan']" :href="$d['tautan']" /></div>
                        </dl>
                    </div>
                </div>
            @empty
                <p class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">Belum ada dokumen pendukung.</p>
            @endforelse
        </section>
    </div>

@endsection