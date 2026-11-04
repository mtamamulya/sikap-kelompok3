<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title','Dashboard mahasiswa') · SIKAP</title><link rel="icon" href="{{ asset('images/logo_trk.png') }}"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">@vite(['resources/css/app.css','resources/js/app.js'])@stack('styles')</head>
@php
    $namaUser = $nama ?? request()->user()?->mahasiswa?->nama ?? 'Belum masuk';
    $inisial = strtoupper(mb_substr($namaUser,0,1));
    $studentMenus = [
        ['Dashboard', 'mahasiswa.dashboard', ['mahasiswa.dashboard']],
        ['Pengajuan', 'pengajuan.index', ['pengajuan.*']],
        ['Kegiatan', 'kegiatan.index', ['kegiatan.*']],
    ];
    $mahasiswaActive = request()->routeIs('mahasiswa.index', 'mahasiswa.mata-kuliah*', 'mahasiswa.prestasi*');
    $mahasiswaSubmenuActive = request()->routeIs('mahasiswa.mata-kuliah*', 'mahasiswa.prestasi*');
@endphp
<body class="frontend-shell mahasiswa-shell bg-[#F9FAFB] text-[#111827] antialiased">
<div class="min-h-screen" data-frontend-shell data-menu-state="sidebar" x-data="{ sidebar:false }" x-on:keydown.escape.window="sidebar=false">
    <div x-show="sidebar" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-on:click="sidebar=false" aria-hidden="true"></div>
    <aside id="student-navigation" data-frontend-navigation :data-open="sidebar ? 'true' : 'false'" class="student-sidebar max-lg:invisible max-lg:data-[open=true]:visible fixed inset-y-0 left-0 z-40 flex w-60 flex-col overflow-y-auto border-r border-gray-100 bg-white p-3 transition-transform duration-200" :class="sidebar?'translate-x-0':'-translate-x-full lg:translate-x-0'">
        <div class="flex items-center justify-between gap-2 pb-6 pt-2"><a class="flex items-center gap-2 text-[#003385]" href="{{ route('mahasiswa.dashboard') }}"><img src="{{ asset('images/logo_trk.png') }}" class="h-10 w-auto shrink-0" alt="Logo TRK"><span class="text-xs font-bold leading-tight">TEKNOLOGI<br>REKAYASA KOMPUTER</span></a><button type="button" class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 lg:hidden" aria-label="Tutup menu" x-on:click="sidebar=false">×</button></div>
        <nav class="flex flex-1 flex-col gap-2" aria-label="Menu mahasiswa">
            @foreach($studentMenus as [$label, $route, $activeRoutes])
                <a class="student-nav-link {{ request()->routeIs(...$activeRoutes) ? 'is-active' : '' }}" href="{{ route($route) }}" @if(request()->routeIs(...$activeRoutes)) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
            <div x-data="{ open: @js($mahasiswaSubmenuActive) }" class="flex flex-col gap-1">
                <div class="flex items-center gap-1">
                    <a class="student-nav-link flex-1 {{ $mahasiswaActive ? 'is-active' : '' }}" href="{{ route('mahasiswa.index') }}" @if(request()->routeIs('mahasiswa.index')) aria-current="page" @endif>Mahasiswa</a>
                    <button type="button" class="flex size-10 shrink-0 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100" x-on:click="open = !open" :aria-expanded="open" aria-controls="student-submenu">
                        <svg class="size-4 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        <span class="sr-only">Buka atau tutup submenu Mahasiswa</span>
                    </button>
                </div>
                <div id="student-submenu" x-show="open" x-cloak x-transition class="flex flex-col gap-1 pl-3">
                    @foreach(['Mata kuliah' => 'mahasiswa.mata-kuliah', 'Prestasi mahasiswa' => 'mahasiswa.prestasi'] as $label => $route)
                        <a class="student-nav-link {{ request()->routeIs($route, $route.'.*') ? 'is-active' : '' }}" href="{{ route($route) }}" @if(request()->routeIs($route, $route.'.*')) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </nav>
        <a href="{{ route('dosen.dashboard') }}" class="student-nav-link mt-6 text-brand">Tampilan dosen</a>
    </aside>
    <a href="#main-content" class="skip-link">Lewati navigasi</a>
    <div class="min-w-0 lg:pl-60">
        <header data-frontend-header class="sticky top-0 z-20 flex min-h-[68px] items-center gap-3 border-b border-gray-100 bg-white px-4 py-3 sm:px-6">
            <button type="button" x-on:click="sidebar=true" aria-label="Buka menu" aria-controls="student-navigation" :aria-expanded="sidebar" class="flex size-10 items-center justify-center rounded-lg border border-gray-200 lg:hidden"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
            <div class="ml-auto flex items-center gap-3"><span class="flex size-10 items-center justify-center rounded-full bg-blue-50 text-sm font-semibold text-brand">{{ $inisial }}</span><span class="hidden text-sm sm:block"><strong class="block">{{ $namaUser }}</strong><span class="text-xs text-gray-500">Mahasiswa</span></span></div>
        </header>
        <main id="main-content" tabindex="-1" class="frontend-main mx-auto w-full max-w-[1440px] px-4 pb-10 pt-6 sm:px-6">@yield('content')</main>
    </div>
</div>@stack('scripts')
</body></html>
