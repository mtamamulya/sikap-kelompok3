{{--
    Satu kolom isian yang hanya untuk dibaca (halaman Detail).
    Pemakaian: <x-detail-field label="Judul penelitian" :value="$p['judul']" />
    icon      (opsional): 'chevron' atau 'kalender', supaya tampilannya sama dengan form isian.
    multiline (opsional): true untuk teks panjang (Keterangan), kotaknya lebih tinggi dan teks boleh turun baris.
    locked    (opsional): true untuk kolom yang terisi otomatis (garis putus-putus, teks abu-abu), mis. Semester.
    href      (opsional): kalau diisi, nilainya tampil sebagai tautan yang bisa diklik (Tautan dokumen).
    Kalau value kosong, tampil tanda "-" berwarna abu-abu.
--}}
@props(['label', 'value' => null, 'icon' => null, 'multiline' => false, 'href' => null, 'locked' => false])

<div class="flex min-w-0 flex-col gap-2">
    <dt class="text-[15px] font-medium">{{ $label }}</dt>
    <dd @class([
        'relative m-0 flex rounded-lg border bg-gray-50 pr-10 pl-3.5 text-[15px]',
        'border-gray-200 text-gray-900' => ! $locked,
        'border-dashed border-gray-300 text-gray-500' => $locked,
        'min-h-20 items-start py-2.5' => $multiline,
        'h-11 items-center' => ! $multiline,
    ])>
        @if (filled($value))
            @if ($href)
                <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="truncate text-brand hover:underline">{{ $value }}</a>
            @else
                <span @class(['whitespace-pre-line' => $multiline, 'truncate' => ! $multiline])>{{ $value }}</span>
            @endif
        @else
            <span class="text-gray-400">-</span>
        @endif

        @if ($icon === 'chevron')
            <svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        @elseif ($icon === 'kalender')
            <svg class="pointer-events-none absolute top-1/2 right-3.5 size-5 -translate-y-1/2 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15.5" rx="3"/><path d="M8 3v4M16 3v4M3.5 10h17M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01M16 17h.01"/></svg>
        @endif
    </dd>
</div>