{{--
    Judul kolom yang bisa diklik untuk mengurutkan (naik / turun). Dipakai di tabel daftar.
    Pemakaian: <x-th-urut kolom="tahun" label="Tahun Pelaksanaan" class="w-1/6" />
    kolom = nama kolom yang diurutkan (harus ada di data atau di "kunci" pada tabelData).
    Harus berada di dalam komponen Alpine yang memakai tabelData (lihat components/tabel-js).
--}}
@props(['kolom', 'label'])

<th scope="col" :aria-sort="ariaUrut('{{ $kolom }}')" {{ $attributes->merge(['class' => 'border-b border-gray-300 p-0 font-medium']) }}>
    <button type="button" x-on:click="urutPer('{{ $kolom }}')"
            class="flex w-full cursor-pointer items-center gap-1.5 px-2.5 py-3 text-left font-medium hover:bg-gray-200/70">
        <span>{{ $label }}</span>
        <span class="flex shrink-0 flex-col" aria-hidden="true">
            <svg class="-mb-0.5 size-3" viewBox="0 0 24 24" fill="currentColor" :class="urutKolom === '{{ $kolom }}' && urutArah === 'asc' ? 'text-brand' : 'text-gray-400'"><path d="M12 8l6 7H6z"/></svg>
            <svg class="size-3" viewBox="0 0 24 24" fill="currentColor" :class="urutKolom === '{{ $kolom }}' && urutArah === 'desc' ? 'text-brand' : 'text-gray-400'"><path d="M12 16l-6-7h12z"/></svg>
        </span>
        <span class="sr-only" x-text="urutKolom === '{{ $kolom }}' ? (urutArah === 'asc' ? '(urut naik)' : '(urut turun)') : '(klik untuk mengurutkan)'"></span>
    </button>
</th>