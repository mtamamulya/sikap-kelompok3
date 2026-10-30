@php
    $menuMahasiswa = [
        'Mata kuliah' => ['route' => 'mahasiswa.mata-kuliah', 'match' => 'mahasiswa.mata-kuliah*'],
        'Prestasi' => ['route' => 'mahasiswa.prestasi', 'match' => 'mahasiswa.prestasi*'],
    ];
    $dashboardActive = request()->routeIs('mahasiswa.dashboard');
    $mhsActive = request()->routeIs('mahasiswa.index', 'mahasiswa.mata-kuliah*', 'mahasiswa.prestasi*', 'mahasiswa.kegiatan', 'mahasiswa.profil');
    $mhsOpen = $mhsActive;
    $item = 'flex h-10 items-center gap-2 rounded-lg px-3 text-base tracking-[-0.18px] transition';
    $on = 'bg-[#2277FF] text-white';
    $off = 'text-[#111827] hover:bg-[#F0F0F0]';
    $icon = 'ti text-2xl';
@endphp
<aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col gap-6 overflow-y-auto border-r border-[#E5E7EB] bg-white pt-4 lg:flex">
    <div class="flex items-center gap-2.5 px-1">
        <img src="{{ asset('images/logo_trk.png') }}" alt="" class="h-10 w-auto shrink-0" />
        <div class="text-[13px] font-bold leading-[1.15] tracking-tight text-[#003385]">
            TEKNOLOGI<br />REKAYASA KOMPUTER
        </div>
    </div>

    <nav class="flex flex-col gap-3 px-3 pb-6">
        <a href="{{ route('mahasiswa.dashboard') }}" @if ($dashboardActive) aria-current="page" @endif class="{{ $item }} {{ $dashboardActive ? $on : $off }}">
            <i class="{{ $icon }} ti-category {{ $dashboardActive ? 'text-white' : 'text-[#2B3F6C]' }}"></i>
            <span class="flex-1">Dashboard</span>
        </a>

        <a href="{{ route('pengajuan.index') }}" class="{{ $item }} {{ request()->routeIs('pengajuan.*') ? $on : $off }}">
            <i class="{{ $icon }} ti-book {{ request()->routeIs('pengajuan.*') ? 'text-white' : 'text-[#2B3F6C]' }}"></i>
            <span class="flex-1">Pengajuan</span>
        </a>

        <div x-data="{ open: @js($mhsOpen) }" class="flex flex-col gap-2">
            <div class="flex items-center gap-1 rounded-lg {{ $mhsActive ? $on : '' }}">
                <a href="{{ route('mahasiswa.index') }}" @if ($mhsActive) aria-current="page" @endif
                   class="flex h-10 flex-1 items-center gap-2 rounded-l-lg px-3 text-left text-base tracking-[-0.18px] transition {{ $mhsActive ? 'text-white' : 'text-[#111827] hover:bg-[#F0F0F0]' }}">
                    <i class="{{ $icon }} ti-users {{ $mhsActive ? 'text-white' : 'text-[#2B3F6C]' }}"></i>
                    <span class="flex-1">Mahasiswa</span>
                </a>
                <button type="button" x-on:click="open = !open" :aria-expanded="open" aria-label="Buka atau tutup submenu Mahasiswa"
                        class="flex h-10 items-center rounded-r-lg px-3 text-[#111827] hover:bg-[#F0F0F0]">
                    <i class="ti ti-chevron-down text-base text-[#6B7280] transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''"></i>
                </button>
            </div>

            <div x-show="open" x-cloak class="flex flex-col gap-2">
                @foreach ($menuMahasiswa as $label => $item)
                    @php $active = request()->routeIs($item['match']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex h-10 items-center rounded-xl pl-11 pr-3 text-sm transition {{ $active ? 'bg-[#2277FF] font-medium text-white' : 'bg-[#F0F0F0] text-[#111827] hover:bg-[#E5E7EB]' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <a href="{{ route('mahasiswa.kegiatan') }}" @if (request()->routeIs('mahasiswa.kegiatan')) aria-current="page" @endif class="{{ $item }} {{ request()->routeIs('mahasiswa.kegiatan') ? $on : $off }}">
            <i class="{{ $icon }} ti-rocket {{ request()->routeIs('mahasiswa.kegiatan') ? 'text-white' : 'text-[#2B3F6C]' }}"></i>
            <span class="flex-1">Kegiatan</span>
        </a>
    </nav>
</aside>