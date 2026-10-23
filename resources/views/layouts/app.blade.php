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

    {{-- Alpine.js: untuk interaksi kecil (dropdown, tambah/hapus baris, dll.) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</head>
<body class="h-dvh overflow-hidden bg-page font-sans text-gray-900 antialiased">
{{-- menu = status menu samping di layar kecil (HP / tablet). Di layar lebar menu selalu tampil. --}}
<div class="flex h-full flex-col" x-data="{ menu: false }" x-on:keydown.escape.window="menu = false">

    {{-- HEADER --}}
    <header class="flex shrink-0 flex-wrap items-center gap-x-4 gap-y-3 bg-white px-4 py-3 lg:gap-x-6 lg:py-3.5 lg:pr-6 lg:pl-0">
        <div class="flex items-center gap-2 lg:w-60 lg:shrink-0 lg:justify-center">
            {{-- Tombol menu: hanya tampil di layar kecil --}}
            <button type="button" x-on:click="menu = true" aria-label="Buka menu" aria-controls="menu-utama" :aria-expanded="menu"
                    class="-ml-1 flex size-10 cursor-pointer items-center justify-center rounded-lg hover:bg-gray-100 lg:hidden">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <span class="text-2xl font-bold tracking-wide">LOGO</span>
        </div>

        <div class="hidden h-9 min-w-0 items-center gap-2.5 lg:flex lg:flex-[1_1_280px] rounded-md border border-gray-400 bg-white px-3">
            <svg class="size-5 shrink-0 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/>
            </svg>
            <input type="search" aria-label="Cari" placeholder="Cari"
                   class="min-w-0 flex-1 border-0 bg-transparent text-[15px] outline-none placeholder:text-gray-400">
        </div>

        <div class="ml-auto flex items-center gap-4 lg:ml-0">
            {{-- Lonceng: membuka halaman Notifikasi. Angka merah = jumlah belum dibaca (data tiruan: 3).
                 Nanti backend mengganti angka 3 di x-data. Angkanya ikut berubah saat notifikasi dibaca. --}}
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
            {{-- Klik akun untuk membuka halaman Profil --}}
            <a href="{{ route('profil') }}" aria-label="Buka profil akun"
               class="-m-1 flex items-center gap-2.5 rounded-lg p-1 hover:bg-gray-100">
                {{-- Kalau file public/images/kuwat.jpg ada, fotonya dipakai. Kalau tidak, tampil inisial. --}}
                @if (file_exists(public_path('images/kuwat.jpg')))
                    <img src="{{ asset('images/kuwat.jpg') }}" alt="" class="size-[34px] rounded-full object-cover">
                @else
                    <span class="flex size-[34px] items-center justify-center rounded-full bg-red-600 text-[13px] font-semibold text-white">KS</span>
                @endif
                <div class="hidden flex-col leading-tight sm:flex">
                    {{-- nanti diganti data user yang login --}}
                    <span class="text-sm font-semibold">Kuwat</span>
                    <span class="text-xs text-gray-600">Dosen</span>
                </div>
            </a>
        </div>
    </header>

    {{-- SIDEBAR + ISI HALAMAN --}}
    {{-- Hanya <main> yang bisa di-scroll. Header dan sidebar tetap di tempat. --}}
    <div class="flex min-h-0 flex-1">
        {{-- Latar gelap di belakang menu (layar kecil). Klik = tutup menu. --}}
        <div x-show="menu" x-cloak x-transition.opacity x-on:click="menu = false"
             class="fixed inset-0 z-30 bg-black/40 lg:hidden" aria-hidden="true"></div>

        <x-sidebar />

        <main class="flex min-w-0 flex-1 flex-col gap-6 overflow-y-auto px-4 pt-6 pb-10 sm:px-6 sm:pt-8 sm:pb-12">
            @yield('content')
        </main>
    </div>

    {{-- Alert / callout global: lihat components/alert.blade.php --}}
    <x-alert />

</div>

@stack('scripts')
</body>
</html>