<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIKAP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-[#F9FAFB] text-[#111827] antialiased">
@php
    $namaUser  = $nama ?? 'Arya Bima';
    $roleUser  = 'Mahasiswa';
    $inisial   = strtoupper(mb_substr($namaUser, 0, 1));
    $mhsActive = request()->routeIs('mahasiswa.*', 'profil');

    // Submenu grup Mahasiswa; 'match' dipakai untuk status aktif (termasuk sub-tab)
    $menuMhs = [
        ['label' => 'Mata kuliah',        'route' => 'mahasiswa.mata-kuliah', 'match' => 'mahasiswa.mata-kuliah*'],
        ['label' => 'Prestasi mahasiswa', 'route' => 'mahasiswa.prestasi',    'match' => 'mahasiswa.prestasi*'],
    ];
    $subAktif = collect($menuMhs)->contains(fn ($i) => request()->routeIs($i['match']));

    // Kelas yang dipakai berulang di menu sidebar
    $navItem = 'flex items-center gap-3 rounded-lg px-3 py-2.5 transition-colors hover:bg-white/10';
    $navIcon = 'size-5 shrink-0';
@endphp

<div class="min-h-screen" x-data="{ sidebar: false }" x-on:keydown.escape.window="sidebar = false">

    {{-- Overlay (mobile) --}}
    <div x-show="sidebar" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-on:click="sidebar = false"></div>

    {{-- ================= SIDEBAR ================= --}}
    <aside class="fixed inset-y-0 left-0 z-40 flex w-[240px] flex-col gap-6 overflow-y-auto bg-linear-to-b from-[#1f66e5] from-0% via-[#1550bd] via-50% to-[#0b2f7a] to-100% px-3 py-5 text-white transition-transform duration-200"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-2.5 px-2">
            <img src="{{ Vite::asset('resources/images/logo-trk.png') }}" alt="Logo TRK" class="h-9 w-auto shrink-0">
            <span class="text-[13px] font-bold leading-[1.3] text-white">TEKNOLOGI<br>REKAYASA KOMPUTER</span>
        </a>

        <nav class="flex flex-col gap-1.5 text-[15px] text-white">
            <a href="{{ route('mahasiswa.dashboard') }}" class="{{ $navItem }}">
                <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h6v6h-6z"/><path d="M14 4h6v6h-6z"/><path d="M4 14h6v6h-6z"/><path d="M14 14h6v6h-6z"/></svg>
                Dashboard
            </a>

            <a href="#" class="{{ $navItem }}">
                <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/></svg>
                Pengajuan
            </a>

            {{-- ===== Grup Mahasiswa ===== --}}
            <div x-data="{ openMhs: {{ $mhsActive ? 'true' : 'false' }} }" class="flex flex-col gap-1.5">
                <div class="flex items-center rounded-lg transition-colors
                            {{ $subAktif ? 'bg-white/15 text-white' : ($mhsActive ? 'bg-white font-semibold text-[#1f66e5] shadow-sm' : 'hover:bg-white/10') }}">
                    {{-- Klik teks: buka halaman dashboard mahasiswa --}}
                    <a href="{{ route('mahasiswa.dashboard') }}" class="flex flex-1 items-center gap-3 rounded-l-lg px-3 py-2.5">
                        <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M8 21v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1"/><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M17 10h2a2 2 0 0 1 2 2v1"/><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M3 13v-1a2 2 0 0 1 2-2h2"/></svg>
                        <span class="flex-1">Mahasiswa</span>
                    </a>
                    {{-- Klik panah: buka/tutup submenu --}}
                    <button type="button" x-on:click="openMhs = !openMhs" :aria-expanded="openMhs"
                            aria-label="Buka atau tutup submenu Mahasiswa" class="rounded-r-lg px-3 py-2.5">
                        <svg class="size-4 transition-transform" :class="openMhs && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6"/></svg>
                    </button>
                </div>

                <div x-show="openMhs" x-cloak x-transition class="flex flex-col gap-1.5">
                    @foreach ($menuMhs as $item)
                        @php
                            $href  = Route::has($item['route']) ? route($item['route']) : '#';
                            $aktif = request()->routeIs($item['match']);
                        @endphp
                        <a href="{{ $href }}" @if ($aktif) aria-current="page" @endif
                           class="rounded-lg py-2 pl-11 pr-3 text-sm transition-colors
                                  {{ $aktif ? 'bg-white font-semibold text-[#1f66e5] shadow-sm' : 'bg-white/10 text-white hover:bg-white/20' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            {{-- ===== Akhir grup Mahasiswa ===== --}}

            <a href="#" class="{{ $navItem }}">
                <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3-5a9 9 0 0 0 6-8a3 3 0 0 0-3-3a9 9 0 0 0-8 6a6 6 0 0 0-5 3"/><path d="M7 14a6 6 0 0 0-3 6a6 6 0 0 0 6-3"/><path d="M15 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0-2 0"/></svg>
                Kegiatan
            </a>
        </nav>
    </aside>

    <div class="lg:pl-[240px]">
        {{-- ================= TOPBAR ================= --}}
        <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-[#E5E7EB] bg-white px-4 py-3 sm:px-8">
            <button type="button" class="rounded-md border border-[#E5E7EB] bg-white p-2 lg:hidden" x-on:click="sidebar = true" aria-label="Buka menu">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>
            </button>

            {{-- Kolom pencarian (nonaktif). Hapus tanda komentar di bawah kalau mau dipakai lagi.
            <form action="#" method="GET" class="flex min-w-0 flex-1 items-center gap-2 rounded-lg border border-[#A8A8A8] bg-white px-4 py-2 focus-within:border-[#27F]">
                <svg class="size-5 shrink-0 text-[#6B7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0-14 0"/><path d="M21 21l-6-6"/></svg>
                <input type="search" name="cari" placeholder="Cari" class="min-w-0 flex-1 bg-transparent text-base text-[#0A0A0A] outline-none placeholder:text-[#A8A8A8]">
            </form>
            --}}

            {{-- ml-auto: dorong blok profil (foto, nama, role) ke kanan --}}
            <div class="relative ml-auto" x-data="{ menu: false }" x-on:click.outside="menu = false">
                <button type="button" x-on:click="menu = !menu" :aria-expanded="menu"
                        class="flex items-center gap-3 rounded-lg p-1.5 transition-colors hover:bg-[#F9FAFB]">
                    <span class="flex size-10 items-center justify-center rounded-full bg-[#DCE7FB] text-sm font-semibold text-[#00235C]">{{ $inisial }}</span>
                    <span class="hidden text-left sm:block">
                        <span class="block text-sm font-semibold leading-5">{{ $namaUser }}</span>
                        <span class="block text-xs leading-4 text-[#6B7280]">{{ $roleUser }}</span>
                    </span>
                    <svg class="size-5 text-[#0A0A0A]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6"/></svg>
                </button>
                <div x-show="menu" x-cloak x-transition class="absolute right-0 mt-2 w-44 overflow-hidden rounded-lg border border-[#E5E7EB] bg-white py-1 shadow-lg">
                    <a href="{{ route('profil') }}" class="block px-4 py-2 text-sm hover:bg-[#F2F6FD]">Profil saya</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-[#FF3F3F] hover:bg-[#FFF1F1]">Keluar</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-[1280px] px-4 pb-10 pt-6 sm:px-8">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>