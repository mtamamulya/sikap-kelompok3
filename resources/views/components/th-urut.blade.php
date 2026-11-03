{{--
    Judul kolom tabel daftar: klik judul untuk mengurutkan, klik ikon garis tiga (≡) untuk menu kolom
    (filter nilai, urutkan naik, urutkan turun, hapus urutan), seperti DataTables.
    Pemakaian: <x-th-urut kolom="tahun" label="Tahun Pelaksanaan" class="w-1/6" />
               <x-th-urut kolom="judul" label="Judul" :filter="false" />   (tanpa daftar filter nilai)
    kolom = nama kolom pada data (atau pada "kunci" di tabelData).
    Harus berada di dalam komponen Alpine yang memakai tabelData (lihat components/tabel-js).
--}}
@props(['kolom', 'label', 'filter' => true])

<th scope="col" :aria-sort="ariaUrut('{{ $kolom }}')" {{ $attributes->merge(['class' => 'border-b border-gray-300 p-0 font-medium']) }}>
    <div class="flex items-center">
        {{-- Klik judul: urut naik / turun --}}
        <button type="button" x-on:click="urutPer('{{ $kolom }}')"
                class="flex min-w-0 flex-1 cursor-pointer items-center gap-1.5 py-3 pr-1 pl-2.5 text-left font-medium hover:bg-gray-200/70">
            <span class="truncate">{{ $label }}</span>
            <span class="flex shrink-0 flex-col" aria-hidden="true">
                <svg class="-mb-0.5 size-3" viewBox="0 0 24 24" fill="currentColor" :class="urutKolom === '{{ $kolom }}' && urutArah === 'asc' ? 'text-brand' : 'text-gray-400'"><path d="M12 8l6 7H6z"/></svg>
                <svg class="size-3" viewBox="0 0 24 24" fill="currentColor" :class="urutKolom === '{{ $kolom }}' && urutArah === 'desc' ? 'text-brand' : 'text-gray-400'"><path d="M12 16l-6-7h12z"/></svg>
            </span>
            <span class="sr-only" x-text="urutKolom === '{{ $kolom }}' ? (urutArah === 'asc' ? '(urut naik)' : '(urut turun)') : '(klik untuk mengurutkan)'"></span>
        </button>

        {{-- Ikon menu kolom. Berwarna biru kalau kolom ini sedang difilter. --}}
        <button type="button" x-on:click.stop="bukaMenu('{{ $kolom }}', $el)"
                aria-haspopup="menu" :aria-expanded="menuBuka === '{{ $kolom }}'" aria-label="Menu kolom {{ $label }}"
                :class="adaFilter('{{ $kolom }}') ? 'text-brand' : 'text-gray-500'"
                class="mr-1 flex size-8 shrink-0 cursor-pointer items-center justify-center rounded hover:bg-gray-200/70">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </div>

    {{-- Menu kolom. Posisinya fixed supaya tidak terpotong oleh tabel yang bisa digulir. --}}
    {{-- x-if (bukan x-show): panel hanya ada di halaman saat kolom ini dibuka, jadi tidak mungkin dua menu tampil bersamaan. --}}
    <template x-if="menuBuka === '{{ $kolom }}'">
    <div role="menu"
         x-on:click.outside="menuBuka = ''"
         x-on:keydown.escape.window="menuBuka = ''"
         x-on:resize.window="menuBuka = ''"
         x-on:scroll.window.capture="$el.contains($event.target) || (menuBuka = '')"
         :style="`left:${menuX}px;top:${menuY}px;max-height:calc(100vh - ${menuY}px - 8px)`"
         class="fixed z-50 w-64 overflow-y-auto rounded-lg border border-gray-200 bg-white py-1.5 text-sm font-normal text-gray-900 shadow-lg">

        @if ($filter)
            <div class="flex items-center justify-around px-3 py-1.5">
                <button type="button" x-on:click="pilihSemua('{{ $kolom }}')" class="cursor-pointer rounded px-2 py-1 font-medium hover:bg-gray-100">
                    Pilih semua (<span x-text="nilaiUnik('{{ $kolom }}').length"></span>)
                </button>
                <button type="button" x-on:click="kosongkan('{{ $kolom }}')" class="cursor-pointer rounded px-2 py-1 font-medium hover:bg-gray-100">Kosongkan</button>
            </div>
            <div class="px-3 pb-2">
                <label for="cari-menu-{{ $kolom }}" class="sr-only">Cari nilai kolom {{ $label }}</label>
                <input id="cari-menu-{{ $kolom }}" type="search" x-model="cariMenu" placeholder="Cari..."
                       class="h-9 w-full rounded-md border border-gray-300 bg-white px-2.5 text-sm placeholder:text-gray-400">
            </div>
            <ul class="max-h-48 overflow-y-auto border-y border-gray-100 py-1">
                <template x-for="n in daftarMenu('{{ $kolom }}')" :key="n">
                    <li>
                        <label class="flex cursor-pointer items-center gap-2.5 px-3 py-1.5 hover:bg-gray-50">
                            <input type="checkbox" class="size-4 shrink-0 accent-[#1f66e5]"
                                   :checked="dipilih('{{ $kolom }}', n)" x-on:change="toggleNilai('{{ $kolom }}', n)">
                            <span class="truncate" :title="n" x-text="n === '' ? '(kosong)' : n"></span>
                        </label>
                    </li>
                </template>
                <li x-show="daftarMenu('{{ $kolom }}').length === 0" class="px-3 py-2 text-gray-500">Tidak ditemukan</li>
            </ul>
        @endif

        <div class="pt-1">
            <button type="button" role="menuitem" x-on:click="urutSet('{{ $kolom }}', 'asc'); menuBuka = ''"
                    :class="urutKolom === '{{ $kolom }}' && urutArah === 'asc' ? 'font-medium text-brand' : ''"
                    class="flex w-full cursor-pointer items-center gap-2.5 px-3 py-2 text-left hover:bg-gray-50">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
                Urutkan naik
            </button>
            <button type="button" role="menuitem" x-on:click="urutSet('{{ $kolom }}', 'desc'); menuBuka = ''"
                    :class="urutKolom === '{{ $kolom }}' && urutArah === 'desc' ? 'font-medium text-brand' : ''"
                    class="flex w-full cursor-pointer items-center gap-2.5 px-3 py-2 text-left hover:bg-gray-50">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
                Urutkan turun
            </button>
            <button type="button" role="menuitem" x-on:click="urutHapus(); menuBuka = ''"
                    class="flex w-full cursor-pointer items-center gap-2.5 px-3 py-2 text-left hover:bg-gray-50">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                Hapus urutan
            </button>
        </div>
    </div>
    </template>
</th>