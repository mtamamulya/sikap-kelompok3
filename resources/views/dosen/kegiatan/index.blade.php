@extends('layouts.app')

@section('title', 'Kegiatan')

@section('content')

@php
    // DATA TIRUAN. Nanti diganti data dari controller / database oleh tim backend.
    // status: Diajukan | Direvisi | Disetujui | Ditolak
    $kegiatan = [
        ['nama' => 'Seminar nasional AI',   'kategori' => 'Lomba',             'jenis' => 'Kegiatan dosen',       'tanggal' => '12 September 2026', 'status' => 'Diajukan'],
        ['nama' => 'Pelatihan industri',    'kategori' => 'Magang',            'jenis' => 'Kegiatan Prodi',       'tanggal' => '20 Agustus 2026',   'status' => 'Direvisi'],
        ['nama' => 'Kunjungan industri',    'kategori' => 'Kunjungan Industri', 'jenis' => 'Prodi dan mahasiswa', 'tanggal' => '3 Agustus 2026',    'status' => 'Disetujui'],
        ['nama' => 'Lomba karya ilmiah',    'kategori' => 'Lomba',             'jenis' => 'Kegiatan mahasiswa',   'tanggal' => '15 Juli 2026',      'status' => 'Ditolak'],
        ['nama' => 'Computer Vision',       'kategori' => 'Lomba',             'jenis' => 'Kegiatan mahasiswa',   'tanggal' => '3 Agustus 2026',    'status' => 'Diajukan'],
        ['nama' => 'Sistem Operasi',        'kategori' => 'Kunjungan Industri', 'jenis' => 'Prodi dan mahasiswa', 'tanggal' => '3 Agustus 2026',    'status' => 'Direvisi'],
        ['nama' => 'Jaringan Komputer',     'kategori' => 'Lomba',             'jenis' => 'Kegiatan Prodi',       'tanggal' => '12 September 2026', 'status' => 'Disetujui'],
        ['nama' => 'Sistem Basis Data',     'kategori' => 'Lomba',             'jenis' => 'Kegiatan dosen',       'tanggal' => '12 September 2026', 'status' => 'Ditolak'],
        ['nama' => 'Sistem Operasi',        'kategori' => 'Kunjungan Industri', 'jenis' => 'Prodi dan mahasiswa', 'tanggal' => '20 Agustus 2026',   'status' => 'Diajukan'],
        ['nama' => 'Internet of Things',    'kategori' => 'Magang',            'jenis' => 'Prodi dan mahasiswa',  'tanggal' => '3 Agustus 2026',    'status' => 'Direvisi'],
    ];

    // Beri id tetap (1, 2, 3, ...) supaya tautan ke Detail / Edit tidak berubah saat daftar difilter oleh tab atau pencarian.
    foreach ($kegiatan as $n => &$kg) { $kg['id'] = $n + 1; }
    unset($kg);

    // Jumlah seluruh kegiatan per status (angka di tab). Nanti dihitung backend dari seluruh halaman.
    $jumlah = ['Semua' => 40, 'Diajukan' => 12, 'Direvisi' => 10, 'Disetujui' => 10, 'Ditolak' => 8];

    // Pagination tiruan: halaman aktif 1 dari 10.
    $halaman = 1;
    $nomor   = [1, 2, '...', 9, 10];
    $pageBtn = 'flex size-8 items-center justify-center rounded border text-sm font-semibold';
