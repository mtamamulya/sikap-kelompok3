@extends('layouts.app')

@section('title', 'Dashboard - Teknologi Rekayasa Komputer')

@section('content')
<div class="flex flex-col gap-6 p-4 md:px-6 md:py-8 lg:px-12 bg-[#F9FAFB]">
    
    <!-- Hero Profile Card (Dengan Efek Fill Transition Kiri-ke-Kanan & Kanan-ke-Kiri) -->
    <div class="group relative overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-8 shadow-[0_2px_10px_rgba(0,0,0,0.03)] transition-all duration-500 hover:border-[#2277FF]">
        
        <!-- Layer Animasi Warna Biru (Meluncur Masuk dari Kiri, Keluar ke Kiri) -->
        <div class="pointer-events-none absolute inset-0 z-0 bg-gradient-to-r from-[#2277FF] to-[#1D4ED8] opacity-0 transition-all duration-500 ease-in-out [transform-origin:left_center] scale-x-0 group-hover:scale-x-100 group-hover:opacity-100"></div>

        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <!-- Informasi Utama Profil -->
            <div class="flex items-center gap-6">
                <!-- Avatar Profil dengan Auto-Sync -->
                <div class="relative flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-[#DCE7FB] text-3xl font-bold text-[#00235C] shadow-sm border-2 border-white transition-colors duration-500 group-hover:border-white/20"
                     x-data="{ dashAvatar: localStorage.getItem('user_avatar') || null }"
                     x-init="
                        window.addEventListener('avatar-updated', (e) => dashAvatar = e.detail);
                        window.addEventListener('storage', () => dashAvatar = localStorage.getItem('user_avatar'));
                     ">
                    <template x-if="dashAvatar">
                        <img :src="dashAvatar" class="absolute inset-0 z-10 h-full w-full object-cover">
                    </template>
                    <template x-if="!dashAvatar">
                        <span>A</span>
                    </template>
                </div>

                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-extrabold text-[#111827] transition-colors duration-500 group-hover:text-white">Arya Bima</h1>
                        <span class="rounded-full bg-[#EFF6FF] px-3 py-1 text-xs font-semibold text-[#2563EB] border border-[#BFDBFE] transition-all duration-500 group-hover:bg-white/20 group-hover:text-white group-hover:border-white/30">Mahasiswa</span>
                    </div>
                    <p class="text-xs font-medium text-[#6B7280] transition-colors duration-500 group-hover:text-blue-100">
                        NIM: <span class="font-semibold text-[#374151] group-hover:text-white">43325115</span> • Teknologi Rekayasa Komputer (Angkatan 2024)
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-1 text-xs text-[#6B7280] transition-colors duration-500 group-hover:text-blue-100">
                        <span class="flex items-center gap-1.5">
                            <span class="size-2 rounded-full bg-[#10B981] group-hover:bg-[#34D399]"></span>
                            Status Akun: <strong class="text-[#047857] group-hover:text-white">Aktif</strong>
                        </span>
                        <span>•</span>
                        <span>Terakhir login: <strong class="text-[#374151] group-hover:text-white">12 Maret 2026, 09:15 WIB</strong></span>
                    </div>
                </div>
            </div>

            <!-- Akses Cepat Kanan -->
            <div class="flex items-center gap-3 border-t border-[#E5E7EB] pt-4 md:border-t-0 md:pt-0 transition-colors duration-500 group-hover:border-white/20">
                <a href="{{ url('/profil') }}" class="flex items-center gap-2 rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-xs font-semibold text-[#374151] shadow-xs transition-all duration-300 hover:bg-gray-50 group-hover:border-white/30 group-hover:bg-white/10 group-hover:text-white group-hover:hover:bg-white/20">
                    <svg class="size-4 text-[#6B7280] transition-colors duration-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Edit Profil</span>
                </a>
                <a href="{{ url('/kegiatan') }}" class="flex items-center gap-2 rounded-xl bg-[#2277FF] px-4 py-2.5 text-xs font-bold text-white shadow-xs transition-all duration-300 hover:bg-blue-600 group-hover:bg-white group-hover:text-[#2277FF]">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Kegiatan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid 3 Card Ringkasan Statistik (Style Seperti di Halaman Kegiatan) -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <!-- Card 1: Total Pengajuan -->
        <div class="group relative overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_2px_8px_rgba(0,0,0,0.02)] transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-[#D1D5DB]">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#2563EB] transition-all duration-300 group-hover:w-2"></div>
            <div class="flex items-center justify-between pl-2">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider transition-colors duration-300 group-hover:text-[#111827]">TOTAL PENGAJUAN</span>
                        <span class="rounded-md bg-[#EFF6FF] px-2 py-0.5 text-[10px] font-semibold text-[#2563EB]">Bulan ini</span>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-bold text-[#111827] leading-none">12</span>
                        <span class="text-[11px] font-medium text-[#9CA3AF]">Dokumen</span>
                    </div>
                </div>
                <div class="flex size-11 items-center justify-center rounded-xl bg-[#EFF6FF] transition-transform duration-300 group-hover:scale-110">
                    <svg class="size-5 text-[#2563EB] transition-transform duration-300 group-hover:rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Kegiatan -->
        <div class="group relative overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_2px_8px_rgba(0,0,0,0.02)] transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-[#D1D5DB]">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#F59E0B] transition-all duration-300 group-hover:w-2"></div>
            <div class="flex items-center justify-between pl-2">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider transition-colors duration-300 group-hover:text-[#111827]">TOTAL KEGIATAN</span>
                        <span class="rounded-md bg-[#FFFBEB] px-2 py-0.5 text-[10px] font-semibold text-[#D97706]">3 kegiatan</span>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-bold text-[#111827] leading-none">3</span>
                        <span class="text-[11px] font-medium text-[#9CA3AF]">Kegiatan</span>
                    </div>
                </div>
                <div class="flex size-11 items-center justify-center rounded-xl bg-[#FFFBEB] transition-transform duration-300 group-hover:scale-110">
                    <svg class="size-5 text-[#D97706] transition-transform duration-300 group-hover:rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Prestasi Mahasiswa -->
        <div class="group relative overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_2px_8px_rgba(0,0,0,0.02)] transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-[#D1D5DB]">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#10B981] transition-all duration-300 group-hover:w-2"></div>
            <div class="flex items-center justify-between pl-2">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider transition-colors duration-300 group-hover:text-[#111827]">PRESTASI MAHASISWA</span>
                        <span class="rounded-md bg-[#ECFDF5] px-2 py-0.5 text-[10px] font-semibold text-[#059669]">Semester ini</span>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-bold text-[#111827] leading-none">2</span>
                        <span class="text-[11px] font-medium text-[#9CA3AF]">Penghargaan</span>
                    </div>
                </div>
                <div class="flex size-11 items-center justify-center rounded-xl bg-[#ECFDF5] transition-transform duration-300 group-hover:scale-110">
                    <svg class="size-5 text-[#059669] transition-transform duration-300 group-hover:rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
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
</div>
@endsection