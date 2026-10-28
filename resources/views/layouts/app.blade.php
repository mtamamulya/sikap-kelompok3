<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIKAP - Teknologi Rekayasa Komputer')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-[#F9FAFB] text-[#111827] antialiased">

<div class="min-h-screen flex" x-data="{ sidebar: false }" x-on:keydown.escape.window="sidebar = false">

    {{-- Overlay untuk tampilan Mobile --}}
    <div x-show="sidebar" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-on:click="sidebar = false"></div>

    {{-- Sidebar Navigasi dari file resources/views/navbar.blade.php --}}
    @include('navbar')

    {{-- Area Konten Utama & Header Topbar --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Header Topbar dari file resources/views/header.blade.php --}}
        @include('header')

        {{-- Isi Konten Halaman --}}
        <main class="w-full flex-1">
            @yield('content')
        </main>
    </div>

</div>

</body>
</html>