@php
    $mhsActive = request()->is('mahasiswa*', 'profil');

    $menuMhs = [
        ['label' => 'Mata kuliah',        'url' => url('/mahasiswa/mata-kuliah'), 'match' => 'mahasiswa/mata-kuliah*'],
        ['label' => 'Prestasi mahasiswa', 'url' => url('/mahasiswa/prestasi'),    'match' => 'mahasiswa/prestasi*'],
    ];
    $subAktif = collect($menuMhs)->contains(fn ($i) => request()->is($i['match']));

    $navIcon = 'size-5 shrink-0';
@endphp

<aside class="hidden w-60 shrink-0 flex-col gap-6 bg-[#2277FF] pt-4 lg:flex sticky top-0 h-screen overflow-y-auto text-white">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="flex items-center gap-2.5 px-3">
        <img src="https://api.builder.io/api/v1/image/assets/TEMP/89ec6d5eff0263da1bc98643370f3576302d902a?width=92" alt="Logo" class="size-[46px] shrink-0">
        <div class="text-[13px] font-bold leading-tight text-white">
            TEKNOLOGI<br />REKAYASA KOMPUTER
        </div>
    </a>

    <!-- Navigasi -->
    <nav class="flex flex-col gap-1.5 px-3 pb-6 text-[15px]">
        <!-- Dashboard -->
        <a href="{{ url('/') }}" 
           class="flex h-10 items-center gap-3 rounded-lg px-3 transition-colors {{ request()->is('/') ? 'bg-white font-semibold text-[#2277FF] shadow-sm' : 'font-medium text-white/80 hover:bg-white/10 hover:text-white' }}">
            <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            <span class="flex-1">Dashboard</span>
        </a>

        <!-- Pengajuan -->
        <a href="#" 
           class="flex h-10 items-center gap-3 rounded-lg px-3 transition-colors {{ request()->is('pengajuan*') ? 'bg-white font-semibold text-[#2277FF] shadow-sm' : 'font-medium text-white/80 hover:bg-white/10 hover:text-white' }}">
            <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span class="flex-1">Pengajuan</span>
        </a>

        <!-- Dropdown Mahasiswa -->
        <div x-data="{ openMhs: {{ $mhsActive ? 'true' : 'false' }} }" class="flex flex-col gap-1.5">
            <div class="flex h-10 items-center rounded-lg transition-colors
                        {{ $subAktif ? 'bg-white/15 text-white font-semibold' : ($mhsActive ? 'bg-white font-semibold text-[#2277FF] shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium') }}">
                
                <a href="#" class="flex flex-1 items-center gap-3 rounded-l-lg px-3 py-2">
                    <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="flex-1">Mahasiswa</span>
                </a>
                
                <button type="button" x-on:click="openMhs = !openMhs" aria-label="Buka atau tutup submenu Mahasiswa" class="rounded-r-lg px-3 py-2">
                    <svg class="size-4 transition-transform" :class="openMhs && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            <div x-show="openMhs" x-cloak x-transition class="flex flex-col gap-1.5 pl-2 pt-0.5">
                @foreach ($menuMhs as $item)
                    @php $aktif = request()->is($item['match']); @endphp
                    <a href="{{ $item['url'] }}" 
                       class="flex h-10 items-center rounded-lg px-4 text-sm font-medium transition-colors
                              {{ $aktif ? 'bg-white font-semibold text-[#2277FF] shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Kegiatan -->
        <a href="{{ url('/kegiatan') }}" 
           class="flex h-10 items-center gap-3 rounded-lg px-3 transition-colors {{ request()->is('kegiatan*') ? 'bg-white font-semibold text-[#2277FF] shadow-sm' : 'font-medium text-white/80 hover:bg-white/10 hover:text-white' }}">
            <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.63 8.41m5.96 5.96a14.926 14.926 0 01-5.84 2.58m0 0a14.951 14.951 0 01-6.16-12.12A14.98 14.98 0 0115.59 14.37z"/></svg>
            <span class="flex-1">Kegiatan</span>
        </a>
    </nav>
</aside>