@extends('layouts.app')

@section('title', 'Kegiatan - Teknologi Rekayasa Komputer')

@section('content')
@php
    // Data Statistik Ringkasan
    $summaryCards = [
        [
            'label' => 'LOMBA', 
            'value' => 12, 
            'unit' => 'Kegiatan',
            'border_accent' => '#2277FF', 
            'bg_icon' => '#EFF6FF', 
            'ic_color' => '#2563EB', 
            'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'
        ],
        [
            'label' => 'VOLUNTEER', 
            'value' => 2, 
            'unit' => 'Kegiatan',
            'border_accent' => '#F59E0B', 
            'bg_icon' => '#FFFBEB', 
            'ic_color' => '#D97706', 
            'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'
        ],
        [
            'label' => 'KKL', 
            'value' => 1, 
            'unit' => 'Kegiatan',
            'border_accent' => '#10B981', 
            'bg_icon' => '#ECFDF5', 
            'ic_color' => '#059669', 
            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
        ],
        [
            'label' => 'MAGANG', 
            'value' => 9, 
            'unit' => 'Kegiatan',
            'border_accent' => '#8B5CF6', 
            'bg_icon' => '#F5F3FF', 
            'ic_color' => '#7C3AED', 
            'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
        ],
        [
            'label' => 'PENGABDIAN', 
            'value' => 12, 
            'unit' => 'Kegiatan',
            'border_accent' => '#EC4899', 
            'bg_icon' => '#FDF2F8', 
            'ic_color' => '#DB2777', 
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
        ],
        [
            'label' => 'PENELITIAN', 
            'value' => 1, 
            'unit' => 'Kegiatan',
            'border_accent' => '#06B6D4', 
            'bg_icon' => '#ECFEFF', 
            'ic_color' => '#0891B2', 
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
        ],
    ];

    $semesters = [
        ['label' => 'Sem 1', 'value' => 12],
        ['label' => 'Sem 2', 'value' => 8],
        ['label' => 'Sem 3', 'value' => 15],
        ['label' => 'Sem 4', 'value' => 6],
        ['label' => 'Sem 5', 'value' => 9],
        ['label' => 'Sem 6', 'value' => 4],
    ];

    $page1 = [
        ['id' => 1, 'name' => 'FKBSE 2025', 'type' => 'Pengabdian', 'date' => '12 Nov 2025', 'raw_date' => '2025-11-12', 'status' => 'done', 'note' => 'Terima kasih atas kontribusi anda.', 'action' => 'Lihat detail', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Himpunan Mahasiswa TRK', 'file_name' => 'sertifikat-fkbse-2025.pdf', 'file_size' => '1.4 MB'],
        ['id' => 2, 'name' => 'KMIPN VIII 2026', 'type' => 'Lomba', 'date' => '10 Sept 2026', 'raw_date' => '2026-09-10', 'status' => 'done', 'note' => 'Selamat atas pencapaiannya.', 'action' => 'Lihat detail', 'capaian' => 'Finalis', 'tingkat' => 'Nasional', 'penyelenggara' => 'Politeknik Negeri', 'file_name' => 'sertifikat-kmipn-viii-2026.pdf', 'file_size' => '2.1 MB'],
        ['id' => 3, 'name' => 'IMAKEN Progress In Action 2026', 'type' => 'Volunteer', 'date' => '30 Nov 2026', 'raw_date' => '2026-11-30', 'status' => 'done', 'note' => 'Kegiatan telah selesai dilaksanakan.', 'action' => 'Lihat detail', 'capaian' => 'Panitia', 'tingkat' => 'Wilayah', 'penyelenggara' => 'IMAKEN', 'file_name' => 'sertifikat-imaken-2026.pdf', 'file_size' => '1.1 MB'],
        ['id' => 4, 'name' => 'Google Singapura', 'type' => 'Kunjungan Industri', 'date' => '06 Mar 2027', 'raw_date' => '2027-03-06', 'status' => 'done', 'note' => 'Kunjungan industri telah diselesaikan.', 'action' => 'Lihat detail', 'capaian' => 'Peserta', 'tingkat' => 'Internasional', 'penyelenggara' => 'Google Asia Pacific', 'file_name' => 'sertifikat-google-singapura.pdf', 'file_size' => '3.0 MB'],
        ['id' => 5, 'name' => 'KKL Schneider Electric', 'type' => 'Kuliah Kerja Lapangan', 'date' => '04 Agu 2027', 'raw_date' => '2027-08-04', 'status' => 'done', 'note' => 'Laporan KKL disetujui.', 'action' => 'Lihat detail', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Schneider Electric Indonesia', 'file_name' => 'sertifikat-kkl-schneider.pdf', 'file_size' => '1.8 MB'],
        ['id' => 6, 'name' => 'Amazon Web Service', 'type' => 'Magang', 'date' => '01 Sept 2027', 'raw_date' => '2027-09-01', 'status' => 'done', 'note' => 'Program magang selesai.', 'action' => 'Lihat detail', 'capaian' => 'Intern', 'tingkat' => 'Internasional', 'penyelenggara' => 'Amazon Web Services', 'file_name' => 'sertifikat-aws-internship.pdf', 'file_size' => '2.5 MB'],
    ];

    $page2 = [
        ['id' => 7, 'name' => 'Hackathon Nasional 2025', 'type' => 'Lomba', 'date' => '15 Jan 2025', 'raw_date' => '2025-01-15', 'status' => 'done', 'note' => 'Juara 2 Kategori Software.', 'action' => 'Lihat detail', 'capaian' => 'Juara 2', 'tingkat' => 'Nasional', 'penyelenggara' => 'Kementerian Kominfo', 'file_name' => 'sertifikat-hackathon-2025.pdf', 'file_size' => '1.9 MB'],
        ['id' => 8, 'name' => 'Workshop IoT & Embedded System', 'type' => 'Pelatihan', 'date' => '22 Feb 2025', 'raw_date' => '2025-02-22', 'status' => 'done', 'note' => 'Sertifikat kompetensi terbit.', 'action' => 'Lihat detail', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Lab Embedded TRK', 'file_name' => 'sertifikat-workshop-iot.pdf', 'file_size' => '1.2 MB'],
        ['id' => 9, 'name' => 'Studi Independen AI Micro-Credential', 'type' => 'Magang', 'date' => '10 Jun 2025', 'raw_date' => '2025-06-10', 'status' => 'done', 'note' => 'Selesai dengan nilai A.', 'action' => 'Lihat detail', 'capaian' => 'Lulus', 'tingkat' => 'Nasional', 'penyelenggara' => 'Kampus Merdeka x AI Center', 'file_name' => 'sertifikat-studi-independen-ai.pdf', 'file_size' => '2.8 MB'],
        ['id' => 10, 'name' => 'Pengabdian Desa Digital Banyumanik', 'type' => 'Pengabdian', 'date' => '18 Agu 2025', 'raw_date' => '2025-08-18', 'status' => 'done', 'note' => 'Selesai dilaksanakan.', 'action' => 'Lihat detail', 'capaian' => 'Tim Pelaksana', 'tingkat' => 'Kabupaten/Kota', 'penyelenggara' => 'Jurusan Elektro Polines', 'file_name' => 'sertifikat-pengabdian-banyumanik.pdf', 'file_size' => '1.5 MB'],
        ['id' => 11, 'name' => 'Seminar International TRK Tech', 'type' => 'Seminar', 'date' => '05 Okt 2025', 'raw_date' => '2025-10-05', 'status' => 'done', 'note' => 'Peserta Aktif.', 'action' => 'Lihat detail', 'capaian' => 'Peserta', 'tingkat' => 'Internasional', 'penyelenggara' => 'TRK Polines', 'file_name' => 'sertifikat-seminar-international.pdf', 'file_size' => '1.3 MB'],
        ['id' => 12, 'name' => 'Lomba Karya Tulis Ilmiah 2025', 'type' => 'Penelitian', 'date' => '12 Des 2025', 'raw_date' => '2025-12-12', 'status' => 'done', 'note' => 'Finalis 10 Besar.', 'action' => 'Lihat detail', 'capaian' => 'Finalis', 'tingkat' => 'Nasional', 'penyelenggara' => 'Universitas Indonesia', 'file_name' => 'sertifikat-lkti-2025.pdf', 'file_size' => '1.7 MB'],
    ];

    $page3 = [
        ['id' => 13, 'name' => 'Lomba CTF Cyber Security 2024', 'type' => 'Lomba', 'date' => '10 Mar 2024', 'raw_date' => '2024-03-10', 'status' => 'done', 'note' => 'Peringkat 5 Nasional.', 'action' => 'Lihat detail', 'capaian' => 'Peringkat 5', 'tingkat' => 'Nasional', 'penyelenggara' => 'BSSN', 'file_name' => 'sertifikat-ctf-2024.pdf', 'file_size' => '2.0 MB'],
        ['id' => 14, 'name' => 'Pelatihan Cloud Architecture AWS', 'type' => 'Pelatihan', 'date' => '14 Mei 2024', 'raw_date' => '2024-05-14', 'status' => 'done', 'note' => 'Sertifikat AWS re:Start.', 'action' => 'Lihat detail', 'capaian' => 'Peserta', 'tingkat' => 'Internasional', 'penyelenggara' => 'AWS Educate', 'file_name' => 'sertifikat-aws-restart.pdf', 'file_size' => '1.6 MB'],
        ['id' => 15, 'name' => 'Volunteer Mengajar AI Sekolah Dasar', 'type' => 'Volunteer', 'date' => '20 Jul 2024', 'raw_date' => '2024-07-20', 'status' => 'done', 'note' => 'Piagam Penghargaan.', 'action' => 'Lihat detail', 'capaian' => 'Relawan', 'tingkat' => 'Kabupaten/Kota', 'penyelenggara' => 'Komunitas AI Semarang', 'file_name' => 'sertifikat-volunteer-ai.pdf', 'file_size' => '1.1 MB'],
        ['id' => 16, 'name' => 'Bootcamp Fullstack Web Developer', 'type' => 'Pelatihan', 'date' => '11 Sep 2024', 'raw_date' => '2024-09-11', 'status' => 'done', 'note' => 'Lulus Capstone Project.', 'action' => 'Lihat detail', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Dicoding Indonesia', 'file_name' => 'sertifikat-fullstack-dev.pdf', 'file_size' => '2.2 MB'],
        ['id' => 17, 'name' => 'Kunjungan Industri Telkomsel Digital', 'type' => 'Kunjungan Industri', 'date' => '02 Nov 2024', 'raw_date' => '2024-11-02', 'status' => 'done', 'note' => 'Selesai diikuti.', 'action' => 'Lihat detail', 'capaian' => 'Peserta', 'tingkat' => 'Nasional', 'penyelenggara' => 'Telkomsel Smart Office', 'file_name' => 'sertifikat-telkomsel-visit.pdf', 'file_size' => '1.4 MB'],
        ['id' => 18, 'name' => 'Kompetisi Robotics & Automation', 'type' => 'Lomba', 'date' => '18 Des 2024', 'raw_date' => '2024-12-18', 'status' => 'done', 'note' => 'Juara Harapan 1.', 'action' => 'Lihat detail', 'capaian' => 'Juara Harapan 1', 'tingkat' => 'Nasional', 'penyelenggara' => 'ITB Robotics Club', 'file_name' => 'sertifikat-robotics-2024.pdf', 'file_size' => '1.8 MB'],
    ];
    
    $allActivities = array_merge($page1, $page2, $page3);
@endphp

<div x-data="kegiatanApp(@js($allActivities))" class="flex flex-col gap-6 p-4 md:px-6 md:py-8 lg:px-12 bg-[#F9FAFB]">
    
    <!-- Grid Ringkasan Kegiatan -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        @foreach($summaryCards as $c)
            <div class="group relative overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_2px_8px_rgba(0,0,0,0.02)] transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-[#D1D5DB]">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 transition-all duration-300 group-hover:w-2" style="background-color: {{ $c['border_accent'] }};"></div>
                
                <div class="flex items-center justify-between pl-2">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider transition-colors duration-300 group-hover:text-[#111827]">{{ $c['label'] }}</span>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-3xl font-bold text-[#111827] leading-none">{{ $c['value'] }}</span>
                            <span class="text-[11px] font-medium text-[#9CA3AF]">{{ $c['unit'] }}</span>
                        </div>
                    </div>
                    
                    <div class="flex size-11 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110" style="background-color: {{ $c['bg_icon'] }};">
                        <svg class="size-5 transition-transform duration-300 group-hover:rotate-6" fill="none" stroke="{{ $c['ic_color'] }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $c['icon'] }}"></path>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Grafik Kegiatan per Semester (Dengan Efek Hover Fokus & Zoom Card) -->
    <section class="flex flex-col gap-2 rounded-2xl border border-[#E5E7EB] bg-white p-4 shadow-[0_4px_12px_rgba(0,0,0,0.05)] transition-all duration-300 ease-out hover:scale-[1.01] hover:shadow-lg hover:border-[#D1D5DB]">
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
                        <div class="w-full max-w-12 rounded-t-lg bg-[#2277FF] transition-all duration-300 group-hover:bg-[#1A5BCC] group-hover:scale-105 origin-bottom" style="height: {{ $s['value'] * 4 }}px"></div>
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
            <!-- Button Lihat Semua / Batasi Tampilan -->
            <button @click="showAll = !showAll; page = 1" 
                    class="rounded-lg bg-[#EFF6FF] px-4 py-2 text-xs font-semibold text-[#2563EB] hover:bg-blue-100 transition">
                <span x-text="showAll ? 'Batasi tampilan' : 'Lihat semua'"></span>
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

                <div x-show="paginatedData.length === 0" x-cloak class="flex items-center justify-center p-8 text-sm text-[#6B7280]">
                    Tidak ada kegiatan yang sesuai dengan filter Anda.
                </div>

                <template x-for="(a, index) in paginatedData" :key="index">
                    <div class="flex min-h-[58px] items-center gap-4 border-b border-[#E5E7EB] px-4 py-2.5 last:border-b-0 hover:bg-[#F9FAFB] transition">
                        <div class="flex flex-1 items-center gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-xs font-bold text-[#2563EB]" x-text="showAll ? index + 1 : ((page - 1) * itemsPerPage) + index + 1"></div>
                            <div class="flex flex-1 flex-col gap-0.5">
                                <span class="text-xs font-semibold text-[#111827]" x-text="a.name"></span>
                                <span class="text-[11px] text-[#6B7280]" x-text="a.type"></span>
                            </div>
                        </div>
                        <div class="w-[115px] text-xs text-[#6B7280]" x-text="a.date"></div>
                        <div class="flex w-[235px] flex-col items-start gap-1">
                            <span class="rounded-full bg-[#ECFDF5] px-2.5 py-1 text-[11px] font-semibold text-[#047857]">
                                Selesai
                            </span>
                            <span x-show="a.note" class="text-[11px] text-[#6B7280]" x-text="a.note"></span>
                        </div>
                        <div class="w-[195px] flex justify-end">
                            <button @click="openModal(a)" class="flex items-center gap-1.5 text-xs font-semibold text-[#2563EB] hover:underline">
                                <span>Lihat detail</span>
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer Info & Pagination -->
        <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
            <span class="text-xs text-[#6B7280]">
                Menampilkan <span x-text="showAll ? `1–${filteredData.length}` : `${showingStart}–${showingEnd}`"></span> dari <span x-text="filteredData.length"></span> Kegiatan
            </span>
            
            <div class="flex items-center gap-2 text-xs" x-show="!showAll && totalPages > 0">
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

    <!-- Modal Detail Bukti Prestasi -->
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
            
            <div class="flex items-start justify-between border-b border-[#F3F4F6] pb-4">
                <div>
                    <span class="text-xs font-semibold text-[#6B7280]">Bukti prestasi</span>
                    <h3 class="text-xl font-bold text-[#111827]" x-text="activeItem?.name"></h3>
                </div>
                <button @click="closeModal()" class="rounded-lg p-1 text-[#9CA3AF] hover:bg-gray-100 hover:text-[#111827] transition">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-12">
                <div class="lg:col-span-7 flex flex-col gap-3">
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

                    <div class="relative flex min-h-[300px] flex-col items-center justify-center rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-4 text-center">
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

                        <div x-show="activeTab === 'foto'" class="w-full flex flex-col items-center justify-center min-h-[220px]">
                            <svg class="size-12 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="mt-2 text-xs font-semibold text-[#4B5563]">Dokumentasi Foto Kegiatan</span>
                            <span class="text-[11px] text-[#9CA3AF]">Foto saat kegiatan berlangsung</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 flex flex-col justify-between gap-4">
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

                    <div class="rounded-xl border border-[#E5E7EB] bg-white p-4 flex flex-col gap-3">
                        <h4 class="text-xs font-bold text-[#111827]">Dokumen</h4>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[11px] text-[#6B7280]">Sertifikat</span>
                            <span class="text-xs font-semibold text-[#111827] break-all" x-text="activeItem?.file_name || 'sertifikat-kegiatan.pdf'"></span>
                            <span class="text-[10px] text-[#9CA3AF]" x-text="activeItem?.file_size || '1.2 MB'"></span>
                        </div>
                        
                        <button @click="downloadFile(activeItem)" class="mt-1 flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-[#2277FF] text-xs font-bold text-white hover:bg-blue-600 transition shadow-xs">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh Sertifikat</span>
                        </button>
                    </div>
                </div>
            </div>

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
        showAll: false,

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
            if (this.showAll) {
                return this.filteredData;
            }
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
            this.showAll = false;
        }
    }));
});
</script>
@endsection