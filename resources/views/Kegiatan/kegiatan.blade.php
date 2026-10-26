@extends('layouts.app')

@section('title', 'Kegiatan - Teknologi Rekayasa Komputer')

@section('content')
@php
    $summaryRow1 = [
        ['label' => 'LOMBA', 'value' => 12, 'bg' => '#AAC8F8', 'fg' => '#0A0A0A', 'border' => 'transparent'],
        ['label' => 'VOLUNTEER', 'value' => 2, 'bg' => '#FCC17E', 'fg' => '#111827', 'border' => '#E5E7EB'],
        ['label' => 'KKL', 'value' => 1, 'bg' => '#ECFDF5', 'fg' => '#111827', 'border' => '#E5E7EB'],
        ['label' => 'MAGANG', 'value' => 9, 'bg' => '#F3F4F6', 'fg' => '#4B5563', 'border' => '#E5E7EB'],
    ];

    $summaryRow2 = [
        ['label' => 'PENGABDIAN', 'value' => 12, 'bg' => '#F8E081', 'fg' => '#0A0A0A', 'border' => 'transparent'],
        ['label' => 'PENELITIAN', 'value' => 1, 'bg' => '#D6EDB5', 'fg' => '#111827', 'border' => '#E5E7EB'],
    ];

    $semesters = [
        ['label' => 'Sem 1', 'value' => 12],
        ['label' => 'Sem 2', 'value' => 8],
        ['label' => 'Sem 3', 'value' => 15],
        ['label' => 'Sem 4', 'value' => 6],
        ['label' => 'Sem 5', 'value' => 9],
        ['label' => 'Sem 6', 'value' => 4],
    ];

    // Data Halaman 1
    $page1 = [
        ['id' => 1, 'name' => 'FKBSE 2025', 'type' => 'Pengabdian', 'date' => '12 Nov 2025', 'raw_date' => '2025-11-12', 'status' => 'done', 'note' => 'Terima kasih atas kontribusi anda.', 'action' => 'Unduh', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Himpunan Mahasiswa TRK', 'file_name' => 'sertifikat-fkbse-2025.pdf', 'file_size' => '1.4 MB'],
        ['id' => 2, 'name' => 'KMIPN VIII 2026', 'type' => 'Lomba', 'date' => '10 Sept 2026', 'raw_date' => '2026-09-10', 'status' => 'ongoing', 'note' => 'Selamat berkegiatan.', 'action' => 'Lihat detail', 'capaian' => 'Finalis', 'tingkat' => 'Nasional', 'penyelenggara' => 'Politeknik Negeri', 'file_name' => 'sertifikat-kmipn-viii-2026.pdf', 'file_size' => '2.1 MB'],
        ['id' => 3, 'name' => 'IMAKEN Progress In Action 2026', 'type' => 'Volunteer', 'date' => '30 Nov 2026', 'raw_date' => '2026-11-30', 'status' => 'upcoming', 'note' => '', 'action' => 'Persiapkan', 'capaian' => 'Panitia', 'tingkat' => 'Wilayah', 'penyelenggara' => 'IMAKEN', 'file_name' => 'sertifikat-imaken-2026.pdf', 'file_size' => '1.1 MB'],
        ['id' => 4, 'name' => 'Google Singapura', 'type' => 'Kunjungan Industri', 'date' => '06 Mar 2027', 'raw_date' => '2027-03-06', 'status' => 'upcoming', 'note' => '', 'action' => 'Persiapkan', 'capaian' => 'Peserta', 'tingkat' => 'Internasional', 'penyelenggara' => 'Google Asia Pacific', 'file_name' => 'sertifikat-google-singapura.pdf', 'file_size' => '3.0 MB'],
        ['id' => 5, 'name' => 'KKL Schneider Electric', 'type' => 'Kuliah Kerja Lapangan', 'date' => '04 Agu 2027', 'raw_date' => '2027-08-04', 'status' => 'upcoming', 'note' => '', 'action' => 'Persiapkan', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Schneider Electric Indonesia', 'file_name' => 'sertifikat-kkl-schneider.pdf', 'file_size' => '1.8 MB'],
        ['id' => 6, 'name' => 'Amazon Web Service', 'type' => 'Magang', 'date' => '01 Sept 2027', 'raw_date' => '2027-09-01', 'status' => 'upcoming', 'note' => '', 'action' => 'Persiapkan', 'capaian' => 'Intern', 'tingkat' => 'Internasional', 'penyelenggara' => 'Amazon Web Services', 'file_name' => 'sertifikat-aws-internship.pdf', 'file_size' => '2.5 MB'],
    ];

    // Data Halaman 2
    $page2 = [
        ['id' => 7, 'name' => 'Hackathon Nasional 2025', 'type' => 'Lomba', 'date' => '15 Jan 2025', 'raw_date' => '2025-01-15', 'status' => 'done', 'note' => 'Juara 2 Kategori Software.', 'action' => 'Unduh', 'capaian' => 'Juara 2', 'tingkat' => 'Nasional', 'penyelenggara' => 'Kementerian Kominfo', 'file_name' => 'sertifikat-hackathon-2025.pdf', 'file_size' => '1.9 MB'],
        ['id' => 8, 'name' => 'Workshop IoT & Embedded System', 'type' => 'Pelatihan', 'date' => '22 Feb 2025', 'raw_date' => '2025-02-22', 'status' => 'done', 'note' => 'Sertifikat kompetensi terbit.', 'action' => 'Unduh', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Lab Embedded TRK', 'file_name' => 'sertifikat-workshop-iot.pdf', 'file_size' => '1.2 MB'],
        ['id' => 9, 'name' => 'Studi Independen AI Micro-Credential', 'type' => 'Magang', 'date' => '10 Jun 2025', 'raw_date' => '2025-06-10', 'status' => 'done', 'note' => 'Selesai dengan nilai A.', 'action' => 'Unduh', 'capaian' => 'Lulus', 'tingkat' => 'Nasional', 'penyelenggara' => 'Kampus Merdeka x AI Center', 'file_name' => 'sertifikat-studi-independen-ai.pdf', 'file_size' => '2.8 MB'],
        ['id' => 10, 'name' => 'Pengabdian Desa Digital Banyumanik', 'type' => 'Pengabdian', 'date' => '18 Agu 2025', 'raw_date' => '2025-08-18', 'status' => 'done', 'note' => 'Selesai dilaksanakan.', 'action' => 'Unduh', 'capaian' => 'Tim Pelaksana', 'tingkat' => 'Kabupaten/Kota', 'penyelenggara' => 'Jurusan Elektro Polines', 'file_name' => 'sertifikat-pengabdian-banyumanik.pdf', 'file_size' => '1.5 MB'],
        ['id' => 11, 'name' => 'Seminar International TRK Tech', 'type' => 'Seminar', 'date' => '05 Okt 2025', 'raw_date' => '2025-10-05', 'status' => 'done', 'note' => 'Peserta Aktif.', 'action' => 'Unduh', 'capaian' => 'Peserta', 'tingkat' => 'Internasional', 'penyelenggara' => 'TRK Polines', 'file_name' => 'sertifikat-seminar-international.pdf', 'file_size' => '1.3 MB'],
        ['id' => 12, 'name' => 'Lomba Karya Tulis Ilmiah 2025', 'type' => 'Penelitian', 'date' => '12 Des 2025', 'raw_date' => '2025-12-12', 'status' => 'done', 'note' => 'Finalis 10 Besar.', 'action' => 'Unduh', 'capaian' => 'Finalis', 'tingkat' => 'Nasional', 'penyelenggara' => 'Universitas Indonesia', 'file_name' => 'sertifikat-lkti-2025.pdf', 'file_size' => '1.7 MB'],
    ];

    // Data Halaman 3
    $page3 = [
        ['id' => 13, 'name' => 'Lomba CTF Cyber Security 2024', 'type' => 'Lomba', 'date' => '10 Mar 2024', 'raw_date' => '2024-03-10', 'status' => 'done', 'note' => 'Peringkat 5 Nasional.', 'action' => 'Unduh', 'capaian' => 'Peringkat 5', 'tingkat' => 'Nasional', 'penyelenggara' => 'BSSN', 'file_name' => 'sertifikat-ctf-2024.pdf', 'file_size' => '2.0 MB'],
        ['id' => 14, 'name' => 'Pelatihan Cloud Architecture AWS', 'type' => 'Pelatihan', 'date' => '14 Mei 2024', 'raw_date' => '2024-05-14', 'status' => 'done', 'note' => 'Sertifikat AWS re:Start.', 'action' => 'Unduh', 'capaian' => 'Peserta', 'tingkat' => 'Internasional', 'penyelenggara' => 'AWS Educate', 'file_name' => 'sertifikat-aws-restart.pdf', 'file_size' => '1.6 MB'],
        ['id' => 15, 'name' => 'Volunteer Mengajar AI Sekolah Dasar', 'type' => 'Volunteer', 'date' => '20 Jul 2024', 'raw_date' => '2024-07-20', 'status' => 'done', 'note' => 'Piagam Penghargaan.', 'action' => 'Unduh', 'capaian' => 'Relawan', 'tingkat' => 'Kabupaten/Kota', 'penyelenggara' => 'Komunitas AI Semarang', 'file_name' => 'sertifikat-volunteer-ai.pdf', 'file_size' => '1.1 MB'],
        ['id' => 16, 'name' => 'Bootcamp Fullstack Web Developer', 'type' => 'Pelatihan', 'date' => '11 Sep 2024', 'raw_date' => '2024-09-11', 'status' => 'done', 'note' => 'Lulus Capstone Project.', 'action' => 'Unduh', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Dicoding Indonesia', 'file_name' => 'sertifikat-fullstack-dev.pdf', 'file_size' => '2.2 MB'],
        ['id' => 17, 'name' => 'Kunjungan Industri Telkomsel Digital', 'type' => 'Kunjungan Industri', 'date' => '02 Nov 2024', 'raw_date' => '2024-11-02', 'status' => 'done', 'note' => 'Selesai diikuti.', 'action' => 'Unduh', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Telkomsel Smart Office', 'file_name' => 'sertifikat-telkomsel-visit.pdf', 'file_size' => '1.4 MB'],
        ['id' => 18, 'name' => 'Kompetisi Robotics & Automation', 'type' => 'Lomba', 'date' => '18 Des 2024', 'raw_date' => '2024-12-18', 'status' => 'done', 'note' => 'Juara Harapan 1.', 'action' => 'Unduh', 'capaian' => 'Juara Harapan 1', 'tingkat' => 'Nasional', 'penyelenggara' => 'ITB Robotics Club', 'file_name' => 'sertifikat-robotics-2024.pdf', 'file_size' => '1.8 MB'],
    ];
    
    // Gabungkan semua data untuk dikirim ke Alpine.js
    $allActivities = array_merge($page1, $page2, $page3);
@endphp

<div x-data="kegiatanApp(@js($allActivities))" class="flex flex-col gap-4 p-4 md:px-6 md:py-8 lg:px-12 bg-[#F7F7F7]">
    <!-- Summary Row 1 -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach($summaryRow1 as $c)
            <div class="flex flex-col gap-1 rounded-2xl border p-3 shadow-[0_4px_12px_rgba(0,0,0,0.05)]" style="background: {{ $c['bg'] }}; color: {{ $c['fg'] }}; border-color: {{ $c['border'] }};">
                <span class="text-base font-semibold">{{ $c['label'] }}</span>
                <span class="text-[32px] font-bold">{{ $c['value'] }}</span>
            </div>
        @endforeach
    </div>

    <!-- Summary Row 2 -->
    <div class="grid grid-cols-2 gap-4">
        @foreach($summaryRow2 as $c)
            <div class="flex flex-col gap-1 rounded-2xl border p-3 shadow-[0_4px_12px_rgba(0,0,0,0.05)]" style="background: {{ $c['bg'] }}; color: {{ $c['fg'] }}; border-color: {{ $c['border'] }};">
                <span class="text-base font-semibold">{{ $c['label'] }}</span>
                <span class="text-[32px] font-bold">{{ $c['value'] }}</span>
            </div>
        @endforeach
    </div>

    <!-- Grafik Kegiatan per Semester -->
    <section class="flex flex-col gap-2 rounded-2xl border border-[#E5E7EB] bg-white p-4 shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
        <div class="flex flex-col gap-0.5">
            <h2 class="text-xl font-bold text-[#111827]">Kegiatan per semester</h2>
            <p class="text-sm leading-[1.5] text-[#6B7280]">
                Ringkasan kegiatan yang diikuti selama masa perkuliahan.
            </p>
        </div>
        <div class="flex items-center justify-between text-xs text-[#6B7280] pt-1">
            <div class="flex items-center gap-2">
                <span class="size-3 rounded-[3px] bg-[#2277FF]"></span>
                Jumlah kegiatan
            </div>
            <span>0 — 20 kegiatan</span>
        </div>
        <div class="flex h-[110px] items-end gap-3 pt-2">
            <div class="flex h-full w-8 flex-col items-end justify-between pb-4 text-[12px] text-[#6B7280]">
                <span>20</span><span>15</span><span>10</span><span>5</span><span>0</span>
            </div>
            <div class="flex h-full flex-1 items-end gap-2 sm:gap-4">
                @foreach($semesters as $s)
                    <div class="group relative flex h-full flex-1 flex-col items-center justify-end gap-1 cursor-pointer">
                        <div class="pointer-events-none absolute -top-8 left-1/2 z-10 flex -translate-x-1/2 flex-col items-center opacity-0 transition-all duration-300 group-hover:-translate-y-1 group-hover:opacity-100">
                            <span class="whitespace-nowrap rounded-md bg-[#111827] px-2.5 py-1.5 text-[11px] font-medium text-white shadow-lg">
                                {{ $s['value'] }} Kegiatan di {{ $s['label'] }}
                            </span>
                            <div class="-mt-1 h-2 w-2 rotate-45 bg-[#111827]"></div>
                        </div>

                        <span class="text-xs font-semibold text-[#111827] transition-colors duration-300 group-hover:text-[#2277FF]">{{ $s['value'] }}</span>
                        <div class="w-full max-w-12 rounded-t-lg bg-[#2277FF] transition-all duration-300 group-hover:bg-[#1A5BCC]" style="height: {{ $s['value'] * 4 }}px"></div>
                        <span class="text-xs text-[#6B7280]">{{ $s['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section Tabel Kegiatan -->
    <section class="flex flex-col gap-4 rounded-2xl border border-[#E5E7EB] bg-white p-4 shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-[#111827]">Daftar kegiatan</h2>
            <button class="rounded-lg bg-[#EFF6FF] px-4 py-2 text-xs font-semibold text-[#2563EB] hover:bg-blue-100 transition">
                Lihat semua
            </button>
        </div>

        <!-- Form Filter & Pencarian -->
        <div class="flex flex-col items-stretch gap-3 lg:flex-row lg:items-end">
            <!-- Search -->
            <div class="flex flex-col gap-1.5 lg:w-[480px]">
                <span class="text-xs font-medium text-[#4B5563]">Cari Kegiatan</span>
                <div class="flex h-[42px] items-center gap-2 rounded-lg border border-[#E5E7EB] bg-white px-3 focus-within:border-[#2277FF] focus-within:ring-1 focus-within:ring-[#2277FF] transition">
                    <input x-model="searchQuery" placeholder="Cari jenis atau nama kegiatan" class="w-full bg-transparent text-xs outline-none placeholder:text-[#9CA3AF] text-[#111827]" />
                    <svg class="size-4 shrink-0 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Kategori -->
            <div x-data="{ open: false }" @click.outside="open = false" class="flex flex-col gap-1.5 flex-1 relative">
                <span class="text-xs font-medium text-[#4B5563]">Kategori</span>
                
                <button type="button" @click="open = !open"
                        class="flex h-[42px] w-full items-center justify-between rounded-lg border bg-white px-3 text-xs outline-none transition-all duration-200"
                        :class="open ? 'border-[#2277FF] ring-1 ring-[#2277FF]' : 'border-[#E5E7EB] hover:bg-gray-50'">
                    <span class="truncate" :class="selectedCategory === '' ? 'text-[#9CA3AF]' : 'text-[#111827]'" x-text="selectedCategory === '' ? 'Semua Kategori' : selectedCategory"></span>
                    <svg class="size-4 shrink-0 text-[#9CA3AF] transition-transform duration-300" :class="open ? 'rotate-180 text-[#2277FF]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-[-10px] scale-95"
                     class="absolute left-0 top-[64px] z-50 max-h-[240px] w-full overflow-y-auto rounded-xl border border-[#E5E7EB] bg-white py-1.5 shadow-xl scrollbar-hide">
                    
                    <template x-for="cat in ['', 'Lomba', 'Pengabdian', 'Volunteer', 'Kunjungan Industri', 'Kuliah Kerja Lapangan', 'Magang', 'Pelatihan', 'Seminar', 'Penelitian']">
                        <button type="button" @click="selectedCategory = cat; open = false"
                                class="flex w-full items-center justify-between px-4 py-2.5 text-left text-xs transition-colors"
                                :class="selectedCategory === cat ? 'bg-[#EFF6FF] text-[#2563EB] font-semibold' : 'text-[#4B5563] hover:bg-[#F9FAFB] hover:text-[#111827]'">
                            <span x-text="cat === '' ? 'Semua Kategori' : cat"></span>
                            <svg x-show="selectedCategory === cat" class="size-4 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Rentang Tanggal -->
            <div x-data="{ dateOpen: false }" @click.outside="dateOpen = false" class="flex flex-col gap-1.5 lg:w-[320px] relative">
                <span class="text-xs font-medium text-[#4B5563]">Rentang tanggal</span>
                
                <button type="button" 
                        @click="dateOpen = !dateOpen; if(dateOpen) { tempStartDate = appliedStartDate; tempEndDate = appliedEndDate; }"
                        class="flex h-[42px] w-full items-center justify-between rounded-lg border bg-white px-3 text-xs outline-none transition-all duration-200"
                        :class="dateOpen ? 'border-[#2277FF] ring-1 ring-[#2277FF]' : 'border-[#E5E7EB] hover:bg-gray-50'">
                    <div class="flex items-center gap-2 truncate" :class="getDateDisplay() === 'Semua Tanggal' ? 'text-[#9CA3AF]' : 'text-[#111827]'">
                        <svg class="size-4 shrink-0 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="font-medium truncate" x-text="getDateDisplay()"></span>
                    </div>
                    <svg class="size-4 shrink-0 text-[#9CA3AF] transition-transform duration-300" :class="dateOpen ? 'rotate-180 text-[#2277FF]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="dateOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-[-10px] scale-95"
                     class="absolute right-0 lg:left-0 top-[64px] z-50 w-[280px] sm:w-full rounded-xl border border-[#E5E7EB] bg-white p-3 shadow-xl">
                    
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-semibold text-[#6B7280]">Dari Tanggal</label>
                            <input type="date" x-model="tempStartDate" class="w-full rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] px-3 py-2 text-xs text-[#111827] outline-none focus:border-[#2277FF] focus:ring-1 focus:ring-[#2277FF] transition" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-semibold text-[#6B7280]">Sampai Tanggal</label>
                            <input type="date" x-model="tempEndDate" class="w-full rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] px-3 py-2 text-xs text-[#111827] outline-none focus:border-[#2277FF] focus:ring-1 focus:ring-[#2277FF] transition" />
                        </div>
                        
                        <div class="mt-1 flex items-center justify-between border-t border-[#E5E7EB] pt-3">
                            <button type="button" @click="tempStartDate = ''; tempEndDate = ''; appliedStartDate = ''; appliedEndDate = ''; dateOpen = false;" class="px-3 py-1.5 text-xs font-semibold text-[#6B7280] hover:text-[#EF4444] hover:bg-red-50 rounded-md transition">Kosongkan</button>
                            <button type="button" @click="appliedStartDate = tempStartDate; appliedEndDate = tempEndDate; dateOpen = false;" class="px-4 py-1.5 text-xs font-bold bg-[#2277FF] text-white hover:bg-blue-600 rounded-md transition shadow-sm">Terapkan</button>
                        </div>
                    </div>
                </div>
            </div>

            <button @click="resetFilters()" class="h-[42px] rounded-lg border border-[#E5E7EB] bg-white px-4 text-xs font-semibold text-[#2563EB] hover:bg-gray-50 transition shadow-sm">
                Reset
            </button>
        </div>

        <!-- Tabel Daftar Kegiatan -->
        <div class="overflow-x-auto rounded-xl border border-[#E5E7EB]">
            <div class="min-w-[760px]">
                <div class="flex gap-4 bg-[#F9FAFB] px-4 py-2.5 text-xs font-semibold text-[#6B7280] uppercase tracking-wider border-b border-[#E5E7EB]">
                    <div class="flex-1">Nama Kegiatan/Jenis Kegiatan</div>
                    <div class="w-[115px]">TANGGAL</div>
                    <div class="w-[235px]">STATUS KEGIATAN</div>
                    <div class="w-[195px] text-right">DOKUMENTASI/SERTIFIKAT</div>
                </div>

                <!-- State Kosong -->
                <div x-show="paginatedData.length === 0" x-cloak class="flex items-center justify-center p-8 text-sm text-[#6B7280]">
                    Tidak ada kegiatan yang sesuai dengan filter Anda.
                </div>

                <!-- Tampilan Data Dinamis -->
                <template x-for="(a, index) in paginatedData" :key="index">
                    <div class="flex min-h-[58px] items-center gap-4 border-b border-[#E5E7EB] px-4 py-2.5 last:border-b-0 hover:bg-[#F9FAFB] transition">
                        <div class="flex flex-1 items-center gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-xs font-bold text-[#2563EB]" x-text="((page - 1) * itemsPerPage) + index + 1"></div>
                            <div class="flex flex-1 flex-col gap-0.5">
                                <span class="text-xs font-semibold text-[#111827]" x-text="a.name"></span>
                                <span class="text-[11px] text-[#6B7280]" x-text="a.type"></span>
                            </div>
                        </div>
                        <div class="w-[115px] text-xs text-[#6B7280]" x-text="a.date"></div>
                        <div class="flex w-[235px] flex-col items-start gap-1">
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold" 
                                  :style="`background: ${getStatusStyle(a.status).bg}; color: ${getStatusStyle(a.status).fg};`" 
                                  x-text="getStatusStyle(a.status).label">
                            </span>
                            <span x-show="a.note" class="text-[11px] text-[#6B7280]" x-text="a.note"></span>
                        </div>
                        <div class="w-[195px] flex justify-end">
                            <button @click="openModal(a)" class="flex items-center gap-1.5 text-xs font-semibold text-[#2563EB] hover:underline">
                                <span x-text="a.action"></span>
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer Pagination Interaktif -->
        <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
            <span class="text-xs text-[#6B7280]">
                Menampilkan <span x-text="showingStart"></span>–<span x-text="showingEnd"></span> dari <span x-text="filteredData.length"></span> Kegiatan
            </span>
            
            <div class="flex items-center gap-2 text-xs" x-show="totalPages > 0">
                <button @click="prevPage()" 
                        :disabled="page === 1"
                        :class="page === 1 ? 'text-[#9CA3AF] cursor-not-allowed' : 'text-[#4B5563] hover:bg-gray-50'"
                        class="flex h-8 items-center gap-1 rounded-lg border border-[#E5E7EB] px-3 transition">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <span>Sebelumnya</span>
                </button>
                
                <template x-for="p in totalPages" :key="p">
                    <button @click="page = p" 
                            :class="page === p ? 'bg-[#2563EB] text-white font-semibold shadow-sm' : 'border border-[#E5E7EB] bg-white text-[#4B5563] hover:bg-gray-50'"
                            class="size-8 rounded-lg transition" x-text="p">
                    </button>
                </template>
                
                <button @click="nextPage()" 
                        :disabled="page === totalPages"
                        :class="page === totalPages ? 'text-[#9CA3AF] cursor-not-allowed' : 'text-[#4B5563] hover:bg-gray-50'"
                        class="flex h-8 items-center gap-1 rounded-lg border border-[#E5E7EB] bg-white px-3 transition">
                    <span>Berikutnya</span>
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </section>

    <!-- ==================== POPUP / MODAL DETAIL BUKTI PRESTASI ==================== -->
    <div x-show="isModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs overflow-y-auto">
        
        <div @click.outside="closeModal()" 
             x-show="isModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-4xl rounded-2xl bg-white p-6 shadow-2xl my-8">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between border-b border-[#F3F4F6] pb-4">
                <div>
                    <span class="text-xs font-semibold text-[#6B7280]">Bukti prestasi</span>
                    <h3 class="text-xl font-bold text-[#111827]" x-text="activeItem?.name"></h3>
                </div>
                <button @click="closeModal()" class="rounded-lg p-1 text-[#9CA3AF] hover:bg-gray-100 hover:text-[#111827] transition">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Content Grid -->
            <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-12">
                
                <!-- Kolom Kiri: Pratinjau Dokumen -->
                <div class="lg:col-span-7 flex flex-col gap-3">
                    <!-- Tab Switcher Dokumen -->
                    <div class="flex items-center gap-2">
                        <button @click="activeTab = 'sertifikat'" 
                                :class="activeTab === 'sertifikat' ? 'bg-[#2563EB] text-white font-semibold' : 'bg-white text-[#6B7280] hover:bg-gray-100'"
                                class="rounded-lg px-3.5 py-1.5 text-xs transition border border-[#E5E7EB]">
                            Dokumen 1 • Sertifikat
                        </button>
                        <button @click="activeTab = 'foto'" 
                                :class="activeTab === 'foto' ? 'bg-[#2563EB] text-white font-semibold' : 'bg-white text-[#6B7280] hover:bg-gray-100'"
                                class="rounded-lg px-3.5 py-1.5 text-xs transition border border-[#E5E7EB]">
                            Dokumen 2 • Foto kegiatan
                        </button>
                    </div>

                    <!-- Area Pratinjau Gambar / Sertifikat -->
                    <div class="relative flex min-h-[300px] flex-col items-center justify-center rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-4 text-center">
                        
                        <!-- Pratinjau Tab Sertifikat -->
                        <div x-show="activeTab === 'sertifikat'" class="w-full flex flex-col items-center">
                            <div class="w-full max-w-md rounded-lg border-2 border-[#D97706] bg-white p-6 shadow-sm flex flex-col items-center text-center gap-2">
                                <span class="text-[10px] font-bold tracking-widest text-[#1E3A8A] uppercase">FORUM INOVASI TEKNOLOGI</span>
                                <h4 class="text-sm font-black tracking-wider text-[#1E3A8A] uppercase">SERTIFIKAT PENGHARGAAN</h4>
                                <span class="text-[10px] text-[#6B7280] mt-2">Diberikan kepada</span>
                                <span class="text-base font-bold text-[#111827] border-b border-[#E5E7EB] pb-0.5 px-4">Arya Bima</span>
                                <p class="text-[10px] text-[#4B5563] mt-1 max-w-[280px]">
                                    sebagai <span class="font-semibold text-[#111827]" x-text="activeItem?.capaian || 'Peserta'"></span> pada <span class="font-semibold text-[#111827]" x-text="activeItem?.name"></span> tingkat <span class="font-semibold text-[#111827]" x-text="activeItem?.tingkat || 'Nasional'"></span>
                                </p>
                                <div class="mt-4 flex w-full justify-between items-end text-[9px] text-[#6B7280]">
                                    <span x-text="activeItem?.date"></span>
                                    <span class="border-t border-[#9CA3AF] pt-0.5 px-2">Penyelenggara</span>
                                </div>
                            </div>
                            <span class="mt-3 text-[11px] text-[#9CA3AF]">Pratinjau contoh. File asli akan tampil di sini setelah diunggah.</span>
                        </div>

                        <!-- Pratinjau Tab Foto Kegiatan -->
                        <div x-show="activeTab === 'foto'" class="w-full flex flex-col items-center justify-center min-h-[220px]">
                            <svg class="size-12 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="mt-2 text-xs font-semibold text-[#4B5563]">Dokumentasi Foto Kegiatan</span>
                            <span class="text-[11px] text-[#9CA3AF]">Foto saat kegiatan berlangsung</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail Informasi & Download -->
                <div class="lg:col-span-5 flex flex-col justify-between gap-4">
                    
                    <!-- Kartu Detail Prestasi -->
                    <div class="rounded-xl border border-[#E5E7EB] bg-white p-4">
                        <h4 class="text-xs font-bold text-[#111827] mb-3">Detail prestasi</h4>
                        <div class="flex flex-col gap-2 text-xs">
                            <div class="flex justify-between"><span class="text-[#6B7280]">Capaian</span><span class="font-semibold text-[#111827]" x-text="activeItem?.capaian || 'Juara 2'"></span></div>
                            <div class="flex justify-between"><span class="text-[#6B7280]">Tingkat</span><span class="font-semibold text-[#111827]" x-text="activeItem?.tingkat || 'Nasional'"></span></div>
                            <div class="flex justify-between"><span class="text-[#6B7280]">Jenis</span><span class="font-semibold text-[#111827]">Akademik</span></div>
                            <div class="flex justify-between"><span class="text-[#6B7280]">Bentuk</span><span class="font-semibold text-[#111827]">Tim</span></div>
                            <div class="flex justify-between"><span class="text-[#6B7280]">Kategori</span><span class="font-semibold text-[#111827]" x-text="activeItem?.type"></span></div>
                            <div class="flex justify-between"><span class="text-[#6B7280]">Penyelenggara</span><span class="font-semibold text-[#111827] text-right" x-text="activeItem?.penyelenggara || 'Forum Inovasi Teknologi'"></span></div>
                            <div class="flex justify-between"><span class="text-[#6B7280]">Tanggal</span><span class="font-semibold text-[#111827]" x-text="activeItem?.date"></span></div>
                            <div class="flex justify-between"><span class="text-[#6B7280]">Kegiatan terkait</span><span class="font-semibold text-[#111827] text-right" x-text="activeItem?.name"></span></div>
                        </div>
                    </div>

                    <!-- Kartu Dokumen File & Tombol Unduh -->
                    <div class="rounded-xl border border-[#E5E7EB] bg-white p-4 flex flex-col gap-3">
                        <h4 class="text-xs font-bold text-[#111827]">Dokumen</h4>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[11px] text-[#6B7280]">Sertifikat</span>
                            <span class="text-xs font-semibold text-[#111827] break-all" x-text="activeItem?.file_name || 'sertifikat-kegiatan.pdf'"></span>
                            <span class="text-[10px] text-[#9CA3AF]" x-text="activeItem?.file_size || '1.2 MB'"></span>
                        </div>
                        
                        <!-- Tombol Unduh Biru Muda -->
                        <button @click="downloadFile(activeItem)" class="mt-1 flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-[#60A5FA] text-xs font-bold text-white hover:bg-[#3B82F6] transition shadow-xs">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 flex justify-end border-t border-[#F3F4F6] pt-4">
                <button @click="closeModal()" class="rounded-xl border border-[#E5E7EB] bg-white px-5 py-2 text-xs font-semibold text-[#374151] hover:bg-gray-50 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kegiatanApp', (initialData) => ({
        items: initialData,
        searchQuery: '',
        selectedCategory: '',
        
        appliedStartDate: '',
        appliedEndDate: '',
        
        tempStartDate: '',
        tempEndDate: '',
        
        page: 1,
        itemsPerPage: 6,

        // State Modal Popup
        isModalOpen: false,
        activeItem: null,
        activeTab: 'sertifikat',

        init() {
            this.$watch('searchQuery', () => { this.page = 1; });
            this.$watch('selectedCategory', () => { this.page = 1; });
            this.$watch('appliedStartDate', () => { this.page = 1; });
            this.$watch('appliedEndDate', () => { this.page = 1; });
        },

        openModal(item) {
            this.activeItem = item;
            this.activeTab = 'sertifikat';
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },

        downloadFile(item) {
            if (!item) return;
            alert(`Mengunduh berkas:\n"${item.file_name || 'sertifikat.pdf'}"`);
        },

        formatDateStr(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${String(d.getDate()).padStart(2, '0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
        },

        getDateDisplay() {
            if (this.appliedStartDate && this.appliedEndDate) return `${this.formatDateStr(this.appliedStartDate)} - ${this.formatDateStr(this.appliedEndDate)}`;
            if (this.appliedStartDate) return `Mulai ${this.formatDateStr(this.appliedStartDate)}`;
            if (this.appliedEndDate) return `Hingga ${this.formatDateStr(this.appliedEndDate)}`;
            return 'Semua Tanggal';
        },

        get filteredData() {
            return this.items.filter(item => {
                const matchSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                    item.type.toLowerCase().includes(this.searchQuery.toLowerCase());
                
                const matchCategory = this.selectedCategory === '' || item.type === this.selectedCategory;
                
                let matchDate = true;
                if (this.appliedStartDate && this.appliedEndDate) {
                    matchDate = item.raw_date >= this.appliedStartDate && item.raw_date <= this.appliedEndDate;
                } else if (this.appliedStartDate) {
                    matchDate = item.raw_date >= this.appliedStartDate;
                } else if (this.appliedEndDate) {
                    matchDate = item.raw_date <= this.appliedEndDate;
                }

                return matchSearch && matchCategory && matchDate;
            });
        },

        get paginatedData() {
            const start = (this.page - 1) * this.itemsPerPage;
            return this.filteredData.slice(start, start + this.itemsPerPage);
        },
        get totalPages() { return Math.ceil(this.filteredData.length / this.itemsPerPage); },
        get showingStart() { return this.filteredData.length > 0 ? ((this.page - 1) * this.itemsPerPage) + 1 : 0; },
        get showingEnd() { return Math.min(this.page * this.itemsPerPage, this.filteredData.length); },

        nextPage() { if (this.page < this.totalPages) this.page++; },
        prevPage() { if (this.page > 1) this.page--; },

        resetFilters() {
            this.searchQuery = '';
            this.selectedCategory = '';
            this.tempStartDate = '';
            this.tempEndDate = '';
            this.appliedStartDate = '';
            this.appliedEndDate = '';
        },

        getStatusStyle(status) {
            const styles = {
                'done': { label: 'Dilaksanakan', bg: '#ECFDF5', fg: '#047857' },
                'ongoing': { label: 'Berlangsung', bg: '#FEF3C7', fg: '#92400E' },
                'upcoming': { label: 'Belum dimulai', bg: '#F3F4F6', fg: '#4B5563' }
            };
            return styles[status] || styles['upcoming'];
        }
    }));
});
</script>
@endsection