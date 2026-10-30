@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')

@php
    // DATA TIRUAN. Nanti diganti data dari controller / database oleh tim backend.
    // type: pengajuan | tim | sistem.   g: kelompok waktu (Hari ini | Kemarin | Lebih lama).
    // href: tujuan saat notifikasi diklik. '#' = belum ada halamannya.
    $notifikasi = [
        ['id' => 1, 'g' => 'Hari ini',   'type' => 'pengajuan', 'title' => 'Pengajuan diterima',               'body' => 'Penelitian "Pengembangan Sistem Monitoring Berbasis IoT" telah diterima oleh admin.',                              'time' => '09.12',       'unread' => true,  'href' => '#'],
        ['id' => 2, 'g' => 'Hari ini',   'type' => 'pengajuan', 'title' => 'Pengajuan perlu direvisi',         'body' => 'Pengabdian "Pelatihan Literasi Digital UMKM" dikembalikan dengan catatan: lengkapi nomor SK dan mitra.',          'time' => '08.40',       'unread' => true,  'href' => '#'],
        ['id' => 3, 'g' => 'Hari ini',   'type' => 'tim',       'title' => 'Anda ditambahkan sebagai anggota', 'body' => 'Wiktasari menambahkan Anda sebagai anggota penelitian "Analisis Citra Satelit untuk Pemetaan Lahan".',            'time' => '07.55',       'unread' => true,  'href' => '#'],
        ['id' => 4, 'g' => 'Kemarin',    'type' => 'pengajuan', 'title' => 'Pengajuan sedang direview',        'body' => 'Pengajuan penelitian "Model Prediksi Beban Listrik" mulai ditinjau oleh admin.',                                 'time' => '16.20',       'unread' => false, 'href' => '#'],
        ['id' => 5, 'g' => 'Kemarin',    'type' => 'pengajuan', 'title' => 'Pengajuan ditolak',                'body' => 'Pengabdian "Penyuluhan Sanitasi Desa" ditolak. Lihat alasan penolakan pada detail pengajuan.',                   'time' => '10.05',       'unread' => false, 'href' => '#'],
        ['id' => 6, 'g' => 'Kemarin',    'type' => 'sistem',    'title' => 'Kata sandi berhasil diubah',       'body' => 'Kata sandi akun Anda diubah dari halaman profil. Jika bukan Anda, segera hubungi admin.',                         'time' => '08.31',       'unread' => false, 'href' => route('profil')],
        ['id' => 7, 'g' => 'Lebih lama', 'type' => 'pengajuan', 'title' => 'Kegiatan ditandai selesai',        'body' => 'Penelitian "Sistem Absensi Wajah" ditandai selesai. Dokumen laporan akhir sudah tersimpan.',                      'time' => '12 Okt 2026', 'unread' => false, 'href' => '#'],
        ['id' => 8, 'g' => 'Lebih lama', 'type' => 'sistem',    'title' => 'Pengingat pembaruan data',         'body' => 'Periksa kembali riwayat pangkat dan golongan Anda sebelum akhir semester.',                                      'time' => '9 Okt 2026',  'unread' => false, 'href' => route('profil')],
    ];
