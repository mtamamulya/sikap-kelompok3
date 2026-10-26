@extends('layouts.app')

@section('title', 'Dashboard - Teknologi Rekayasa Komputer')

@section('content')
@php
    $summary = [
        ['title' => 'Total pengajuan', 'badge' => 'Bulan ini', 'value' => '12', 'color' => 'text-[#2563EB]'],
        ['title' => 'Total kegiatan', 'badge' => '3 kegiatan', 'value' => '3', 'color' => 'text-[#92400E]'],
        ['title' => 'Prestasi mahasiswa', 'badge' => 'Semester ini', 'value' => '2', 'color' => 'text-[#047857]'],
    ];

    $steps = ['Pilih jenis dokumen', 'Lengkapi data diri', 'Unggah lampiran', 'Periksa dan kirim'];
    $card = "rounded-2xl border border-[#E5E7EB] bg-white shadow-[0_4px_12px_rgba(0,0,0,0.05)]";
@endphp

<div class="flex flex-col gap-5 p-4 md:px-6 md:py-8 lg:px-12">
    <!-- Profile Card -->
    <section class="{{ $card }} flex flex-wrap items-center gap-5 p-6">
        <div class="flex size-[72px] items-center justify-center rounded-full bg-[#EFF6FF] text-[28px] font-bold text-[#2563EB]">
            A
        </div>
        <div class="flex min-w-[220px] flex-1 flex-col gap-3">
            <div class="flex items-center justify-between gap-3">
                <h1 class="text-2xl font-bold">Arya</h1>
                <span class="rounded-full bg-[#EFF6FF] px-3 py-1.5 text-sm font-semibold text-[#2563EB]">Mahasiswa</span>
            </div>
            <p class="text-base text-[#6B7280]">NIM 43325115</p>
            <div class="flex flex-wrap gap-5">
                <div class="flex flex-col gap-1">
                    <span class="text-[13px] text-[#9CA3AF]">Status akun</span>
                    <span class="font-semibold text-[#80CC16]">Aktif</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[13px] text-[#9CA3AF]">Terakhir login</span>
                    <span class="font-semibold">12 Maret 2026, 09:15 WIB</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Summary Cards -->
    <section class="grid gap-4 md:grid-cols-3">
        @foreach($summary as $s)
            <div class="{{ $card }} flex flex-col gap-3 p-5">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-semibold">{{ $s['title'] }}</span>
                    <span class="rounded-full bg-[#EFF6FF] px-2.5 py-1.5 text-xs font-semibold text-[#2563EB]">{{ $s['badge'] }}</span>
                </div>
                <span class="text-[32px] font-bold {{ $s['color'] }}">{{ $s['value'] }}</span>
            </div>
        @endforeach
    </section>

    <!-- Steps Section -->
    <section class="{{ $card }} flex flex-col gap-5 p-6">
        <div class="flex flex-col gap-2">
            <h2 class="text-xl font-bold">Panduan singkat proses pengajuan</h2>
            <p class="text-sm text-[#6B7280]">
                Ikuti langkah-langkah berikut agar pengajuan dokumen Anda dapat diproses dengan cepat dan tepat.
            </p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($steps as $index => $step)
                <div class="flex flex-col gap-3">
                    <div class="flex h-8 items-center gap-3">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-sm font-bold text-[#2563EB]">
                            {{ $index + 1 }}
                        </div>
                        @if($index < count($steps) - 1)
                            <div class="hidden h-0.5 flex-1 rounded-full bg-[#EFF6FF] lg:block"></div>
                        @endif
                    </div>
                    <span class="text-[15px] font-semibold">{{ $step }}</span>
                </div>
            @endforeach
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        <!-- Banner -->
        <div class="flex flex-col gap-5 rounded-2xl bg-[#2563EB] p-6 md:flex-row md:items-center">
            <div class="flex flex-1 flex-col gap-1.5">
                <h3 class="text-2xl font-bold text-white md:text-[28px]">Buat pengajuan dokumen baru</h3>
                <p class="text-[15px] text-[#DBEAFE]">
                    Pilih jenis dokumen, lengkapi data, dan unggah lampiran yang diperlukan untuk memulai proses pengajuan.
                </p>
            </div>
            <a href="#" class="rounded-lg bg-white px-5 py-3 text-center text-[15px] font-semibold text-[#2563EB] hover:bg-blue-50 transition">
                Buat Pengajuan
            </a>
        </div>
    </section>

    <!-- Submissions -->
    <section class="{{ $card }} flex flex-col gap-4 p-6">
        <div class="flex items-center justify-between gap-3">
            <div class="flex flex-col gap-1">
                <h2 class="text-xl font-bold">Daftar pengajuan terbaru</h2>
                <p class="text-sm text-[#6B7280]">Lihat status terbaru dari pengajuan dokumen Anda.</p>
            </div>
            <a href="#" class="shrink-0 rounded-lg bg-[#EFF6FF] px-4 py-2.5 text-sm font-semibold text-[#2563EB] hover:bg-blue-100 transition">
                Lihat semua
            </a>
        </div>

        <div class="flex items-center gap-4 rounded-xl bg-[#F9FAFB] p-5 border border-[#E5E7EB]">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-sm font-bold text-[#2563EB]">
                PJ
            </div>
            <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                <span class="text-[15px] font-semibold">Pengajuan proposal penelitian internal</span>
                <span class="text-[13px] text-[#6B7280]">
                    Proposal penelitian internal tentang pengembangan model pembelajaran berbasis teknologi.
                </span>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="rounded-full bg-[#ECFDF5] px-2.5 py-1.5 text-xs font-semibold text-[#047857]">
                    Diproses
                </span>
                <span class="whitespace-nowrap text-[13px] text-[#9CA3AF]">12 Mar 2024</span>
            </div>
        </div>
    </section>
</div>
@endsection