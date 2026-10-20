<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIKAP') | SIKAP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</head>
<body class="h-screen overflow-hidden bg-page font-sans text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex h-full flex-col">

    {{-- HEADER --}}
    <header class="flex shrink-0 items-center justify-between gap-x-4 border-b border-gray-100 bg-white px-4 py-3.5 md:px-6">
        
        <!-- Sisi Kiri: Tombol Hamburger (Mobile) & Logo -->
        <div class="flex items-center gap-3 md:w-60 md:shrink-0">
            <!-- Tombol Hamburger (Hanya Tampil di Layar Kecil/HP) -->
            <button @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-1 text-gray-600 hover:bg-gray-100 md:hidden" aria-label="Buka Menu">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="text-xl font-bold tracking-wide md:text-2xl">LOGO</div>
        </div>

        <!-- Tengah: Search Bar (Sembunyi di HP Sangat Kecil, Otomatis Melebar di Tablet/Laptop) -->
        <div class="hidden max-w-md flex-1 items-center gap-2.5 rounded-md border border-gray-300 bg-white px-3 py-1.5 sm:flex">
            <svg class="size-5 shrink-0 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/>
            </svg>
            <input type="search" aria-label="Cari" placeholder="Cari"
                   class="min-w-0 flex-1 border-0 bg-transparent text-[15px] outline-none placeholder:text-gray-400">
        </div>

        <!-- Sisi Kanan: Notifikasi & Profil -->
        <div class="flex items-center gap-2 sm:gap-4">
            {{-- Lonceng Notifikasi --}}
            <a href="{{ route('notifikasi') }}"
               x-data="{ n: 3 }" x-on:notif-count.window="n = $event.detail"
               :aria-label="`Notifikasi, ${n} belum dibaca`"
               @if (request()->routeIs('notifikasi')) aria-current="page" @endif
               class="relative flex size-10 items-center justify-center rounded-lg {{ request()->routeIs('notifikasi') ? 'bg-blue-100 text-brand' : 'hover:bg-gray-100' }}">
                <svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 16.5V11a6 6 0 0 1 12 0v5.5l1.5 1.5h-15zM10 20.5a2 2 0 0 0 4 0"/>
                </svg>
                <span x-show="n > 0" x-text="n" aria-hidden="true"
                      class="absolute top-0.5 right-0 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-semibold text-white"></span>
            </a>

            {{-- Profil Akun --}}
            <a href="{{ route('profil') }}" aria-label="Buka profil akun"
               class="-m-1 flex items-center gap-2.5 rounded-lg p-1 hover:bg-gray-100">
                @if (file_exists(public_path('images/kuwat.jpg')))
                    <img src="{{ asset('images/kuwat.jpg') }}" alt="" class="size-[34px] rounded-full object-cover">
                @else
                    <span class="flex size-[34px] items-center justify-center rounded-full bg-red-600 text-[13px] font-semibold text-white">KS</span>
                @endif
                <div class="hidden flex-col leading-tight sm:flex">
                    <span class="text-sm font-semibold">Kuwat</span>
                    <span class="text-xs text-gray-600">Dosen</span>
                </div>
            </a>
        </div>
    </header>

    {{-- SIDEBAR + ISI HALAMAN --}}
    <div class="relative flex min-h-0 flex-1">
        
        {{-- BACKDROP GELAP (Hanya Muncul di HP saat Sidebar Dibuka) --}}
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-20 bg-black/50 transition-opacity md:hidden"
             x-transition.opacity>
        </div>

        {{-- WRAPPER SIDEBAR RESPONSIF --}}
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
             class="fixed inset-y-0 left-0 z-30 w-60 transform bg-white transition-transform duration-200 ease-in-out md:relative md:translate-x-0">
            <x-sidebar />
        </div>

        {{-- MAIN CONTENT --}}
        <main class="flex min-w-0 flex-1 flex-col gap-6 overflow-y-auto px-4 pt-6 pb-12 md:px-6 md:pt-8">
            @yield('content')
        </main>
    </div>

</div>

@stack('scripts')
</body>
</html>