@endphp

    {{-- Seluruh halaman memakai satu data Alpine: filter, daftar, dan status dibaca --}}
    <div class="flex flex-col gap-5" x-data="notifikasi(@js($notifikasi))">

        {{-- Judul + tombol --}}
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div class="flex flex-col gap-1.5">
                <h1 class="text-3xl font-bold tracking-tight">Notifikasi</h1>
                <p class="text-2xl text-gray-400">Dashboard / Notifikasi</p>
            </div>
            <button type="button" x-on:click="tandaiSemua()" :disabled="belumDibaca === 0"
                    class="flex h-11 cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-4.5 text-[15px] font-medium hover:bg-gray-50 disabled:cursor-not-allowed disabled:text-gray-400 disabled:hover:bg-white">
                <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 13l4 4 7-9M11 15.5l1.5 1.5 8-10"/></svg>
                Tandai semua dibaca
            </button>
        </div>

        {{-- Filter --}}
        <div role="group" aria-label="Filter notifikasi" class="flex flex-wrap gap-2">
            <template x-for="c in chip" :key="c.kunci">
                <button type="button" x-on:click="filter = c.kunci" :aria-pressed="filter === c.kunci"
                        :class="filter === c.kunci ? 'border-brand bg-brand font-semibold text-white' : 'border-gray-300 bg-white font-medium text-gray-700 hover:bg-gray-50'"
                        class="flex min-h-11 cursor-pointer items-center gap-2 rounded-full border px-4 text-sm">
                    <span x-text="c.label"></span>
                    <span :class="filter === c.kunci ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-700'"
                          class="flex h-[22px] min-w-[22px] items-center justify-center rounded-full px-1.5 text-xs font-semibold" x-text="c.jumlah"></span>
                </button>
            </template>
        </div>

        {{-- Daftar, dikelompokkan per waktu --}}
        <div class="flex max-w-full flex-col gap-5">
            <template x-for="g in grup" :key="g.judul">
                <section :aria-label="g.judul" class="flex flex-col gap-2">
                    <h2 class="text-sm font-semibold tracking-wide text-gray-500" x-text="g.judul"></h2>
                    <ul class="overflow-hidden rounded-lg bg-white shadow-sm">
                        <template x-for="n in g.item" :key="n.id">
                            <li class="border-t border-gray-200 first:border-t-0">
                                <a :href="n.href" x-on:click="n.href === '#' && $event.preventDefault(); baca(n)"
                                   :class="n.unread ? 'bg-blue-50' : 'bg-white'"
                                   class="flex items-start gap-3.5 px-5 py-4 hover:bg-blue-50/60">
                                    {{-- ikon sesuai jenis --}}
                                    <span :class="warna[n.type].tile" class="flex size-11 shrink-0 items-center justify-center rounded-xl">
                                        <svg x-show="n.type === 'pengajuan'" class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4.5" y="5" width="15" height="15.5" rx="2.5"/><path d="M9 3.5h6v3H9zM9 13.5l2.2 2.2 4-4.4"/></svg>
                                        <svg x-show="n.type === 'tim'" x-cloak class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8.5" r="3.2"/><path d="M3 19.5c0-3.1 2.7-5 6-5s6 1.9 6 5M16 5.5a3.2 3.2 0 0 1 0 6M18 14.8c1.8.6 3 2 3 4.2"/></svg>
                                        <svg x-show="n.type === 'sistem'" x-cloak class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7.5 3v5.5c0 4.5-3 8-7.5 9.5-4.5-1.5-7.5-5-7.5-9.5V6z"/><path d="M9 12l2.2 2.2 4-4.4"/></svg>
                                    </span>

                                    <div class="flex min-w-0 flex-1 flex-col gap-1">
                                        <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1.5">
                                            <span :class="n.unread ? 'font-bold' : 'font-medium'" class="text-[15px]" x-text="n.title"></span>
                                            <span :class="warna[n.type].tag" class="rounded-md px-2 py-0.5 text-xs font-medium" x-text="label[n.type]"></span>
                                        </div>
                                        <span class="text-sm leading-normal text-gray-600" x-text="n.body"></span>
                                        <span class="text-[13px] text-gray-400" x-text="n.time"></span>
                                    </div>

                                    {{-- titik biru = belum dibaca --}}
                                    <div class="flex shrink-0 items-center pt-1">
                                        <template x-if="n.unread">
                                            <span>
                                                <span class="sr-only">Belum dibaca</span>
                                                <span aria-hidden="true" class="block size-2.5 rounded-full bg-brand"></span>
                                            </span>
                                        </template>
                                    </div>
                                </a>
                            </li>
                        </template>
                    </ul>
                </section>
            </template>

            {{-- Kosong --}}
            <div x-show="tampil.length === 0" x-cloak
                 class="flex flex-col items-center gap-2.5 rounded-lg bg-white px-6 py-12 text-center shadow-sm">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-500">
                    <svg class="size-[26px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 16.5V11a6 6 0 0 1 12 0v5.5l1.5 1.5h-15zM10 20.5a2 2 0 0 0 4 0"/></svg>
                </span>
                <span class="text-[17px] font-semibold">Tidak ada notifikasi</span>
                <span class="max-w-85 text-sm leading-normal text-gray-500">Tidak ada notifikasi yang cocok dengan filter ini. Coba pilih filter lain.</span>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notifikasi', (daftar) => ({
            daftar: daftar,
            filter: 'semua',

            label: { pengajuan: 'Pengajuan', tim: 'Tim', sistem: 'Sistem' },
            warna: {
                pengajuan: { tile: 'bg-blue-100 text-blue-700',     tag: 'bg-blue-100 text-blue-700' },
                tim:       { tile: 'bg-purple-100 text-purple-700', tag: 'bg-purple-100 text-purple-700' },
                sistem:    { tile: 'bg-green-100 text-green-800',   tag: 'bg-green-100 text-green-800' },
            },

            init() {
                // Angka merah di lonceng header ikut sama dengan jumlah belum dibaca
                this.$watch('belumDibaca', (n) => window.dispatchEvent(new CustomEvent('notif-count', { detail: n })));
                window.dispatchEvent(new CustomEvent('notif-count', { detail: this.belumDibaca }));
            },

            get belumDibaca() { return this.daftar.filter(n => n.unread).length; },
            cocok(n, kunci) {
                return kunci === 'semua' || (kunci === 'belum' ? n.unread : n.type === kunci);
            },
            get chip() {
                return [
                    ['semua', 'Semua'], ['belum', 'Belum dibaca'],
                    ['pengajuan', 'Pengajuan'], ['tim', 'Tim'], ['sistem', 'Sistem'],
                ].map(([kunci, label]) => ({ kunci, label, jumlah: this.daftar.filter(n => this.cocok(n, kunci)).length }));
            },
            get tampil() { return this.daftar.filter(n => this.cocok(n, this.filter)); },
            get grup() {
                return ['Hari ini', 'Kemarin', 'Lebih lama']
                    .map(judul => ({ judul, item: this.tampil.filter(n => n.g === judul) }))
                    .filter(g => g.item.length > 0);
            },

            baca(n) { n.unread = false; },
            tandaiSemua() { this.daftar.forEach(n => { n.unread = false; }); },
        }));
    });
</script>
@endpush