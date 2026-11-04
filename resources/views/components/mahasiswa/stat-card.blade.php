@props([
    'label',
    'icon' => null,
    'accent' => '#2277FF',
    'iconBg' => '#EFF6FF',
    'iconColor' => '#2563EB',
])

{{--
    Kartu statistik. Ukuran kartu diatur di sini, jadi semua halaman yang memakainya ikut berubah.
    Pemakaian:
        <x-mahasiswa.stat-card label="Judul kartu">
            <x-slot:icon> <svg>...</svg> </x-slot:icon>
            ...isi kartu...
        </x-mahasiswa.stat-card>
--}}
<div class="group relative flex min-w-0 flex-col gap-2.5 overflow-hidden rounded-xl border border-[#E5E7EB] bg-white p-4 pl-5 shadow-[0_2px_8px_rgba(0,0,0,0.02)] transition-all duration-300 hover:-translate-y-1 hover:border-[#D1D5DB] hover:shadow-md">
    <span class="absolute inset-y-0 left-0 w-1.5 transition-all duration-300 group-hover:w-2" style="background-color: {{ $accent }}"></span>
    <div class="flex items-center justify-between gap-2">
        <span class="text-sm text-[#6B7280]">{{ $label }}</span>
        @if ($icon)
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-110 [&>svg]:size-5"
                  style="background-color: {{ $iconBg }}; color: {{ $iconColor }}">{{ $icon }}</span>
        @endif
    </div>

    {{ $slot }}
</div>