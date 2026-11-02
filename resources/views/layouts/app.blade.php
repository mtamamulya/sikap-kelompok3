<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teknologi Rekayasa Komputer')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
</head>

<body class="font-sans antialiased bg-[#F9FAFB] text-[#111827]">

@php
    $isDosenLayout = request()->routeIs(
        'dosen.dashboard',
        'penelitian.*',
        'pengabdian.*',
        'pengajaran.*',
        'prestasi.*',
        'kegiatan.*',
        'pengajuan.dosen*',
        'pengajuan.mahasiswa*',
        'berita.*',
        'notifikasi',
        'profil'
    );
@endphp

<div class="flex min-h-screen" x-data="{ menu: false }">
    @unless(View::hasSection('hide_sidebar'))
        @if ($isDosenLayout)
            @include('components.sidebar')
        @elseif(View::exists('components.navbar'))
            @include('components.navbar')
        @else
            @include('navbar')
        @endif
    @endunless

    @if ($isDosenLayout)
        <button type="button" x-show="menu" x-cloak x-on:click="menu = false"
                class="fixed inset-0 z-30 bg-black/40 lg:hidden" aria-label="Tutup menu"></button>
    @endif

    <div class="flex min-w-0 flex-1 flex-col">
        @unless(View::hasSection('hide_header'))
            @if ($isDosenLayout)
                @include('components.header-dosen')
            @elseif(View::exists('components.header'))
                @include('components.header')
            @else
                @include('header')
            @endif
        @endunless

        <main class="flex-1 relative">
            @yield('content')
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        function syncGlobalAvatar() {
            const savedAvatar = localStorage.getItem('user_avatar_base64');
            if (savedAvatar) {
                const avatars = document.querySelectorAll('.global-avatar-sync');
                avatars.forEach(img => {
                    img.src = savedAvatar;
                    img.style.display = 'block';
                });
            }
        }
        syncGlobalAvatar();
        window.addEventListener('avatar-updated', syncGlobalAvatar);
    });
</script>

@stack('scripts')

</body>
</html>