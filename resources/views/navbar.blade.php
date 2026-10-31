@php
    // Logika pengecekan status aktif (Mencakup Route Name, URL Path, dan Slash Utama)
    $isDashboard = request()->routeIs('dashboard') || request()->is('dashboard*') || request()->path() === 'dashboard' || request()->is('/');
    $isPengajuan = request()->is('pengajuan*');
    $isKegiatan  = request()->is('kegiatan*') || request()->routeIs('kegiatan*');
    
    $mhsActive = request()->is('mahasiswa*', 'profil*');

    $menuMhs = [
        ['label' => 'Mata kuliah',        'url' => url('/mahasiswa/mata-kuliah'), 'match' => 'mahasiswa/mata-kuliah*'],
        ['label' => 'Prestasi mahasiswa', 'url' => url('/mahasiswa/prestasi'),    'match' => 'mahasiswa/prestasi*'],
    ];
    $subAktif = collect($menuMhs)->contains(fn ($i) => request()->is($i['match']));

    $navIcon = 'size-5 shrink-0';
@endphp

{{-- ================= SIDEBAR / NAVBAR (ASIDE) ================= --}}
<aside class="hidden w-60 shrink-0 flex-col gap-6 overflow-y-auto bg-gradient-to-b from-[#1f66e5] via-[#1550bd] to-[#0b2f7a] px-3 py-5 text-white lg:flex sticky top-0 h-screen select-none">

    <!-- Logo & Brand Header -->
    <a href="{{ url('/dashboard') }}" class="flex items-center gap-2.5 px-2 hover:opacity-90 transition-opacity">
        <img src="{{ Vite::asset('resources/images/logo-trk.png') }}" alt="Logo TRK" class="h-9 w-auto shrink-0" onerror="this.onerror=null; this.src='https://api.builder.io/api/v1/image/assets/TEMP/89ec6d5eff0263da1bc98643370f3576302d902a?width=92';">
        <span class="text-[13px] font-bold leading-[1.3] text-white">TEKNOLOGI<br>REKAYASA KOMPUTER</span>
    </a>

    <!-- Menu Navigasi Utama -->
    <nav class="flex flex-col gap-1.5 text-[15px] text-white">
        
        <!-- Dashboard -->
        <a href="{{ url('/dashboard') }}" 
           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-200 {{ $isDashboard ? 'bg-white font-bold text-[#3565ed] shadow-xs' : 'text-white/90 hover:bg-white/15 hover:text-white font-medium' }}">
            <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h6v6h-6z"/><path d="M14 4h6v6h-6z"/><path d="M4 14h6v6h-6z"/><path d="M14 14h6v6h-6z"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Pengajuan -->
        <a href="{{ url('/pengajuan') }}" 
           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-200 {{ $isPengajuan ? 'bg-white font-bold text-[#3565ed] shadow-xs' : 'text-white/90 hover:bg-white/15 hover:text-white font-medium' }}">
            <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/>
            </svg>
            <span>Pengajuan</span>
        </a>

        {{-- ===== Grup Submenu Mahasiswa ===== --}}
        <div x-data="{ openMhs: {{ $mhsActive ? 'true' : 'false' }} }" class="flex flex-col gap-1.5">
            <div class="flex items-center rounded-xl transition-all duration-200
                        {{ $subAktif ? 'bg-white/15 text-white font-medium' : ($mhsActive ? 'bg-white font-bold text-[#3565ed] shadow-xs' : 'text-white/90 hover:bg-white/15 hover:text-white font-medium') }}">
                
                <a href="{{ url('/profil') }}" class="flex flex-1 items-center gap-3 rounded-l-xl px-3.5 py-2.5">
                    <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M8 21v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1"/><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M17 10h2a2 2 0 0 1 2 2v1"/><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M3 13v-1a2 2 0 0 1 2-2h2"/>
                    </svg>
                    <span class="flex-1">Mahasiswa</span>
                </a>

                <button type="button" x-on:click="openMhs = !openMhs" :aria-expanded="openMhs"
                        aria-label="Buka atau tutup submenu Mahasiswa" class="rounded-r-xl px-3 py-2.5 hover:opacity-80">
                    <svg class="size-4 transition-transform duration-200" :class="openMhs && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 9l6 6l6-6"/>
                    </svg>
                </button>
            </div>

            <div x-show="openMhs" x-cloak x-transition class="flex flex-col gap-1.5">
                @foreach ($menuMhs as $item)
                    @php $aktif = request()->is($item['match']); @endphp
                    <a href="{{ $item['url'] }}" @if ($aktif) aria-current="page" @endif
                       class="rounded-xl py-2 pl-11 pr-3 text-sm transition-all duration-200
                              {{ $aktif ? 'bg-white font-bold text-[#3565ed] shadow-xs' : 'text-white/80 hover:bg-white/15 hover:text-white font-medium' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Kegiatan -->
        <a href="{{ url('/kegiatan') }}" 
           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-200 {{ $isKegiatan ? 'bg-white font-bold text-[#3565ed] shadow-xs' : 'text-white/90 hover:bg-white/15 hover:text-white font-medium' }}">
            <svg class="{{ $navIcon }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3-5a9 9 0 0 0 6-8a3 3 0 0 0-3-3a9 9 0 0 0-8 6a6 6 0 0 0-5 3"/><path d="M7 14a6 6 0 0 0-3 6a6 6 0 0 0 6-3"/><path d="M15 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0-2 0"/>
            </svg>
            <span>Kegiatan</span>
        </a>

    </nav>
</aside>