<!doctype html>
<html lang="id" dosen-shell>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DOSEN') | DOSEN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_trk.png') }}?v=2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js dimuat melalui resources/js/app.js. --}}
</head>
@php $namaDosen = request()->user()?->dosen?->nama ?? 'Pratinjau dosen'; @endphp
<body class="frontend-shell dosen-shell h-dvh overflow-hidden bg-page font-sans text-gray-900 antialiased">
{{-- menu = status menu samping di layar kecil (HP / tablet). Di layar lebar menu selalu tampil. --}}
<div class="flex h-full" data-frontend-shell data-menu-state="menu" x-data="{ menu: false }" x-on:keydown.escape.window="menu = false">

    {{-- Latar gelap di belakang menu (layar kecil). Klik = tutup menu. --}}
    <div x-show="menu" x-cloak x-transition.opacity x-on:click="menu = false"
         class="fixed inset-0 z-30 bg-black/40 lg:hidden" aria-hidden="true"></div>

    {{-- SIDEBAR (berisi logo) di kiri, setinggi layar --}}
    <x-sidebar />
    <a class="skip-link" href="#main-content">Lewati navigasi</a>

    {{-- KANAN: header + isi halaman. Hanya <main> yang bisa di-scroll. --}}
    <div class="flex min-w-0 flex-1 flex-col">
    {{-- HEADER --}}
    <header data-frontend-header class="flex shrink-0 flex-wrap items-center gap-x-4 gap-y-3 bg-white px-4 py-3 lg:gap-x-6 lg:px-6 lg:py-3.5">
        {{-- Tombol menu: hanya tampil di layar kecil. Logo ada di sidebar. --}}
        <button type="button" x-on:click="menu = true" aria-label="Buka menu" aria-controls="menu-utama" :aria-expanded="menu"
                class="-ml-1 flex size-10 cursor-pointer items-center justify-center rounded-lg hover:bg-gray-100 lg:hidden">
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>

        <div class="ml-auto flex items-center gap-4">
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
            {{-- Akun: klik untuk membuka menu kecil (Profil Saya dan Keluar). Tutup dengan klik di luar menu atau tombol Esc. --}}
            <div class="relative" x-data="{ akun: false }" x-on:click.outside="akun = false" x-on:keydown.escape.window="akun = false">
                <button type="button" x-on:click="akun = !akun" aria-haspopup="menu" aria-controls="menu-akun" :aria-expanded="akun"
                        class="-m-1 flex cursor-pointer items-center gap-2.5 rounded-lg p-1 hover:bg-gray-100"
                        :class="akun ? 'bg-gray-100' : ''">
                    {{-- Kalau file public/images/kuwat.jpeg ada, fotonya dipakai. Kalau tidak, tampil inisial. --}}
                    @if (file_exists(public_path('images/kuwat.jpeg')))
                        <img src="{{ asset('images/kuwat.jpeg') }}" alt="" class="size-[34px] rounded-full object-cover">
                    @else
                        <span class="flex size-[34px] items-center justify-center rounded-full bg-red-600 text-[13px] font-semibold text-white">KS</span>
                    @endif
                    <span class="hidden max-w-40 flex-col text-left leading-tight sm:flex">
                        {{-- nanti diganti data user yang login --}}
                        <span class="truncate text-sm font-semibold">{{ $namaDosen }}</span>
                        <span class="text-xs text-gray-600">Dosen</span>
                    </span>
                    <svg class="size-4 shrink-0 text-gray-500 transition-transform" :class="akun ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </button>

                <div id="menu-akun" x-show="akun" x-cloak role="menu" aria-label="Menu akun"
                     x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="-translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                     x-transition:leave="transition duration-100 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="absolute top-full right-0 z-40 mt-3 w-64 rounded-xl border border-gray-100 bg-white p-4 shadow-lg">
                    <div class="flex flex-col gap-0.5 pb-3">
                        {{-- nanti diganti data user yang login --}}
                        <p class="truncate text-[15px] font-bold" title="{{ $namaDosen }}">{{ $namaDosen }}</p>
                        <p class="text-sm text-gray-500">Dosen</p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <a href="{{ route('profil') }}" role="menuitem"
                           class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-2.5 text-[15px] text-gray-800 hover:bg-gray-100">
                            <svg class="size-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20a7 7 0 0 1 14 0"/></svg>
                            Profil Saya
                        </a>
                        {{-- Autentikasi dikelola kelompok 1; tidak menambah alur login/logout di sini. --}}
                        <a href="{{ route('dashboard') }}" role="menuitem" class="block rounded-lg px-2 py-2.5 text-sm text-brand hover:bg-blue-50">Tampilan mahasiswa</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

        <main id="main-content" tabindex="-1" class="frontend-main flex min-w-0 flex-1 flex-col gap-6 overflow-y-auto px-4 pt-6 pb-10 sm:px-6 sm:pt-8 sm:pb-12">
            @yield('content')
        </main>
    </div>

    {{-- Alert / callout global: lihat components/alert.blade.php --}}
    <x-alert />

    {{-- Pratinjau dokumen: dibuka lewat tombol mata / Lihat pada dokumen. Lihat components/preview-dokumen.blade.php --}}
    <x-preview-dokumen />

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
