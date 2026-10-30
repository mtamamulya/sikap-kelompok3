@extends('layouts.app')

@section('title', 'Detail Penelitian')

@section('content')

@php
    // DATA TIRUAN. $id berasal dari alamat (/penelitian/{id}).
    // Nanti diganti data penelitian dari controller / database oleh tim backend.
    $p = [
        'judul'          => 'Deteksi hoaks dengan ML',
        'afiliasi'       => 'Jurusan Teknik Elektro',
        'skim'           => 'Penelitian Terapan',
        'ranah'          => 'Penelitian Terapan',
        'bidang'         => 'Energi',
        'sebelumnya'     => 'Tidak ada, Penelitian baru',
        'tahun_anggaran' => 2026,
        'tahun_ke'       => 1,
        'lama'           => 1,
        'lokasi'         => 'Kota Semarang',
        'nomor_sk'       => '4389/UN/KP/2026',
        'tanggal_sk'     => '04/06/2026',
        'dana_dikti'     => 'Rp 1.000.000',
        'dana_pt'        => 'Rp 1.000.000',
        'dana_lain'      => 'Rp 1.000.000',
        'in_kind'        => null,
        'mitra'          => null,
    ];

    $anggota = [
        ['nama' => 'Kuwat Santoso',         'peran' => 'Ketua',   'status' => 'Dosen',     'kunci' => true],
        ['nama' => 'Wiktasari',             'peran' => 'Anggota', 'status' => 'Dosen',     'kunci' => false],
        ['nama' => 'Mukhlis Pratama Mulya', 'peran' => 'Anggota', 'status' => 'Mahasiswa', 'kunci' => false],
    ];

    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
@endphp

    {{-- Judul halaman + tombol kembali --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Detail Penelitian</h1>
            <p class="text-2xl text-gray-400">Dashboard / Penelitian / Detail Penelitian</p>
        </div>
        <a href="{{ route('penelitian.index') }}"
           class="mt-2 flex h-11 items-center gap-2 rounded-md bg-blue-100 px-4 text-base font-medium text-brand hover:bg-blue-200">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Halaman ini hanya untuk membaca, jadi tidak ada formulir atau tombol simpan --}}
    <div class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">

        {{-- 1. INFORMASI PENELITIAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-info">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <h2 id="h-info" class="text-[15px] font-semibold">Informasi Penelitian</h2>
            </div>

            <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="md:col-span-2"><x-detail-field label="Judul penelitian" :value="$p['judul']" /></div>
                <div class="md:col-span-2"><x-detail-field label="Afiliasi" :value="$p['afiliasi']" /></div>

                <x-detail-field label="Skim" :value="$p['skim']" icon="chevron" />
                <x-detail-field label="Ranah" :value="$p['ranah']" />
                <x-detail-field label="Kelompok bidang" :value="$p['bidang']" icon="chevron" />
                <x-detail-field label="Penelitian sebelumnya (opsional)" :value="$p['sebelumnya']" icon="chevron" />
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

        {{-- 4. ANGGOTA PENELITIAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-anggota">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">4</span>
                <h2 id="h-anggota" class="text-[15px] font-semibold">Anggota Penelitian</h2>
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
    </div>

@endsection