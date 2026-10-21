@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    // DATA TIRUAN. Nanti diganti data dari controller / database oleh tim backend.
    $stats = [
        ['label' => 'Pengajaran',          'value' => 24, 'aria' => 'Buka pengajaran',                   'alert' => false],
        ['label' => 'Penelitian',          'value' => 67, 'aria' => 'Buka penelitian',                   'alert' => false],
        ['label' => 'Pengabdian',          'value' => 32, 'aria' => 'Buka pengabdian',                   'alert' => false],
        ['label' => 'Menunggu Verifikasi', 'value' => 24, 'aria' => 'Buka kegiatan menunggu verifikasi', 'alert' => true],
    ];

    $perTahun = ['2022' => 8, '2023' => 17, '2024' => 9, '2025' => 20, '2026' => 14, '2027' => 16, '2028' => 7];
    $max   = 20;
    $ticks = [0, 5, 10, 15, 20];

    $menunggu = [
        ['nama' => 'Seminar Nasional AI',  'jenis' => 'Penelitian', 'tanggal' => '15 Agustus 2026'],
        ['nama' => 'Pelatihan Industri',   'jenis' => 'Pengabdian', 'tanggal' => '12 Agustus 2026'],
        ['nama' => 'Kunjungan Industri',   'jenis' => 'Pengabdian', 'tanggal' => '26 Juli 2026'],
        ['nama' => 'Pelatihan Industri',   'jenis' => 'Pengabdian', 'tanggal' => '11 Juli 2026'],
        ['nama' => 'Kuliah Tamu',          'jenis' => 'Pengajaran', 'tanggal' => '10 Juli 2026'],
    ];
@endphp

    {{-- Judul halaman --}}
    <div class="flex flex-col gap-1.5">
        <h1 class="text-4xl font-bold tracking-tight">Dashboard</h1>
        <p class="text-2xl text-gray-400">Ringkasan kegiatan Anda</p>
    </div>

    {{-- Kartu ringkasan --}}
    <div class="flex flex-wrap gap-6">
        @foreach ($stats as $s)
            <div class="flex min-h-41 min-w-0 flex-[1_1_220px] flex-col justify-between rounded-lg bg-white p-3 shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <span class="text-xl font-medium">{{ $s['label'] }}</span>
                    <a href="#" aria-label="{{ $s['aria'] }}" class="flex rounded-full text-gray-900 hover:text-brand">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9.5"/><path d="M9 15l6-6M10 9h5v5"/>
                        </svg>
                    </a>
                </div>
                <div class="text-5xl leading-none font-bold {{ $s['alert'] ? 'text-red-600' : '' }}">{{ $s['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Grafik batang --}}
    <section class="flex flex-col gap-5 rounded-lg bg-white p-4 shadow-sm">
        <h2 class="text-xl font-medium">Kegiatan Saya Per Tahun</h2>

        <div class="flex gap-2">
            {{-- Angka sumbu Y --}}
            <div class="relative h-70 w-7 shrink-0" aria-hidden="true">
                @foreach ($ticks as $t)
                    <span class="absolute right-0 translate-y-1/2 text-xs text-gray-600"
                          style="bottom: {{ $t / $max * 100 }}%">{{ $t }}</span>
                @endforeach
            </div>

            <div class="flex min-w-0 flex-1 flex-col gap-2.5">
                <div class="relative h-70">
                    {{-- Garis bantu --}}
                    @foreach ($ticks as $t)
                        <div class="absolute inset-x-0 h-px bg-gray-200" style="bottom: {{ $t / $max * 100 }}%"></div>
                    @endforeach

                    {{-- Batang --}}
                    <div class="absolute inset-0 flex items-end justify-center">
                        @foreach ($perTahun as $tahun => $jumlah)
                            <div class="group flex h-full w-[10.28%] shrink-0 flex-col items-center justify-end">
                                {{-- tinggi batang dihitung dari data, jadi pakai style inline --}}
                                <div class="relative w-12 max-w-[70%]" style="height: {{ $jumlah / $max * 100 }}%">
                                    <span class="pointer-events-none absolute bottom-full left-1/2 mb-1.5 -translate-x-1/2 rounded bg-white px-2 py-0.5 text-xs whitespace-nowrap opacity-0 shadow-md group-hover:opacity-100">
                                        {{ $jumlah }} Kegiatan
                                    </span>
                                    <div class="size-full rounded-t bg-blue-500 group-hover:bg-brand"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Label tahun --}}
                <div class="flex justify-center">
                    @foreach ($perTahun as $tahun => $jumlah)
                        <div class="w-[10.28%] shrink-0 text-center text-xs text-gray-600">{{ $tahun }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Tabel menunggu verifikasi --}}
    <section class="flex flex-col gap-5 rounded-lg bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-medium">Kegiatan Menunggu Verifikasi</h2>
            <a href="#" class="px-3 py-2 text-[15px] text-brand hover:underline">Lihat semua</a>
        </div>

        <div class="overflow-x-auto rounded-md border border-gray-300">
            <table class="w-full min-w-[640px] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th scope="col" class="border-b border-gray-300 px-2 py-2.5 font-medium">Nama Kegiatan</th>
                        <th scope="col" class="border-b border-gray-300 px-2 py-2.5 font-medium">Jenis</th>
                        <th scope="col" class="border-b border-gray-300 px-2 py-2.5 font-medium">Tanggal Diajukan</th>
                        <th scope="col" class="border-b border-gray-300 px-2 py-2.5 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menunggu as $k)
                        <tr class="border-b border-gray-300 last:border-b-0">
                            <td class="px-2 py-2">{{ $k['nama'] }}</td>
                            <td class="px-2 py-2">{{ $k['jenis'] }}</td>
                            <td class="px-2 py-2">{{ $k['tanggal'] }}</td>
                            <td class="px-2 py-1">
                                <a href="#" aria-label="Lihat detail {{ $k['nama'] }}"
                                   class="flex size-10 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

@endsection