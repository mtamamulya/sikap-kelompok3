@extends('layouts.app')

@section('title', 'Prestasi')

@section('content')

@php
    // DATA TIRUAN. Nanti diganti data dari controller / database oleh tim backend.
    $prestasi = [
        ['nama' => 'Juara 1 Lomba Karya Tulis Ilmiah Nasional 2026', 'penghargaan' => 'Best Poster', 'tingkat' => 'Nasional',      'penyelenggara' => 'Institut Teknologi Sepuluh Nopember'],
        ['nama' => 'Juara 1 Lomba Karya Tulis Ilmiah Internasional', 'penghargaan' => 'Best Desain', 'tingkat' => 'Internasional', 'penyelenggara' => 'PT. KAI'],
        ['nama' => 'Juara 1 Poster',          'penghargaan' => 'Juara 1',   'tingkat' => 'Provinsi',      'penyelenggara' => 'Freeport'],
        ['nama' => 'Juara 2 Design Graphic',  'penghargaan' => 'Juara 2',   'tingkat' => 'Kabupaten/kota', 'penyelenggara' => 'Institut Teknologi Sepuluh Nopember'],
        ['nama' => 'Lomba Karate',            'penghargaan' => 'Harapan 3', 'tingkat' => 'Provinsi',      'penyelenggara' => 'PT. KAI'],
        ['nama' => 'Lomba LKTIN',             'penghargaan' => 'Juara 3',   'tingkat' => 'Internasional', 'penyelenggara' => 'Freeport'],
        ['nama' => 'Lomba LKTIN',             'penghargaan' => 'Harapan 2', 'tingkat' => 'Kabupaten/kota', 'penyelenggara' => 'Institut Teknologi Sepuluh Nopember'],
        ['nama' => 'Lomba LKTIN',             'penghargaan' => 'Harapan 1', 'tingkat' => 'Provinsi',      'penyelenggara' => 'PT. KAI'],
        ['nama' => 'Lomba LKTIN',             'penghargaan' => 'Juara 1',   'tingkat' => 'Nasional',      'penyelenggara' => 'Freeport'],
        ['nama' => 'Lomba LKTIN',             'penghargaan' => 'Juara 2',   'tingkat' => 'Nasional',      'penyelenggara' => 'Institut Teknologi Sepuluh Nopember'],
    ];

    // id tetap (1, 2, 3, ...) supaya tautan ke Detail / Edit tidak berubah saat tabel dicari, difilter, atau diurutkan.
    foreach ($prestasi as $n => &$d) { $d['id'] = $n + 1; }
    unset($d);

    $daftarTingkat = ['Internal', 'Kabupaten/kota', 'Provinsi', 'Nasional', 'Internasional'];

    $selectClass = 'h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-9 pl-3.5 text-[15px] text-gray-900';
@endphp

    {{-- Judul halaman + tombol tambah --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-4xl font-bold tracking-tight">Prestasi</h1>
            <p class="text-2xl text-gray-400">Dashboard / Prestasi</p>
        </div>
        <a href="{{ route('prestasi.create') }}" class="mt-2 flex h-11 items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Prestasi
        </a>
    </div>

    {{-- Pencarian, filter, urut kolom, dan pagination berjalan di browser (Alpine.js + quick sort pada data tiruan).
         Kalau backend sudah ada, pencarian / filter / urutan bisa diganti parameter ?q=...&urut=... dan pagination dari Laravel. --}}
    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm"
             x-data="prestasi(@js($prestasi))">

        <div class="flex flex-wrap items-center gap-4">
            <div class="relative min-w-60 flex-[1_1_320px]">
                <label for="q" class="sr-only">Cari nama prestasi</label>
                <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
                <input id="q" name="q" type="search" x-model="cari" x-on:input="halaman = 1" placeholder="Cari nama, penghargaan, atau penyelenggara..."
                       class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-3 pl-10 text-[15px] placeholder:text-gray-400">
            </div>

            <div class="relative w-46">
                <label for="tingkat" class="sr-only">Filter tingkat prestasi</label>
                <select id="tingkat" x-model="f.tingkat" x-on:change="halaman = 1" class="{{ $selectClass }}">
                    <option value="">Tingkat Prestasi</option>
                    @foreach ($daftarTingkat as $o)
                        <option value="{{ $o }}">{{ $o }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </div>
        </div>

        {{-- Tabel. Klik judul kolom untuk mengurutkan naik / turun. --}}
        <div class="overflow-x-auto rounded-md border border-gray-300">
            <table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <x-th-urut kolom="nama" label="Nama Prestasi" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="penghargaan" label="Penghargaan" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="tingkat" label="Tingkat Prestasi" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="penyelenggara" label="Penyelenggara" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <th scope="col" class="w-1/5 border-b border-gray-300 px-2.5 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="k in tampil" :key="k.id">
                        <tr class="border-b border-gray-300 last:border-b-0 hover:bg-gray-50">
                            <td class="truncate px-2.5 py-3" :title="k.nama" x-text="k.nama"></td>
                            <td class="truncate px-2.5 py-3" :title="k.penghargaan" x-text="k.penghargaan"></td>
                            <td class="truncate px-2.5 py-3" :title="k.tingkat" x-text="k.tingkat"></td>
                            <td class="truncate px-2.5 py-3" :title="k.penyelenggara" x-text="k.penyelenggara"></td>
                            <td class="px-2.5 py-2">
                                <div class="flex items-center gap-3">
                                    <a :href="`{{ url('/prestasi') }}/${k.id}`" :aria-label="`Lihat detail ${k.nama}`"
                                       class="flex size-8 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a :href="`{{ url('/prestasi') }}/${k.id}/edit`" :aria-label="`Edit ${k.nama}`"
                                       class="flex size-8 items-center justify-center rounded-lg bg-orange-100 text-orange-400 hover:bg-orange-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                    </a>
                                    {{-- Hapus: membuka modal konfirmasi (components/modal-hapus). action '#' nanti diganti route hapus dari backend. --}}
                                    <button type="button" :aria-label="`Hapus ${k.nama}`"
                                            x-on:click="$dispatch('minta-hapus', { jenis: 'prestasi', nama: k.nama, action: '#' })"
                                            class="flex size-8 cursor-pointer items-center justify-center rounded-lg bg-red-100 text-red-500 hover:bg-red-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 7h14M10 7V5h4v2M7 7l1 12h8l1-12M10 11v5M14 11v5"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <p x-show="terfilter.length === 0" x-cloak class="px-4 py-10 text-center text-sm text-gray-500">
                Tidak ada prestasi yang cocok dengan pencarian atau filter ini.
            </p>
        </div>

        <x-tabel-footer />
    </section>

    <x-modal-hapus />

    <x-tabel-js />

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('prestasi', (daftar) => tabelData({
            daftar,
            cari: '',
            f: { tingkat: '' },

            // Daftar yang lolos pencarian dan filter. Dipakai tabel, pengurutan, dan pagination.
            get terfilter() {
                const q = this.cari.trim().toLowerCase();
                return this.daftar.filter(k => (q === '' || k.nama.toLowerCase().includes(q) || k.penyelenggara.toLowerCase().includes(q) || k.penghargaan.toLowerCase().includes(q)) &&
                    (this.f.tingkat === '' || k.tingkat === this.f.tingkat));
            },
        }, { perHalaman: 5, kunci: { tingkat: (k) => ['Internal', 'Kabupaten/kota', 'Provinsi', 'Nasional', 'Internasional'].indexOf(k.tingkat) } }));
    });
</script>
@endpush