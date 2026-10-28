@extends('layouts.app')

@section('title', 'Dashboard - Teknologi Rekayasa Komputer')

@section('content')
<div class="flex flex-col gap-6 p-4 md:px-6 md:py-8 lg:px-12 bg-[#F9FAFB]">
    
    <!-- Hero Profile Card (Dengan Efek Fill Transition Kiri-ke-Kanan & Kanan-ke-Kiri) -->
    <div class="group relative overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-8 shadow-[0_2px_10px_rgba(0,0,0,0.03)] transition-all duration-500 hover:border-[#2277FF]">
        
        <!-- Layer Animasi Warna Biru (Meluncur Masuk dari Kiri, Keluar ke Kiri) -->
        <div class="pointer-events-none absolute inset-0 z-0 bg-gradient-to-r from-[#2277FF] to-[#1D4ED8] opacity-0 transition-all duration-500 ease-in-out [transform-origin:left_center] scale-x-0 group-hover:scale-x-100 group-hover:opacity-100"></div>

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
        <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
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
                    {{-- 7 kolom sama lebar. Di HP memenuhi lebar grafik, di layar lebar dipersempit supaya batang tidak terlalu renggang. --}}
                    <div class="absolute inset-y-0 left-1/2 grid w-full -translate-x-1/2 grid-cols-7 items-end sm:w-[72%]">
                        @foreach ($perTahun as $tahun => $jumlah)
                            <div class="group flex h-full min-w-0 flex-col items-center justify-end">
                                {{-- tinggi batang dihitung dari data, jadi pakai style inline --}}
                                <div class="relative w-3/5 max-w-12" style="height: {{ $jumlah / $max * 100 }}%">
                                    <span class="pointer-events-none absolute bottom-full left-1/2 mb-1.5 -translate-x-1/2 rounded bg-white px-2 py-0.5 text-xs whitespace-nowrap opacity-0 shadow-md group-hover:opacity-100">
                                        {{ $jumlah }} Kegiatan
                                    </span>
                                    <div class="size-full rounded-t bg-blue-500 group-hover:bg-brand"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

                {{-- Label tahun --}}
                {{-- Di HP tahun dipendekkan ('22) supaya tidak saling menumpuk --}}
                <div class="mx-auto grid w-full grid-cols-7 sm:w-[72%]">
                    @foreach ($perTahun as $tahun => $jumlah)
                        <div class="min-w-0 text-center text-xs text-gray-600" aria-label="{{ $tahun }}">
                            <span class="sm:hidden" aria-hidden="true">'{{ substr((string) $tahun, 2) }}</span>
                            <span class="max-sm:hidden">{{ $tahun }}</span>
                        </div>
                    @endforeach
                </div>
                <span class="text-3xl font-bold text-[#2277FF]">12</span>
            </div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_2px_8px_rgba(0,0,0,0.04)]">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-[13px] font-semibold text-[#6B7280]">Total kegiatan</span>
                    <span class="rounded-md bg-[#FEF3C7] px-2 py-0.5 text-[11px] font-semibold text-[#D97706]">3 kegiatan</span>
                </div>
                <span class="text-3xl font-bold text-[#D97706]">3</span>
            </div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_2px_8px_rgba(0,0,0,0.04)]">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-[13px] font-semibold text-[#6B7280]">Prestasi mahasiswa</span>
                    <span class="rounded-md bg-[#ECFDF5] px-2 py-0.5 text-[11px] font-semibold text-[#059669]">Semester ini</span>
                </div>
                <span class="text-3xl font-bold text-[#059669]">2</span>
            </div>
        </div>
    </div>

    <!-- Section Panduan Singkat Proses Pengajuan -->
    <section class="flex flex-col gap-6 rounded-2xl border border-[#E5E7EB] bg-white p-6 shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-[#111827]">Panduan singkat proses pengajuan</h2>
            <p class="text-xs text-[#6B7280]">Ikuti langkah-langkah berikut agar pengajuan dokumen Anda dapat diproses dengan cepat dan tepat.</p>
        </div>

        <!-- Progress Steps Line -->
        <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:gap-4">
            <div class="absolute top-4 left-6 right-6 hidden h-[2px] bg-[#E5E7EB] sm:block"></div>

            <div class="relative z-10 flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-xs font-bold text-[#2277FF]">1</span>
                <span class="text-xs font-semibold text-[#111827]">Pilih jenis dokumen</span>
            </div>

            <div class="relative z-10 flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-xs font-bold text-[#2277FF]">2</span>
                <span class="text-xs font-semibold text-[#111827]">Lengkapi data diri</span>
            </div>

            <div class="relative z-10 flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-xs font-bold text-[#2277FF]">3</span>
                <span class="text-xs font-semibold text-[#111827]">Unggah lampiran</span>
            </div>

            <div class="relative z-10 flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-xs font-bold text-[#2277FF]">4</span>
                <span class="text-xs font-semibold text-[#111827]">Periksa dan kirim</span>
            </div>
        </div>

        <!-- Banner Buat Pengajuan Baru -->
        <div class="flex flex-col gap-4 rounded-2xl bg-[#2277FF] p-6 text-white sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-1">
                <h3 class="text-xl font-bold">Buat pengajuan dokumen baru</h3>
                <p class="text-xs text-blue-100">Pilih jenis dokumen, lengkapi data, dan unggah lampiran yang diperlukan untuk memulai proses pengajuan.</p>
            </div>
            <a href="#" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-white px-6 py-3 text-xs font-bold text-[#2277FF] hover:bg-blue-50 transition shadow-sm">
                Buat Pengajuan
            </a>
        </div>
    </section>

    <!-- Section Daftar Pengajuan Terbaru -->
    <section class="flex flex-col gap-4 rounded-2xl border border-[#E5E7EB] bg-white p-6 shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-lg font-bold text-[#111827]">Daftar pengajuan terbaru</h2>
                <p class="text-xs text-[#6B7280]">Lihat status terbaru dari pengajuan dokumen Anda.</p>
            </div>
            <a href="#" class="rounded-lg bg-[#EFF6FF] px-4 py-2 text-xs font-semibold text-[#2563EB] hover:bg-blue-100 transition">
                Lihat semua
            </a>
        </div>

        <div class="flex items-center justify-between rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-4 transition hover:bg-gray-100/80">
            <div class="flex items-center gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[#DBEAFE] text-xs font-bold text-[#2563EB]">
                    PJ
                </div>
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs font-bold text-[#111827]">Pengajuan proposal penelitian internal</span>
                    <span class="text-[11px] text-[#6B7280]">Proposal penelitian internal tentang pengembangan model pembelajaran berbasis teknologi.</span>
                </div>
            </div>
            
            <div class="flex flex-col items-end gap-1">
                <span class="rounded-full bg-[#ECFDF5] px-2.5 py-0.5 text-[10px] font-semibold text-[#047857]">
                    Diproses
                </span>
                <span class="text-[11px] text-[#9CA3AF]">12 Mar 2024</span>
            </div>
        </div>
    </section>

    {{-- Tabel menunggu verifikasi --}}
    <section class="flex flex-col gap-5 rounded-lg bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-medium">Kegiatan Menunggu Verifikasi</h2>
            <a href="{{ route('pengajuan.dosen') }}" class="px-3 py-2 text-[15px] text-brand hover:underline">Lihat semua</a>
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
                                {{-- Semua baris sementara membuka contoh Detail Pengajuan Dosen kondisi Direview (id 2). Nanti diganti id kegiatan yang sebenarnya. --}}
                                <a href="{{ route('pengajuan.dosen.show', 2) }}" aria-label="Lihat detail {{ $k['nama'] }}"
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