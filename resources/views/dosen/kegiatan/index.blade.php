@extends('layouts.app')

@section('title', 'Kegiatan')

@section('content')

@php
    // Data dari KegiatanController ($allActivities). Dua field tambahan, keduanya OPSIONAL:
    //   'peran'     => 'Ketua' | 'Anggota'  (kalau belum dikirim backend, semua dianggap Ketua)
    //   'raw_date'  => 'YYYY-MM-DD'          (sudah ada di controller; dipakai supaya urut tanggal benar)
    // SEMENTARA: data contoh supaya tampilan terlihat. HAPUS blok ini sebelum push.
    $allActivities = collect([
        ['id'=>1, 'name'=>'KMIPN VIII 2026',                  'type'=>'Lomba',             'date'=>'13 Mei 2026', 'raw_date'=>'2026-05-13', 'status'=>'Diajukan',  'peran'=>'Ketua'],
        ['id'=>2, 'name'=>'Seminar Nasional Kecerdasan Buatan','type'=>'Seminar',          'date'=>'02 Mar 2026', 'raw_date'=>'2026-03-02', 'status'=>'Disetujui', 'peran'=>'Anggota'],
        ['id'=>3, 'name'=>'Kunjungan Industri ke PT Telkom',  'type'=>'Kunjungan Industri','date'=>'20 Jan 2026', 'raw_date'=>'2026-01-20', 'status'=>'Diajukan',  'peran'=>'Anggota'],
        ['id'=>4, 'name'=>'Magang Dosen di Industri',         'type'=>'Magang',            'date'=>'08 Feb 2026', 'raw_date'=>'2026-02-08', 'status'=>'Direvisi',  'peran'=>'Ketua'],
        ['id'=>5, 'name'=>'Pelatihan Laravel untuk Mahasiswa','type'=>'Pelatihan',         'date'=>'15 Apr 2026', 'raw_date'=>'2026-04-15', 'status'=>'Disetujui', 'peran'=>'Ketua'],
        ['id'=>6, 'name'=>'Workshop Cloud Computing',         'type'=>'Workshop',          'date'=>'11 Des 2025', 'raw_date'=>'2025-12-11', 'status'=>'Ditolak',   'peran'=>'Anggota'],
        ['id'=>7, 'name'=>'Lomba Karya Ilmiah Nasional',      'type'=>'Lomba',             'date'=>'30 Nov 2025', 'raw_date'=>'2025-11-30', 'status'=>'Selesai',   'peran'=>'Ketua'],
    ]);
    $kegiatan = $allActivities->map(fn ($row) => [
        'id'          => $row['id'],
        'nama'        => $row['name'],
        'kategori'    => $row['type'],
        'jenis'       => 'Kegiatan dosen',
        'tanggal'     => $row['date'],
        'tanggal_raw' => $row['raw_date'] ?? '',
        'peran'       => $row['peran'] ?? 'Ketua',
        'status'      => $row['status'],
    ])->values();
