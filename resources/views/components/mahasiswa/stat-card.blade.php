@props(['label', 'icon' => null])

{{--
    Kartu statistik. Ukuran kartu diatur di sini, jadi semua halaman yang memakainya ikut berubah.
    Pemakaian:
        <x-mahasiswa.stat-card label="Judul kartu">
            <x-slot:icon> <svg>...</svg> </x-slot:icon>
            ...isi kartu...
        </x-mahasiswa.stat-card>
--}}
<div class="flex min-w-0 flex-col gap-2.5 rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
    <div class="flex items-center justify-between gap-2">
        <span class="text-sm text-[#6B7280]">{{ $label }}</span>
        @if ($icon)
            <span class="shrink-0 text-[#27F] [&>svg]:size-5">{{ $icon }}</span>
        @endif
    </div>

    {{ $slot }}
</div>