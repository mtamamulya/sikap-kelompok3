<header class="sticky top-0 z-40 shrink-0 bg-white/95 backdrop-blur-sm">
    <div class="flex h-14 items-center px-3 sm:px-5 lg:h-[72px]">
        <a href="{{ route('pengajuan.index') }}" class="flex min-w-0 items-center gap-2 lg:hidden">
            <img src="{{ asset('images/logo-trk.png') }}" alt="Logo TRK" class="size-8 shrink-0" />
            <span class="text-[10px] font-bold leading-tight text-[#1f66e5]">TEKNOLOGI<br>REKAYASA KOMPUTER</span>
        </a>

        <div x-data="{ openProfile: false }" class="relative ml-auto">
            <button x-on:click="openProfile = !openProfile" @click.outside="openProfile = false" type="button" class="flex items-center gap-3 rounded-lg p-1 transition hover:bg-[#F3F4F6]">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[#DBEAFE] text-base font-semibold text-[#1f66e5]" aria-hidden="true">A</span>
                <div class="flex flex-col text-left">
                    <span class="text-xs font-bold leading-tight text-[#111827] sm:text-sm">Arya Bima</span>
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
                    <a href="{{ route('mahasiswa.profil') }}" class="flex items-center gap-3 rounded-xl px-2 py-1.5 text-base font-medium text-[#374151] transition hover:bg-[#F3F4F6]">
                        <svg class="size-5 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"></path>
                        </svg>
                        <span>Profil Saya</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-2 py-1.5 text-left text-base font-semibold text-[#E5484D] transition hover:bg-[#FEF2F2]">
                            <svg class="size-5 text-[#E5484D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"></path>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ open: false }" class="bg-linear-to-r from-[#1f66e5] via-[#1550bd] to-[#0b2f7a] text-white lg:hidden">
        <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="mobile-pengajuan-nav"
                class="flex h-11 w-full items-center gap-2 px-4 text-sm font-semibold">
            <i class="ti ti-menu-2 text-lg"></i>
            <span>Menu navigasi</span>
            <i class="ti ti-chevron-down ml-auto transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
        </button>
        <nav id="mobile-pengajuan-nav" x-show="open" x-cloak x-transition class="flex flex-col gap-1 px-3 pb-3">
            <a @click="open = false" href="{{ route('mahasiswa.dashboard') }}"
               class="flex h-10 items-center gap-3 rounded-lg px-3 text-sm transition {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-white font-semibold text-[#1f66e5]' : 'text-white hover:bg-white/10' }}">
                <i class="ti ti-category text-lg"></i>Dashboard
            </a>
            <a @click="open = false" href="{{ route('pengajuan.index') }}"
               class="flex h-10 items-center gap-3 rounded-lg px-3 text-sm transition {{ request()->routeIs('pengajuan.*') ? 'bg-white font-semibold text-[#1f66e5]' : 'text-white hover:bg-white/10' }}">
                <i class="ti ti-book text-lg"></i>Pengajuan
            </a>
            <div x-data="{ openMhs: @js(request()->routeIs('mahasiswa.index', 'mahasiswa.mata-kuliah*', 'mahasiswa.prestasi*', 'mahasiswa.kegiatan', 'mahasiswa.profil')) }" class="flex flex-col gap-1">
                <div class="flex items-center rounded-lg bg-white/10">
                    <a @click="open = false" href="{{ route('mahasiswa.index') }}"
                       class="flex h-10 flex-1 items-center gap-3 rounded-l-lg px-3 text-sm font-semibold text-white hover:bg-white/10">
                        <i class="ti ti-users text-lg"></i>Mahasiswa
                    </a>
                    <button type="button" x-on:click="openMhs = !openMhs" :aria-expanded="openMhs"
                            aria-label="Buka atau tutup submenu Mahasiswa"
                            class="flex h-10 items-center rounded-r-lg px-3 text-white hover:bg-white/10">
                        <i class="ti ti-chevron-down text-base transition-transform duration-200" :class="openMhs ? 'rotate-180' : ''"></i>
                    </button>
                </div>
                <div x-show="openMhs" x-cloak class="flex flex-col gap-1">
                    @foreach (['Mata kuliah' => 'mahasiswa.mata-kuliah', 'Prestasi' => 'mahasiswa.prestasi'] as $label => $routeName)
                        <a @click="open = false" href="{{ route($routeName) }}"
                           class="flex h-9 items-center rounded-lg pl-10 pr-3 text-sm transition {{ request()->routeIs($routeName.'*') ? 'bg-white font-semibold text-[#1f66e5]' : 'text-white hover:bg-white/10' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
            <a @click="open = false" href="{{ route('mahasiswa.kegiatan') }}"
               class="flex h-10 items-center gap-3 rounded-lg px-3 text-sm transition {{ request()->routeIs('mahasiswa.kegiatan') ? 'bg-white font-semibold text-[#1f66e5]' : 'text-white hover:bg-white/10' }}">
                <i class="ti ti-rocket text-lg"></i>Kegiatan
            </a>
        </nav>
    </div>
</header>