@endphp

    {{-- Judul halaman + tombol --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Kegiatan</h1>
            <p class="text-2xl text-gray-400">Dashboard / Kegiatan</p>
        </div>
        <a href="{{ route('dosen.kegiatan.create') }}" class="mt-2 flex h-11 items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Ajukan Kegiatan
        </a>
    </div>

    {{-- Tab status, pencarian, filter peran, urut kolom, dan pagination berjalan di browser (Alpine.js pada data dari controller).
         Daftar memuat kegiatan sebagai Ketua dan kegiatan di mana dosen di-tag sebagai Anggota. --}}
    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm"
             x-data="kegiatan(@js($kegiatan))">

        {{-- Tab status. Angka biru hanya tampil di tab yang aktif dan ikut filter peran. --}}
        <div role="tablist" aria-label="Status kegiatan" class="flex gap-x-2 overflow-x-auto border-b border-gray-200 pb-px [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
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

        {{-- Pencarian + filter peran --}}
        <div class="flex flex-wrap items-center gap-4">
            <div class="relative min-w-60 flex-[1_1_320px]">
                <label for="q" class="sr-only">Cari nama kegiatan</label>
                <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/>
                </svg>
                <input id="q" name="q" type="search" x-model="cari" x-on:input="halaman = 1" placeholder="Cari nama kegiatan..."
                       class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-3 pl-10 text-[15px] placeholder:text-gray-400">
            </div>

            <div class="relative w-full sm:w-46">
                <label for="peran" class="sr-only">Filter peran</label>
                <select id="peran" x-model="peran" x-on:change="halaman = 1"
                        class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-9 pl-3.5 text-[15px] text-gray-900">
                    <option value="">Semua Peran</option>
                    <option value="Ketua">Ketua</option>
                    <option value="Anggota">Anggota</option>
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </div>
        </div>

        {{-- Tabel. Klik judul kolom untuk mengurutkan, klik ikon garis tiga untuk menu kolom. --}}
        <div class="overflow-x-auto rounded-md border border-gray-300">
            <table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <x-th-urut kolom="nama" label="Nama Kegiatan" class="w-[21%] border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="kategori" label="Kategori" class="w-[14%] border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="jenis" label="Jenis" class="w-[13%] border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="tanggal" label="Tanggal" class="w-[14%] border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="peran" label="Peran" class="w-[12%] border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="status" label="Status" class="w-[14%] border-b border-gray-300 p-0 font-medium" />
                        <th scope="col" class="w-[12%] border-b border-gray-300 px-2.5 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(k, i) in tampil" :key="k.id + '-' + i">
                        <tr class="border-b border-gray-300 last:border-b-0 hover:bg-gray-50">
                            <td class="truncate px-2.5 py-3" :title="k.nama" x-text="k.nama"></td>
                            <td class="truncate px-2.5 py-3" x-text="k.kategori"></td>
                            <td class="truncate px-2.5 py-3" x-text="k.jenis"></td>
                            <td class="px-2.5 py-3" x-text="k.tanggal"></td>
                            <td class="px-2.5 py-3">
                                <span :class="warnaPeran[k.peran] || warnaNetral" class="inline-block rounded-md border px-2 py-0.5 text-[13px]" x-text="k.peran"></span>
                            </td>
                            <td class="px-2.5 py-3">
                                <span :class="warna[k.status] || warnaNetral" class="inline-block rounded-md border px-2 py-0.5 text-[13px]" x-text="k.status"></span>
                            </td>
                            <td class="px-2.5 py-2">
                                <div class="flex items-center gap-3">
                                    <a :href="`{{ url('/dosen/kegiatan') }}/${k.id}`" :aria-label="`Lihat detail ${k.nama}`"
                                       class="flex size-8 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    {{-- Edit hanya untuk kegiatan milik sendiri (Ketua). Sebagai Anggota, dosen hanya melihat detail. --}}
                                    <template x-if="k.peran === 'Ketua'">
                                        <a :href="`{{ url('/dosen/kegiatan') }}/${k.id}/edit`" :aria-label="`Edit ${k.nama}`"
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

            {{-- Kosong --}}
            <p x-show="terfilter.length === 0" x-cloak class="px-4 py-10 text-center text-sm text-gray-500">
                Tidak ada kegiatan yang cocok dengan filter ini.
            </p>
        </div>

        <x-tabel-footer />
    </section>

    <x-tabel-js />

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kegiatan', (daftar) => tabelData({
            daftar,
            tab: ['Semua', 'Diajukan', 'Direvisi', 'Disetujui', 'Ditolak', 'Selesai'],
            aktif: 'Semua',
            cari: '',
            peran: '',   // '' = semua peran, atau 'Ketua' / 'Anggota'

            // warna badge status dan peran
            warna: {
                Diajukan:  'border-blue-200 bg-blue-100 text-blue-700',
                Direvisi:  'border-orange-200 bg-orange-100 text-orange-700',
                Disetujui: 'border-green-200 bg-green-100 text-green-700',
                Ditolak:   'border-red-200 bg-red-100 text-red-600',
            },
            warnaPeran: {
                Ketua:   'border-blue-200 bg-blue-100 text-blue-700',
                Anggota: 'border-teal-200 bg-teal-100 text-teal-700',
            },
            warnaNetral: 'border-gray-300 bg-gray-100 text-gray-700',

            // Daftar yang lolos peran, tab status, dan pencarian. Dipakai tabel, pengurutan, dan pagination.
            get terfilter() {
                const q = this.cari.trim().toLowerCase();
                return this.daftar.filter(k =>
                    (this.peran === '' || k.peran === this.peran) &&
                    (this.aktif === 'Semua' || k.status === this.aktif) &&
                    (q === '' || k.nama.toLowerCase().includes(q))
                );
            },
            // Angka di tab dihitung dari data (mengikuti filter peran), jadi selalu cocok dengan isi tabel.
            jumlah(t) {
                const dasar = this.daftar.filter(k => this.peran === '' || k.peran === this.peran);
                return t === 'Semua' ? dasar.length : dasar.filter(k => k.status === t).length;
            },
        }, { perHalaman: 5, kunci: { tanggal: (k) => k.tanggal_raw || '' } }));
    });
</script>
@endpush