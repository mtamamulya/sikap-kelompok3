@php
    $menu = request('menu');
    $onSegera = request()->routeIs('pengajuan-segera');
    $mhsOpen = $onSegera && in_array($menu, ['Mata kuliah', 'Prestasi mahasiswa']);
    $item = 'flex h-10 items-center gap-2 rounded-lg px-3 text-base tracking-[-0.18px] transition';
    $on = 'bg-[#2277FF] text-white';
    $off = 'text-[#111827] hover:bg-[#F0F0F0]';
    $icon = 'ti text-2xl';
@endphp
<aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col gap-6 overflow-y-auto border-r border-[#E5E7EB] bg-white pt-4 lg:flex">
    <div class="flex items-center gap-1.5 px-3">
        <img src="{{ asset('images/logo-trk.png') }}" alt="Logo TRK" class="size-[46px]" />
        <div class="text-[13px] font-bold leading-tight text-[#003385]">
            TEKNOLOGI<br />REKAYASA KOMPUTER
        </div>
    </div>

    <nav class="flex flex-col gap-3 px-3 pb-6">
        <a href="{{ route('pengajuan-segera', ['menu' => 'Dashboard']) }}" class="{{ $item }} {{ $onSegera && $menu === 'Dashboard' ? $on : $off }}">
            <i class="{{ $icon }} ti-category {{ $onSegera && $menu === 'Dashboard' ? 'text-white' : 'text-[#2B3F6C]' }}"></i>
            <span class="flex-1">Dashboard</span>
        </a>

        <a href="{{ route('pengajuan.index') }}" class="{{ $item }} {{ request()->routeIs('pengajuan.*') ? $on : $off }}">
            <i class="{{ $icon }} ti-book {{ request()->routeIs('pengajuan.*') ? 'text-white' : 'text-[#2B3F6C]' }}"></i>
            <span class="flex-1">Pengajuan</span>
        </a>

        <div x-data="{ open: @js($mhsOpen) }" class="flex flex-col gap-2">
            <button type="button" x-on:click="open = !open"
                    x-bind:class="open ? 'bg-[#F0F0F0]' : 'hover:bg-[#F0F0F0]'"
                    class="flex h-10 w-full items-center gap-2 rounded-lg px-3 text-left text-base tracking-[-0.18px] text-[#111827] transition">
                <i class="{{ $icon }} ti-users text-[#2B3F6C]"></i>
                <span class="flex-1">Mahasiswa</span>
                <i class="ti ti-chevron-down text-base text-[#6B7280] transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-cloak class="flex flex-col gap-2">
                @foreach (['Mata kuliah', 'Prestasi mahasiswa'] as $s)
                    @php $active = $onSegera && $menu === $s; @endphp
                    <a href="{{ route('pengajuan-segera', ['menu' => $s]) }}"
                       class="flex h-10 items-center rounded-xl pl-11 pr-3 text-sm transition {{ $active ? 'bg-[#2277FF] font-medium text-white' : 'bg-[#F0F0F0] text-[#111827] hover:bg-[#E5E7EB]' }}">
                        {{ $s }}
                    </a>
                @endforeach
            </div>
        </div>

        <a href="{{ route('pengajuan-segera', ['menu' => 'Kegiatan']) }}" class="{{ $item }} {{ $onSegera && $menu === 'Kegiatan' ? $on : $off }}">
            <i class="{{ $icon }} ti-rocket {{ $onSegera && $menu === 'Kegiatan' ? 'text-white' : 'text-[#2B3F6C]' }}"></i>
            <span class="flex-1">Kegiatan</span>
        </a>
    </nav>
</aside>