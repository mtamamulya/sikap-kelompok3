@php
    $menuMahasiswa = [
        'Mata kuliah' => ['route' => 'mahasiswa.mata-kuliah', 'match' => 'mahasiswa.mata-kuliah*'],
        'Prestasi' => ['route' => 'mahasiswa.prestasi', 'match' => 'mahasiswa.prestasi*'],
    ];
    $dashboardActive = request()->routeIs('mahasiswa.dashboard');
    $mhsActive = request()->routeIs('mahasiswa.index', 'mahasiswa.mata-kuliah*', 'mahasiswa.prestasi*');
    $mhsLandingActive = request()->routeIs('mahasiswa.index');
    $mhsOpen = $mhsActive;
    $pengajuanOpen = request()->routeIs('dashboard', 'pengajuan.*');
    $pengajuanActive = request()->routeIs('pengajuan.*');
    $kegiatanActive = request()->routeIs('pengajuan.index', 'pengajuan.create', 'pengajuan.show', 'pengajuan.edit', 'pengajuan.store', 'pengajuan.update');
    $beritaActive = request()->routeIs('pengajuan.berita.*');
    $navItem = 'flex min-h-12 items-center gap-4 rounded-lg px-4 py-3 text-lg transition-colors hover:bg-white/10';
    $activeItem = 'bg-white text-[#1f66e5] font-semibold shadow-sm hover:bg-white';
    $icon = 'size-5 shrink-0';
@endphp
<aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col gap-7 overflow-y-auto bg-linear-to-b from-[#1f66e5] from-0% via-[#1550bd] via-50% to-[#0b2f7a] to-100% px-3 py-6 text-white lg:flex">
    <a href="{{ route('pengajuan.index') }}" aria-label="Teknologi Rekayasa Komputer, ke Pengajuan" class="flex items-center gap-2.5 px-1">
        <img src="{{ asset('images/logo-trk.png') }}" alt="" class="h-10 w-auto shrink-0" />
        <span class="text-[13px] font-bold leading-[1.15] tracking-tight text-white">TEKNOLOGI<br>REKAYASA KOMPUTER</span>
    </a>

    <nav class="flex flex-col gap-2.5 text-lg text-white">
        <a href="{{ route('mahasiswa.dashboard') }}" @if ($dashboardActive) aria-current="page" @endif
           class="{{ $navItem }} {{ $dashboardActive ? $activeItem : '' }}">
            <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h6v6h-6z"/><path d="M14 4h6v6h-6z"/><path d="M4 14h6v6h-6z"/><path d="M14 14h6v6h-6z"/>
            </svg>
            <span class="flex-1">Dashboard</span>
        </a>

        <div x-data="{ open: @js($pengajuanOpen) }" class="flex flex-col gap-2">
            <div class="flex items-center rounded-lg transition-colors {{ $pengajuanActive ? 'bg-white/15 text-white' : '' }}">
                <button type="button" x-on:click="open = !open" :aria-expanded="open"
                        class="flex min-h-12 flex-1 items-center gap-4 rounded-l-lg px-4 py-3 text-left text-lg tracking-[-0.18px] text-white transition-colors hover:bg-white/10">
                    <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/>
                    </svg>
                    <span class="flex-1">Pengajuan</span>
                </button>
                <button type="button" x-on:click="open = !open" :aria-expanded="open" aria-label="Buka atau tutup submenu Pengajuan"
                        class="flex min-h-12 items-center rounded-r-lg px-4 py-3 text-white hover:bg-white/10">
                    <svg class="size-4 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 9l6 6l6-6"/>
                    </svg>
                </button>
            </div>
            <div x-show="open" x-cloak class="flex flex-col gap-2">
                <a href="{{ route('pengajuan.index') }}" @if ($kegiatanActive) aria-current="page" @endif
                   class="flex items-center gap-3 rounded-lg py-3 pl-10 pr-4 text-base transition-colors {{ $kegiatanActive ? 'bg-white text-[#1f66e5] font-semibold shadow-sm' : 'bg-white/10 text-white hover:bg-white/20' }}">
                    <i class="ti ti-calendar-event size-5 shrink-0" aria-hidden="true"></i>
                    Kegiatan Mahasiswa
                </a>
                <a href="{{ route('pengajuan.berita.index') }}" @if ($beritaActive) aria-current="page" @endif
                   class="flex items-center gap-3 rounded-lg py-3 pl-10 pr-4 text-base transition-colors {{ $beritaActive ? 'bg-white text-[#1f66e5] font-semibold shadow-sm' : 'bg-white/10 text-white hover:bg-white/20' }}">
                    <i class="ti ti-news size-5 shrink-0" aria-hidden="true"></i>
                    Berita
                </a>
            </div>
        </div>

        <div x-data="{ open: @js($mhsOpen) }" class="flex flex-col gap-2">
            <div class="flex items-center rounded-lg {{ $mhsLandingActive ? $activeItem : '' }}">
                <a href="{{ route('mahasiswa.index') }}" @if (request()->routeIs('mahasiswa.index')) aria-current="page" @endif
                   class="flex min-h-12 flex-1 items-center gap-4 rounded-l-lg px-4 py-3 text-lg transition-colors {{ $mhsLandingActive ? 'text-[#1f66e5]' : 'text-white hover:bg-white/10' }}">
                    <svg class="{{ $icon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M8 21v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1"/><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M17 10h2a2 2 0 0 1 2 2v1"/><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M3 13v-1a2 2 0 0 1 2-2h2"/>
                    </svg>
                    <span class="flex-1">Mahasiswa</span>
                </a>
                <button type="button" x-on:click="open = !open" :aria-expanded="open" aria-controls="desktop-mahasiswa-submenu" aria-label="Buka atau tutup submenu Mahasiswa"
                        class="flex min-h-12 items-center rounded-r-lg px-4 py-3 {{ $mhsLandingActive ? 'text-[#1f66e5]' : 'text-white hover:bg-white/10' }}">
                    <svg class="size-4 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 9l6 6l6-6"/>
                    </svg>
                </button>
            </div>

            <div id="desktop-mahasiswa-submenu" x-show="open" x-cloak x-transition class="flex flex-col gap-2">
                @foreach ($menuMahasiswa as $label => $item)
                    @php $active = request()->routeIs($item['match']); @endphp
                    <a href="{{ route($item['route']) }}"
                       @if ($active) aria-current="page" @endif
                       class="flex items-center gap-3 rounded-lg py-3 pl-10 pr-4 text-base transition-colors {{ $active ? 'bg-white text-[#1f66e5] font-semibold shadow-sm' : 'bg-white/10 text-white hover:bg-white/20' }}">
                        <i class="ti {{ $label === 'Mata kuliah' ? 'ti-book-2' : 'ti-trophy' }} size-5 shrink-0" aria-hidden="true"></i>
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <a href="{{ route('kegiatan.index') }}" @if (request()->routeIs('kegiatan.*')) aria-current="page" @endif
           class="{{ $navItem }} {{ request()->routeIs('kegiatan.*') ? $activeItem : '' }}">
            <i class="ti ti-rocket {{ $icon }}" aria-hidden="true"></i>
            <span class="flex-1">Kegiatan</span>
        </a>

    </nav>
</aside>