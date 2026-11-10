@extends('layouts.mahasiswa-tim')

@section('title', 'Dashboard - Teknologi Rekayasa Komputer')

@section('content')
<div class="dashboard-content flex flex-col gap-6">

    <!-- Hero Profile Card (Dengan Efek Fill Transition Kiri-ke-Kanan & Kanan-ke-Kiri) -->
    <div class="dashboard-profile group relative overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-8 shadow-[0_2px_10px_rgba(0,0,0,0.03)] transition-all duration-500 hover:border-[#2277FF]">
        <span class="absolute inset-y-0 left-0 z-20 w-1.5 bg-[#2277FF]" aria-hidden="true"></span>

        <!-- Layer Animasi Warna Biru (Meluncur Masuk dari Kiri, Keluar ke Kiri) -->
        <div class="pointer-events-none absolute inset-0 z-0 bg-gradient-to-r from-[#2277FF] to-[#1D4ED8] opacity-0 transition-all duration-500 ease-in-out [transform-origin:left_center] scale-x-0 group-hover:scale-x-100 group-hover:opacity-100"></div>

        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <!-- Informasi Utama Profil -->
            <div class="flex items-center gap-6">
                <!-- Avatar Profil dengan Auto-Sync -->
                <div class="relative flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-[#DCE7FB] text-3xl font-bold text-[#00235C] shadow-sm border-2 border-white transition-colors duration-500 group-hover:border-white/20">
                    <img @if($user?->foto_profil) src="{{ Storage::disk('public')->url($user->foto_profil) }}" @endif alt="Avatar" class="global-avatar-sync absolute inset-0 z-10 h-full w-full object-cover" style="display: none;" onload="this.style.display='block';">
                    <span>{{ mb_substr($nama, 0, 1) }}</span>
                </div>

                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-extrabold text-[#111827] transition-colors duration-500 group-hover:text-white">{{ $nama }}</h1>
                        <span class="rounded-full bg-[#EFF6FF] px-3 py-1 text-xs font-semibold text-[#2563EB] border border-[#BFDBFE] transition-all duration-500 group-hover:bg-white/20 group-hover:text-white group-hover:border-white/30">{{ $role }}</span>
                    </div>
                    <p class="text-xs font-medium text-[#6B7280] transition-colors duration-500 group-hover:text-blue-100">
                        {{ $labelIdentitas }}: <span class="font-semibold text-[#374151] group-hover:text-white">{{ $nomorIdentitas }}</span> • Angkatan {{ $angkatan }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-1 text-xs text-[#6B7280] transition-colors duration-500 group-hover:text-blue-100">
                        <span class="flex items-center gap-1.5">
                            <span class="size-2 rounded-full bg-[#10B981] group-hover:bg-[#34D399]"></span>
                            Status Akun: <strong class="text-[#047857] group-hover:text-white">{{ $statusAkun }}</strong>
                        </span>
                        <span>•</span>
                        <span>Terakhir login: <strong class="text-[#374151] group-hover:text-white">Belum tersedia</strong></span>
                    </div>
                </div>
            </div>

            <!-- Akses Cepat Kanan -->
            <div class="flex items-center gap-3 border-t border-[#E5E7EB] pt-4 md:border-t-0 md:pt-0 transition-colors duration-500 group-hover:border-white/20">
                <a href="{{ route('mahasiswa.profil') }}" class="flex items-center gap-2 rounded-xl border border-[#E5E7EB] bg-white px-4 py-2.5 text-xs font-semibold text-[#374151] shadow-xs transition-all duration-300 hover:bg-gray-50 group-hover:border-white/30 group-hover:bg-white/10 group-hover:text-white group-hover:hover:bg-white/20">
                    <svg class="size-4 text-[#6B7280] transition-colors duration-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Edit Profil</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Ringkasan 3 Stat Dashboard -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
        <div class="group relative flex min-w-0 items-center justify-between gap-2 overflow-hidden rounded-2xl border border-[#BFDBFE] bg-linear-to-br from-white to-[#EFF6FF] p-3 pl-4 shadow-[0_2px_8px_rgba(37,99,235,0.08)] transition duration-200 hover:-translate-y-1 hover:border-[#93C5FD] hover:shadow-lg active:-translate-y-1 active:shadow-lg sm:gap-3 sm:p-5 sm:pl-6">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#2277FF]" aria-hidden="true"></span>
            <div class="flex min-w-0 flex-col gap-2">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-xs font-semibold text-[#4B5563] sm:text-[13px]">Total pengajuan</span>
                    <span class="rounded-md bg-[#DBEAFE] px-2 py-0.5 text-[10px] font-semibold text-[#2563EB] sm:text-[11px]">Bulan ini</span>
                </div>
                <span class="text-2xl font-bold leading-none text-[#2277FF] sm:text-3xl">{{ $totalPengajuan }}</span>
            </div>
            <span class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-[#DBEAFE] text-[#2563EB] sm:size-11">
                <svg class="size-4 sm:size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/><path d="M8 13h8M8 17h8"/></svg>
            </span>
        </div>

        <div class="group relative flex min-w-0 items-center justify-between gap-2 overflow-hidden rounded-2xl border border-[#FDE68A] bg-linear-to-br from-white to-[#FFFBEB] p-3 pl-4 shadow-[0_2px_8px_rgba(217,119,6,0.08)] transition duration-200 hover:-translate-y-1 hover:border-[#FCD34D] hover:shadow-lg active:-translate-y-1 active:shadow-lg sm:gap-3 sm:p-5 sm:pl-6">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#F59E0B]" aria-hidden="true"></span>
            <div class="flex min-w-0 flex-col gap-2">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-xs font-semibold text-[#4B5563] sm:text-[13px]">Total kegiatan</span>
                    <span class="rounded-md bg-[#FEF3C7] px-2 py-0.5 text-[10px] font-semibold text-[#D97706] sm:text-[11px]">{{ $totalKegiatan }} kegiatan</span>
                </div>
                <span class="text-2xl font-bold leading-none text-[#D97706] sm:text-3xl">{{ $totalKegiatan }}</span>
            </div>
            <span class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-[#FEF3C7] text-[#D97706] sm:size-11">
                <svg class="size-4 sm:size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/></svg>
            </span>
        </div>

        <div class="group relative col-span-2 flex min-w-0 items-center justify-between gap-2 overflow-hidden rounded-2xl border border-[#A7F3D0] bg-linear-to-br from-white to-[#ECFDF5] p-3 pl-4 shadow-[0_2px_8px_rgba(5,150,105,0.08)] transition duration-200 hover:-translate-y-1 hover:border-[#6EE7B7] hover:shadow-lg active:-translate-y-1 active:shadow-lg sm:col-span-1 sm:gap-3 sm:p-5 sm:pl-6">
            <span class="absolute inset-y-0 left-0 w-1.5 bg-[#10B981]" aria-hidden="true"></span>
            <div class="flex min-w-0 flex-col gap-2">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-xs font-semibold text-[#4B5563] sm:text-[13px]">Prestasi mahasiswa</span>
                    <span class="rounded-md bg-[#D1FAE5] px-2 py-0.5 text-[10px] font-semibold text-[#059669] sm:text-[11px]">Semester ini</span>
                </div>
                <span class="text-2xl font-bold leading-none text-[#059669] sm:text-3xl">{{ $totalPrestasi }}</span>
            </div>
            <span class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-[#D1FAE5] text-[#059669] sm:size-11">
                <svg class="size-4 sm:size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3z"/></svg>
            </span>
        </div>
    </div>

    <!-- Section Panduan Singkat Proses Pengajuan -->
    <section class="flex flex-col gap-6 rounded-2xl border border-[#E5E7EB] bg-white p-6 shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-[#111827]">Panduan singkat proses pengajuan</h2>
            <p class="text-xs text-[#6B7280]">Pilih kategori kegiatan, isi detail kegiatan, lalu kirim pengajuan untuk ditinjau.</p>
        </div>

        <!-- Progress Steps Line -->
        <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:gap-4">
            <div class="absolute bottom-4 left-4 top-4 z-0 w-[3px] bg-[#93C5FD] sm:bottom-auto sm:left-6 sm:right-6 sm:top-4 sm:h-[3px] sm:w-auto"></div>

            <div class="relative z-10 flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-white bg-[#2277FF] text-xs font-bold text-white shadow-sm">1</span>
                <span class="text-xs font-semibold text-[#111827]">Pilih jenis pengajuan</span>
            </div>

            <div class="relative z-10 flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-white bg-[#2277FF] text-xs font-bold text-white shadow-sm">2</span>
                <span class="text-xs font-semibold text-[#111827]">Isi formulir sesuai informasi kegiatan</span>
            </div>

            <div class="relative z-10 flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-white bg-[#2277FF] text-xs font-bold text-white shadow-sm">3</span>
                <span class="text-xs font-semibold text-[#111827]">Periksa kembali, lalu kirim untuk ditinjau</span>
            </div>
        </div>

        <!-- Banner Buat Pengajuan Baru -->
        <div class="flex flex-col gap-4 rounded-2xl bg-[#2277FF] p-6 text-white sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-1">
                <h3 class="text-xl font-bold">Buat pengajuan dokumen baru</h3>
                <p class="text-xs text-blue-100">Pilih jenis pengajuan, isi formulir yang tersedia, lalu periksa kembali sebelum mengirim.</p>
            </div>
            <a href="{{ route($pengajuanRoute) }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-white px-6 py-3 text-xs font-bold text-[#2277FF] hover:bg-blue-50 transition shadow-sm">
                Buat Pengajuan
            </a>
        </div>
    </section>

    <!-- Section Daftar Pengajuan Terbaru -->
    <section class="dashboard-recent flex flex-col gap-4 rounded-2xl border border-[#E5E7EB] bg-white p-6 shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-lg font-bold text-[#111827]">Daftar pengajuan terbaru</h2>
                <p class="text-xs text-[#6B7280]">Lihat status terbaru dari pengajuan dokumen Anda.</p>
            </div>
            <a href="{{ route($pengajuanRoute) }}" class="rounded-lg bg-[#EFF6FF] px-4 py-2 text-xs font-semibold text-[#2563EB] hover:bg-blue-100 transition">
                Lihat semua
            </a>
        </div>

        @forelse ($pengajuanTerbaru as $pengajuan)
        <div class="flex items-center justify-between rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-4 transition hover:bg-gray-100/80">
            <div class="flex items-center gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[#DBEAFE] text-xs font-bold text-[#2563EB]">
                    PJ
                </div>
                <div class="flex flex-col gap-0.5">
                    <a href="{{ route('kegiatan.show', $pengajuan->getKey()) }}" class="text-xs font-bold text-[#111827]">{{ $pengajuan->nama_kegiatan }}</a>
                    <span class="text-[11px] text-[#6B7280]">{{ $pengajuan->deskripsi ?? '—' }}</span>
                </div>
            </div>

            <div class="flex flex-col items-end gap-1">
                <span class="rounded-full bg-[#ECFDF5] px-2.5 py-0.5 text-[10px] font-semibold text-[#047857]">
                    {{ ucfirst($pengajuan->statusTerkini?->status ?? 'Belum diajukan') }}
                </span>
                <span class="text-[11px] text-[#9CA3AF]">{{ $pengajuan->created_at?->locale('id')->translatedFormat('d M Y') }}</span>
            </div>
        </div>
        @empty
            <p class="text-xs text-[#6B7280]">Belum ada pengajuan.</p>
        @endforelse
    </section>
</div>
@endsection