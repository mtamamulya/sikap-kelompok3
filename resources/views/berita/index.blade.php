@extends('layouts.app')

@section('title', 'Pengajuan Berita')

@php
    // DATA TIRUAN dari berita/data.php. Nanti diganti data dari controller / database oleh tim backend.
    $berita = require resource_path('views/berita/data.php');
@endphp

@section('content')

    {{-- Judul halaman + tombol --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Pengajuan Berita</h1>
            <p class="text-2xl text-gray-400">Dashboard / Pengajuan / Berita</p>
        </div>
        <a href="{{ route('berita.create') }}" class="mt-2 flex h-11 items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Ajukan Berita
        </a>
    </div>

    {{-- Tab status, pencarian, urut kolom, dan pagination berjalan di browser (Alpine.js + quick sort pada data tiruan).
         Kalau backend sudah ada, bisa diganti parameter ?status=...&q=...&urut=... dan pagination dari Laravel. --}}
    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm"
             x-data="beritaDaftar(@js($berita))">

        {{-- Tab status. Angka biru hanya tampil di tab yang aktif. --}}
        <div role="tablist" aria-label="Status berita" class="flex gap-x-2 overflow-x-auto border-b border-gray-200 pb-px [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <template x-for="t in tab" :key="t">
                <button type="button" role="tab" :aria-selected="aktif === t" x-on:click="aktif = t; halaman = 1"
                        :class="aktif === t ? 'border-brand text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-700'"
                        class="-mb-px flex shrink-0 cursor-pointer items-center gap-2 border-b-2 px-3 py-2.5 text-sm">
                    <span x-text="t"></span>
                    <span x-show="aktif === t" x-cloak
                          class="flex h-5 min-w-5 items-center justify-center rounded-full bg-brand px-1.5 text-xs text-white"
                          x-text="jumlah(t)"></span>
                </button>
            </template>
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <div class="relative min-w-60 flex-[1_1_320px]">
                <label for="q" class="sr-only">Cari judul atau kategori berita</label>
                <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
                <input id="q" name="q" type="search" x-model="cari" x-on:input="halaman = 1" placeholder="Cari judul atau kategori berita..."
                       class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-3 pl-10 text-[15px] placeholder:text-gray-400">
            </div>
        </div>

        {{-- Tabel. Klik judul kolom untuk mengurutkan naik / turun. --}}
        <div class="overflow-x-auto rounded-md border border-gray-300">
            <table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <x-th-urut kolom="judul" label="Judul Berita" class="w-[28%] border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="kategori" label="Kategori" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="tanggal" label="Tanggal Diajukan" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="status" label="Status" class="w-1/6 border-b border-gray-300 p-0 font-medium" />
                        <th scope="col" class="border-b border-gray-300 px-2.5 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="b in tampil" :key="b.id">
                        <tr class="border-b border-gray-300 last:border-b-0 hover:bg-gray-50">
                            <td class="truncate px-2.5 py-3" :title="b.judul" x-text="b.judul"></td>
                            <td class="truncate px-2.5 py-3" :title="b.kategori" x-text="b.kategori"></td>
                            <td class="px-2.5 py-3" x-text="b.tanggal"></td>
                            <td class="px-2.5 py-3"><span :class="warna[b.status]" class="inline-block rounded-md border px-2 py-0.5 text-[13px]" x-text="b.status"></span></td>
                            <td class="px-2.5 py-2">
                                <div class="flex items-center gap-3">
                                    <a :href="`{{ url('/pengajuan/berita') }}/${b.id}`" :aria-label="`Lihat detail ${b.judul}`"
                                       class="flex size-8 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="2.8"/></svg>
                                    </a>
                                    {{-- Edit hanya untuk berita yang masih bisa diubah: Draft dan Direvisi --}}
                                    <template x-if="b.status === 'Draft' || b.status === 'Direvisi'">
                                        <a :href="`{{ url('/pengajuan/berita') }}/${b.id}/edit`" :aria-label="`Edit ${b.judul}`"
                                           class="flex size-8 items-center justify-center rounded-lg bg-orange-100 text-orange-400 hover:bg-orange-200">
                                            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                        </a>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <p x-show="terfilter.length === 0" x-cloak class="px-4 py-10 text-center text-sm text-gray-500">
                Tidak ada berita yang cocok dengan filter ini.
            </p>
        </div>

        {{-- Pagination (tanpa keterangan jumlah data dan pilihan baris per halaman). Bekerja lewat tabelData di components/tabel-js. --}}
        <nav aria-label="Halaman" x-show="totalHalaman > 1" class="flex items-center justify-center gap-2.5 pt-1 pb-4">
            <button type="button" x-on:click="ke(halamanAktif - 1)" :disabled="halamanAktif === 1" aria-label="Halaman sebelumnya"
                    class="flex size-8 cursor-pointer items-center justify-center rounded border border-brand bg-brand text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
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
                    class="flex size-8 cursor-pointer items-center justify-center rounded border border-brand bg-blue-100 text-brand hover:bg-blue-200 disabled:cursor-not-allowed disabled:opacity-50">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
            </button>
        </nav>
    </section>

    <x-tabel-js />

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('beritaDaftar', (daftar) => tabelData({
            daftar,
            cari: '',
            tab: ['Semua', 'Draft', 'Diajukan', 'Direvisi', 'Dipublikasikan', 'Ditolak'],
            aktif: 'Semua',
            // warna badge status
            warna: {
                Draft:          'border-gray-300 bg-gray-100 text-gray-700',
                Diajukan:       'border-blue-200 bg-blue-100 text-blue-700',
                Direvisi:       'border-orange-200 bg-orange-100 text-orange-700',
                Dipublikasikan: 'border-green-200 bg-green-100 text-green-700',
                Ditolak:        'border-red-200 bg-red-100 text-red-600',
            },

            // Daftar yang lolos tab dan pencarian. Dipakai tabel, pengurutan, dan pagination.
            get terfilter() {
                const q = this.cari.trim().toLowerCase();
                return this.daftar.filter(b => (this.aktif === 'Semua' || b.status === this.aktif) &&
                    (q === '' || b.judul.toLowerCase().includes(q) || b.kategori.toLowerCase().includes(q)));
            },
            // Angka di tab dihitung dari data, jadi selalu cocok dengan isi tabel.
            jumlah(t) { return t === 'Semua' ? this.daftar.length : this.daftar.filter(b => b.status === t).length; },
        }, { perHalaman: 5, kunci: { tanggal: (b) => tanggalKeAngka(b.tanggal) } }));
    });
</script>
@endpush