@endphp

    {{-- Judul halaman + tombol --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Kegiatan</h1>
            <p class="text-2xl text-gray-400">Dashboard / Kegiatan</p>
        </div>
        <a href="{{ route('kegiatan.create') }}" class="mt-2 flex h-11 items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Ajukan Kegiatan
        </a>
    </div>

    {{-- Tab status + pencarian + tabel. Tab dan pencarian memfilter data tiruan lewat Alpine.js.
         Kalau backend sudah ada, tab bisa diganti tautan ?status=... dan pencarian jadi form GET. --}}
    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm"
             x-data="kegiatan(@js($kegiatan), @js($jumlah))">

        {{-- Tab --}}
        <div role="tablist" aria-label="Status kegiatan" class="flex gap-x-2 overflow-x-auto border-b border-gray-200 pb-px [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <template x-for="t in tab" :key="t">
                <button type="button" role="tab" :aria-selected="aktif === t" x-on:click="aktif = t"
                        :class="aktif === t ? 'border-brand text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-700'"
                        class="-mb-px flex shrink-0 cursor-pointer items-center gap-2 border-b-2 px-3 py-2.5 text-sm">
                    <span x-text="t"></span>
                    <span x-show="aktif === t" x-cloak
                          class="flex h-5 min-w-5 items-center justify-center rounded-full bg-brand px-1.5 text-xs text-white"
                          x-text="jumlah[t]"></span>
                </button>
            </template>
        </div>

        {{-- Pencarian --}}
        <div class="relative">
            <label for="q" class="sr-only">Cari nama kegiatan</label>
            <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/>
            </svg>
            <input id="q" name="q" type="search" x-model="cari" placeholder="Cari nama kegiatan..."
                   class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-3 pl-10 text-[15px] placeholder:text-gray-400">
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto rounded-md border border-gray-300">
            <table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2.5 py-3 font-medium">Nama Kegiatan</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2.5 py-3 font-medium">Kategori</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2.5 py-3 font-medium">Jenis</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2.5 py-3 font-medium">Tanggal</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2.5 py-3 font-medium">Status</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2.5 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(k, i) in tampil" :key="k.nama + i">
                        <tr class="border-b border-gray-300 last:border-b-0 hover:bg-gray-50">
                            <td class="truncate px-2.5 py-3" :title="k.nama" x-text="k.nama"></td>
                            <td class="truncate px-2.5 py-3" x-text="k.kategori"></td>
                            <td class="truncate px-2.5 py-3" x-text="k.jenis"></td>
                            <td class="px-2.5 py-3" x-text="k.tanggal"></td>
                            <td class="px-2.5 py-3">
                                <span :class="warna[k.status]" class="inline-block rounded-md border px-2 py-0.5 text-[13px]" x-text="k.status"></span>
                            </td>
                            <td class="px-2.5 py-2">
                                <div class="flex items-center gap-3">
                                    <a :href="`{{ url('/kegiatan') }}/${k.id}`" :aria-label="`Lihat detail ${k.nama}`"
                                       class="flex size-8 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a :href="`{{ url('/kegiatan') }}/${k.id}/edit`" :aria-label="`Edit ${k.nama}`"
                                       class="flex size-8 items-center justify-center rounded-lg bg-orange-100 text-orange-400 hover:bg-orange-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Kosong --}}
            <p x-show="tampil.length === 0" x-cloak class="px-4 py-10 text-center text-sm text-gray-500">
                Tidak ada kegiatan yang cocok dengan filter ini.
            </p>
        </div>

        {{-- Pagination (tampilan saja; nanti diganti $kegiatan->links() dari Laravel) --}}
        <nav aria-label="Halaman" class="flex items-center justify-center gap-2.5 pt-2 pb-4">
            <a href="#" aria-label="Halaman sebelumnya" class="{{ $pageBtn }} border-brand bg-brand text-white hover:bg-blue-700">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            </a>

            @foreach ($nomor as $n)
                @if ($n === '...')
                    <span class="{{ $pageBtn }} border-gray-300 bg-white text-gray-900">...</span>
                @elseif ($n === $halaman)
                    <a href="#" aria-current="page" aria-label="Halaman {{ $n }}" class="{{ $pageBtn }} border-brand bg-white text-brand">{{ $n }}</a>
                @else
                    <a href="#" aria-label="Halaman {{ $n }}" class="{{ $pageBtn }} border-gray-300 bg-white text-gray-900 hover:bg-gray-50">{{ $n }}</a>
                @endif
            @endforeach

            <a href="#" aria-label="Halaman berikutnya" class="{{ $pageBtn }} border-brand bg-blue-100 text-brand hover:bg-blue-200">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        </nav>
    </section>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kegiatan', (daftar, jumlah) => ({
            daftar: daftar,
            jumlah: jumlah,
            tab: ['Semua', 'Diajukan', 'Direvisi', 'Disetujui', 'Ditolak'],
            aktif: 'Semua',
            cari: '',

            // warna badge status
            warna: {
                Diajukan:  'border-blue-200 bg-blue-100 text-blue-700',
                Direvisi:  'border-orange-200 bg-orange-100 text-orange-700',
                Disetujui: 'border-green-200 bg-green-100 text-green-700',
                Ditolak:   'border-red-200 bg-red-100 text-red-600',
            },

            get tampil() {
                const q = this.cari.trim().toLowerCase();
                return this.daftar.filter(k =>
                    (this.aktif === 'Semua' || k.status === this.aktif) &&
                    (q === '' || k.nama.toLowerCase().includes(q))
                );
            },
        }));
    });
</script>
@endpush