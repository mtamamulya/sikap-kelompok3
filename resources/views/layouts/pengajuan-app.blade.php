<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pengajuan')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
<div class="flex min-h-screen bg-[#F7F7F7] text-[#111827]">
    @include('layouts.partials.pengajuan-sidebar')

    <div class="flex min-w-0 flex-1 flex-col">
        @include('layouts.partials.pengajuan-header')
        <main class="min-w-0 flex-1">
            @if (session('success'))
                <div class="mx-4 mt-4 rounded-lg bg-[#ECFDF5] px-4 py-3 text-sm text-[#047857] sm:mx-6 lg:mx-12">{{ session('success') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>