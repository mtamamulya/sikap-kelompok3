@extends('layouts.app')

@section('title', 'Detail Pengajaran')

@section('content')

@php
    // $p (data pengajaran) dan $dokumen dikirim oleh DosenPengajaranController@show.

    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
@endphp

    {{-- Judul halaman + tombol kembali --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Detail Pengajaran</h1>
            <p class="text-2xl text-gray-400">Dashboard / Pengajaran / Detail Pengajaran</p>
        </div>
        <a href="{{ route('pengajaran.index') }}"
           class="mt-2 flex h-11 items-center gap-2 rounded-md bg-blue-100 px-4 text-base font-medium text-brand hover:bg-blue-200">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Halaman ini hanya untuk membaca, jadi tidak ada formulir atau tombol simpan --}}
    <div class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">

        {{-- 1. KELAS DAN PERIODE --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-kelas">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <h2 id="h-kelas" class="text-[15px] font-semibold">Kelas dan Periode</h2>
            </div>

            <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <x-detail-field label="Tahun akademik" :value="$p['tahun_akademik']" icon="chevron" />
                <x-detail-field label="Semester" :value="$p['semester']" locked />
                <x-detail-field label="Program studi" :value="$p['prodi']" icon="chevron" />
                <x-detail-field label="Kelas" :value="$p['kelas']" icon="chevron" />
            </dl>
        </section>

        {{-- 2. MATA KULIAH DAN DOSEN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-matkul">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">2</span>
                <h2 id="h-matkul" class="text-[15px] font-semibold">Mata Kuliah dan Dosen</h2>
            </div>

            <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <x-detail-field label="Mata kuliah" :value="$p['matkul']" icon="chevron" />
                <x-detail-field label="Dosen pengampu" :value="$p['dosen']" icon="chevron" locked />
            </dl>

            {{-- Rincian mata kuliah --}}
            <div class="overflow-x-auto rounded-md border border-gray-300">
                <table class="w-full min-w-[520px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">Kode</th>
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">SKS</th>
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">Dosen Pengampu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-300">
                            <td class="px-2.5 py-2.5">{{ $p['kode'] }}</td>
                            <td class="px-2.5 py-2.5">{{ $p['sks'] }}</td>
                            <td class="px-2.5 py-2.5">{{ $p['dosen_lengkap'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-[15px] text-gray-400">Dosen pengampu terisi otomatis dari akun yang login.</p>
        </section>

        {{-- Ringkasan --}}
        <div role="status" class="flex items-center gap-2.5 rounded-lg bg-blue-100 px-4 py-3.5 text-sm text-brand">
            <svg class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5.5M12 7.5v.5"/></svg>
            <span>Ringkasan : {{ $p['matkul'] }}, kelas {{ str_replace('Kelas ', '', $p['kelas']) }}, {{ $p['tahun_akademik'] }}, {{ $p['dosen_lengkap'] }}</span>
        </div>

        {{-- 3. DOKUMEN PENDUKUNG
             Di halaman Detail tidak ada kotak unggah. Dokumen ditampilkan sebagai tautan yang dibuka di tab baru (tidak ada unggah file).
             Dokumen ditambah / diganti di halaman Edit. --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-dokumen">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">3</span>
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
                            <span class="text-[15px] font-medium">Tautan dokumen</span>
                            @if (! empty($d['tautan']))
                                <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-brand">
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1 1M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1-1"/></svg>
                                        </span>
                                        <span class="truncate text-[15px]" title="{{ $d['tautan'] }}">{{ $d['tautan'] }}</span>
                                    </div>
                                    <a href="{{ $d['tautan'] }}" target="_blank" rel="noopener noreferrer"
                                       class="flex h-9 items-center gap-1.5 rounded-lg bg-brand px-3.5 text-sm font-medium text-white hover:bg-blue-700">
                                        Buka dokumen
                                        <svg class="size-[16px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4h6v6M20 4l-9 9M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"/></svg>
                                        <span class="sr-only">(dibuka di tab baru)</span>
                                    </a>
                                </div>
                            @else
                                <p class="rounded-lg border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500">Belum ada tautan dokumen.</p>
                            @endif
                        </div>

                        <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                            <x-detail-field label="Nama dokumen" :value="$d['nama']" />
                            <x-detail-field label="Jenis dokumen" :value="$d['jenis']" icon="chevron" />
                            <!-- <div class="md:col-span-2"><x-detail-field label="Keterangan" :value="$d['keterangan']" multiline /></div> -->
                        </dl>
                    </div>
                </div>
            @empty
                <p class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">Belum ada dokumen pendukung.</p>
            @endforelse
        </section>
    </div>

@endsection