<header class="sticky top-0 z-20 flex h-[60px] shrink-0 items-center justify-between bg-white px-4 sm:px-6">
    <div class="flex items-center gap-3 lg:hidden">
        <button type="button" x-on:click="menu = true" aria-label="Buka menu"
                class="flex size-9 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <a href="{{ route('dosen.dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-trk.png') }}" alt="" class="h-8 w-auto">
            <span class="text-[10px] font-bold leading-tight text-brand">TEKNOLOGI<br>REKAYASA KOMPUTER</span>
        </a>
    </div>

    <div class="ml-auto flex items-center gap-4">
        <a href="{{ route('notifikasi') }}" aria-label="Notifikasi"
           class="relative flex size-9 items-center justify-center rounded-full text-gray-700 hover:bg-gray-100">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
            </svg>
            <span class="absolute top-1 right-1 size-2.5 rounded-full bg-red-600" aria-hidden="true"></span>
        </a>

        <div x-data="{ open: false }" x-on:click.outside="open = false" class="relative">
            <button type="button" x-on:click="open = !open" :aria-expanded="open"
                    class="flex items-center gap-2.5 rounded-lg p-1.5 text-left hover:bg-gray-50">
                <span class="flex size-9 items-center justify-center rounded-full bg-red-600 text-xs font-bold text-white">KS</span>
                <span class="hidden sm:flex sm:flex-col">
                    <span class="text-xs font-bold leading-tight text-gray-900">Kuwat Santoso</span>
                    <span class="text-[11px] leading-tight text-gray-500">Dosen</span>
                </span>
                <svg class="size-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            <div x-show="open" x-cloak x-transition
                 class="absolute right-0 top-full z-50 mt-2 w-52 rounded-xl border border-gray-200 bg-white p-2 shadow-lg">
                <a href="{{ route('profil') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>
