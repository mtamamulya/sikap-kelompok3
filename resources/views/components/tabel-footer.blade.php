{{--
    Bagian bawah tabel: keterangan jumlah data, pilihan baris per halaman, dan pagination yang berfungsi.
    Harus berada di dalam komponen Alpine yang memakai tabelData (lihat components/tabel-js).
    Nanti kalau backend sudah ada, bagian ini bisa diganti $data->links() dari Laravel.
--}}
<div class="flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600">
    <p aria-live="polite"
       x-text="terfilter.length ? `Menampilkan ${dari}–${sampai} dari ${terfilter.length} data` : 'Tidak ada data'"></p>
    <label class="flex items-center gap-2">
        <span>Baris per halaman</span>
        <select x-model.number="perHalaman" x-on:change="halaman = 1"
                class="h-9 rounded-md border border-gray-300 bg-white px-2 text-sm text-gray-900">
            <template x-for="u in ukuran" :key="u"><option :value="u" x-text="u"></option></template>
        </select>
    </label>
</div>

<nav aria-label="Halaman" x-show="totalHalaman > 1" class="flex items-center justify-center gap-2.5 pt-1 pb-4">
    <button type="button" x-on:click="ke(halamanAktif - 1)" :disabled="halamanAktif === 1" aria-label="Halaman sebelumnya"
            class="flex size-8 cursor-pointer items-center justify-center rounded border border-brand bg-brand text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:border-gray-300 disabled:bg-gray-100 disabled:text-gray-400">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
    </button>

    <template x-for="(n, i) in nomorHalaman" :key="i">
        <span>
            <span x-show="n === '...'" class="flex size-8 items-center justify-center rounded border border-gray-300 bg-white text-sm font-semibold">...</span>
            <button type="button" x-show="n !== '...'" x-on:click="ke(n)" x-text="n"
                    :aria-label="`Halaman ${n}`" :aria-current="n === halamanAktif ? 'page' : null"
                    :class="n === halamanAktif ? 'border-brand bg-white text-brand' : 'border-gray-300 bg-white text-gray-900 hover:bg-gray-50'"
                    class="flex size-8 cursor-pointer items-center justify-center rounded border text-sm font-semibold"></button>
        </span>
    </template>

    <button type="button" x-on:click="ke(halamanAktif + 1)" :disabled="halamanAktif === totalHalaman" aria-label="Halaman berikutnya"
            class="flex size-8 cursor-pointer items-center justify-center rounded border border-brand bg-brand text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:border-gray-300 disabled:bg-gray-100 disabled:text-gray-400">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
    </button>
</nav>