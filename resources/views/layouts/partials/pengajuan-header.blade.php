<header class="sticky top-0 z-40 flex items-center justify-between gap-4 border-b border-[#E5E7EB] bg-white/95 px-6 py-4 backdrop-blur-sm">
    <div class="flex flex-1 items-center gap-2 rounded-lg border border-[#A8A8A8] bg-[#FEFEFE] px-3 py-1.5 shadow-[inset_1px_2px_2px_rgba(0,0,0,0.12)]">
        <svg class="size-5 text-[#A8A8A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input placeholder="Cari" class="min-w-0 flex-1 bg-transparent text-base outline-none placeholder:text-[#A8A8A8]" />
    </div>

    <div x-data="{ openProfile: false }" class="relative">
        <button x-on:click="openProfile = !openProfile" @click.outside="openProfile = false" type="button" class="flex items-center gap-3 rounded-lg p-1 transition hover:bg-[#F3F4F6]">
            <img src="{{ asset('images/avatar.png') }}" alt="Avatar" class="size-10 shrink-0 rounded-full" />
            <div class="hidden flex-1 flex-col text-left sm:flex">
                <span class="text-sm font-bold leading-tight text-[#111827]">Arya Bima</span>
                <span class="text-xs text-[#6B7280]">Mahasiswa</span>
            </div>
            <svg class="size-4 text-[#6B7280] transition-transform duration-200" x-bind:class="openProfile ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="openProfile" x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 top-full z-50 mt-2 w-64 rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.08)]">
            <div class="flex flex-col gap-0.5 border-b border-[#E5E7EB] pb-4">
                <span class="text-lg font-bold text-[#111827]">Arya Bima</span>
                <span class="text-sm text-[#6B7280]">Mahasiswa</span>
            </div>

            <div class="flex flex-col gap-2 pt-4">
                <a href="{{ route('pengajuan-segera', ['menu' => 'Profil Saya']) }}" class="flex items-center gap-3 rounded-xl px-2 py-1.5 text-base font-medium text-[#374151] transition hover:bg-[#F3F4F6]">
                    <svg class="size-5 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil Saya</span>
                </a>

                <a href="{{ route('pengajuan-segera', ['menu' => 'Keluar']) }}" class="flex items-center gap-3 rounded-xl px-2 py-1.5 text-base font-semibold text-[#E5484D] transition hover:bg-[#FEF2F2]">
                    <svg class="size-5 text-[#E5484D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </div>
</header>