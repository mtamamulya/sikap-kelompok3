<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIKAP - Teknologi Rekayasa Komputer')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_trk.png') }}?v=2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-[#F9FAFB] font-sans text-[#111827] antialiased">

<div class="min-h-screen flex" x-data="{ sidebar: false }" x-on:keydown.escape.window="sidebar = false">

    {{-- Overlay untuk tampilan Mobile --}}
    <div x-show="sidebar" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-on:click="sidebar = false"></div>

    {{-- Sidebar Navigasi: resources/views/mahasiswa/navbar.blade.php --}}
    @include('mahasiswa.navbar')

    {{-- Area Konten Utama & Header Topbar --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Header Topbar: resources/views/mahasiswa/header.blade.php --}}
        @include('mahasiswa.header')

        {{-- Isi Konten Halaman --}}
        <main class="w-full flex-1 px-4 pt-6 pb-10 sm:px-6 sm:pt-8 sm:pb-12">
            @yield('content')
        </main>
    </div>

    {{-- Alert / callout global: lihat components/alert.blade.php --}}
    <x-alert />

    {{-- Pratinjau dokumen: lihat components/preview-dokumen.blade.php --}}
    <x-preview-dokumen />

</div>

</body>
</html>git add resources/views/layouts/mahasiswa.blade.php
git commit -m "fix: pulihkan layout mahasiswa"
git push