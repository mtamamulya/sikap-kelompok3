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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-[#F9FAFB] font-sans text-[#111827] antialiased">

<div class="flex min-h-screen bg-[#F7F7F7] text-[#111827]">
    @include('layouts.partials.pengajuan-sidebar')

    <div class="flex min-w-0 flex-1 flex-col">
        @include('layouts.partials.pengajuan-header')
        <main class="min-w-0 flex-1">
            <div class="min-w-0 px-3 py-4 sm:px-6 sm:py-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    <x-alert />
    <x-preview-dokumen />
</div>

</body>
</html>