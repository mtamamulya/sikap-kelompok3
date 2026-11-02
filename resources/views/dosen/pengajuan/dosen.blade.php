@extends('layouts.app')

@section('title', 'Pengajuan Dosen')

@php
    $daftar = [
        ['judul' => 'Penyuluhan Literasi Digital', 'oleh' => 'Filda Huwani Dewi', 'kategori' => 'Pengabdian', 'tanggal' => '3 Oktober 2026', 'status' => 'Diajukan', 'catatan' => null],
        ['judul' => 'Seminar Nasional AI', 'oleh' => 'Angga Wahyu Wibowo', 'kategori' => 'Pengabdian', 'tanggal' => '15 Agustus 2026', 'status' => 'Direview', 'catatan' => null],
        ['judul' => 'Pelatihan Industri', 'oleh' => 'Kuwat Santoso', 'kategori' => 'Pengabdian', 'tanggal' => '12 Agustus 2026', 'status' => 'Direvisi', 'catatan' => 'Lampirkan surat tugas yang sudah ditandatangani.'],
        ['judul' => 'Woorkshop Penulisan Jurnal', 'oleh' => 'Tahan Prahara', 'kategori' => 'Pengabdian', 'tanggal' => '2 Juli 2026', 'status' => 'Ditolak', 'catatan' => 'Jadwal bentrok dengan kegiatan fakultas pada tanggal yang sama.'],
        ['judul' => 'Kunjungan Industri', 'oleh' => 'Wiktasari', 'kategori' => 'Pengabdian', 'tanggal' => '26 Juli 2026', 'status' => 'Diterima', 'catatan' => null],
        ['judul' => 'Kuliah Tamu', 'oleh' => 'Kuwat Santoso', 'kategori' => 'Pengabdian', 'tanggal' => '10 Juli 2026', 'status' => 'Selesai', 'catatan' => null],
    ];

    $alur = [
        'Diajukan' => ['langkah' => ['a', 'm', 'm', 'm'], 'warna' => 'biru', 'label3' => 'Keputusan'],
        'Direview' => ['langkah' => ['d', 'a', 'm', 'm'], 'warna' => 'biru', 'label3' => 'Keputusan'],
        'Direvisi' => ['langkah' => ['d', 'd', 'a', 'm'], 'warna' => 'oranye', 'label3' => 'Direvisi'],
        'Ditolak' => ['langkah' => ['d', 'd', 'a', 'm'], 'warna' => 'merah', 'label3' => 'Ditolak'],
        'Diterima' => ['langkah' => ['d', 'd', 'h', 'm'], 'warna' => 'hijau', 'label3' => 'Diterima'],
        'Selesai' => ['langkah' => ['d', 'd', 'd', 'h'], 'warna' => 'hijau', 'label3' => 'Diterima'],
    ];

    $daftar = array_map(function ($pengajuan, $index) use ($alur) {
        $status = $alur[$pengajuan['status']];
        $pengajuan['id'] = $index + 1;
        $pengajuan['langkah'] = $status['langkah'];
        $pengajuan['warna'] = $status['warna'];
        $pengajuan['label3'] = $status['label3'];

        return $pengajuan;
    }, $daftar, array_keys($daftar));
@endphp

@section('content')
    <div class="flex flex-col gap-1.5">
        <h1 class="text-3xl font-bold tracking-tight">Pengajuan Dosen</h1>
        <p class="text-2xl text-gray-400">Dashboard / Pengajuan / Dosen</p>
    </div>

    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm"
             x-data="pengajuanDosen(@js($daftar))">
        <div role="tablist" aria-label="Status pengajuan"
             class="flex gap-x-2 overflow-x-auto border-b border-gray-200 pb-px [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <template x-for="t in tab" :key="t">
                <button type="button" role="tab" :aria-selected="aktif === t" x-on:click="aktif = t"
                        :class="aktif === t ? 'border-brand text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-700'"
                        class="-mb-px flex shrink-0 cursor-pointer items-center gap-2 border-b-2 px-3 py-2.5 text-sm">
                    <span x-text="t"></span>
                    <span x-show="aktif === t" x-cloak
                          class="flex h-5 min-w-5 items-center justify-center rounded-full bg-brand px-1.5 text-xs text-white"
                          x-text="jumlah(t)"></span>
                </button>
            </template>
        </div>

        <div class="relative">
            <label for="q" class="sr-only">Cari kegiatan atau nama dosen</label>
            <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                 stroke-linecap="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/>
            </svg>
            <input id="q" type="search" x-model="cari" placeholder="Cari kegiatan atau nama dosen..."
                   class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-3 pl-10 text-[15px] placeholder:text-gray-400">
        </div>

        <div class="flex flex-col gap-5">
            <template x-for="k in tampil" :key="k.id">
                <article class="flex flex-col gap-5 rounded-lg border border-gray-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 flex-col gap-2 text-[15px]">
                            <h2 class="text-lg font-semibold" x-text="k.judul"></h2>
                            <p>Diajukan oleh <span class="font-semibold" x-text="k.oleh"></span></p>
                            <p><span x-text="k.kategori"></span> &middot; Diajukan <span x-text="k.tanggal"></span></p>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            <span :class="warnaBadge[k.status]" class="rounded-md px-2 py-0.5 text-xs"
                                  x-text="k.status"></span>
                            <a :href="`{{ url('/pengajuan/dosen') }}/${k.id}`"
                               :aria-label="`Tinjau ${k.judul}`"
                               class="flex h-9 items-center rounded-md bg-brand px-4 text-base text-white hover:bg-blue-700">
                                Tinjau
                            </a>
                        </div>
                    </div>

                    <ol class="flex" aria-label="Tahapan pengajuan">
                        <template x-for="(s, n) in k.langkah" :key="n">
                            <li class="flex flex-col items-start gap-2" :class="n === 3 ? 'shrink-0' : 'flex-1'">
                                <div class="flex w-full items-center">
                                    <span :class="lingkaran(k, n)"
                                          class="flex size-8 shrink-0 items-center justify-center rounded-full border-2 text-xs font-medium">
                                        <template x-if="s === 'd' || s === 'h'">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                                 stroke-linejoin="round" aria-hidden="true">
                                                <path d="M5 12.5l4.5 4.5L19 7.5"/>
                                            </svg>
                                        </template>
                                        <template x-if="s === 'a' || s === 'm'">
                                            <span x-text="'0' + (n + 1)"></span>
                                        </template>
                                    </span>
                                    <span x-show="n < 3" class="mx-2 h-px flex-1 bg-gray-400"
                                          aria-hidden="true"></span>
                                </div>
                                <span :class="teksLangkah(k, n)" class="text-xs font-medium"
                                      x-text="namaLangkah(k, n)"></span>
                            </li>
                        </template>
                    </ol>

                    <template x-if="k.catatan">
                        <div :class="k.status === 'Ditolak' ? 'bg-red-400' : 'bg-orange-300'"
                             class="rounded-md px-3 py-2.5 text-gray-900">
                            <p class="text-xs font-semibold">Catatan reviewer</p>
                            <p class="text-xs" x-text="k.catatan"></p>
                        </div>
                    </template>
                </article>
            </template>

            <p x-show="tampil.length === 0" x-cloak class="px-4 py-10 text-center text-sm text-gray-500">
                Tidak ada pengajuan yang cocok dengan filter ini.
            </p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pengajuanDosen', (daftar) => ({
                daftar,
                tab: ['Semua', 'Diajukan', 'Direview', 'Direvisi', 'Ditolak', 'Diterima', 'Selesai'],
                aktif: 'Semua',
                cari: '',
                warnaBadge: {
                    Diajukan: 'border border-blue-200 bg-blue-100 text-blue-700',
                    Direview: 'border border-blue-200 bg-blue-100 text-blue-700',
                    Direvisi: 'border border-orange-200 bg-orange-100 text-orange-700',
                    Ditolak: 'border border-red-200 bg-red-100 text-red-600',
                    Diterima: 'border border-green-200 bg-green-100 text-green-700',
                    Selesai: 'border border-green-200 bg-green-100 text-green-700',
                },
                jumlah(status) {
                    return status === 'Semua'
                        ? this.daftar.length
                        : this.daftar.filter((pengajuan) => pengajuan.status === status).length;
                },
                get tampil() {
                    const query = this.cari.trim().toLowerCase();

                    return this.daftar.filter((pengajuan) =>
                        (this.aktif === 'Semua' || pengajuan.status === this.aktif)
                        && (query === ''
                            || pengajuan.judul.toLowerCase().includes(query)
                            || pengajuan.oleh.toLowerCase().includes(query))
                    );
                },
                warnaAktif(pengajuan, langkah) {
                    if (langkah === 2 && pengajuan.langkah[2] === 'a') {
                        return { oranye: 'orange', merah: 'red' }[pengajuan.warna] || 'blue';
                    }

                    return 'blue';
                },
                lingkaran(pengajuan, langkah) {
                    const status = pengajuan.langkah[langkah];
                    if (status === 'd') return 'border-brand bg-brand text-white';
                    if (status === 'h') return 'border-lime-500 bg-lime-500 text-white';
                    if (status === 'a') {
                        const warna = this.warnaAktif(pengajuan, langkah);
                        return warna === 'orange' ? 'border-orange-400 bg-white text-orange-400'
                            : warna === 'red' ? 'border-red-500 bg-white text-red-500'
                            : 'border-brand bg-white text-brand';
                    }

                    return 'border-gray-400 bg-white text-gray-700';
                },
                teksLangkah(pengajuan, langkah) {
                    const status = pengajuan.langkah[langkah];
                    if (status === 'm') return 'text-gray-700';
                    if (status === 'h') return 'text-lime-600';
                    if (status === 'a') {
                        const warna = this.warnaAktif(pengajuan, langkah);
                        return warna === 'orange' ? 'text-orange-400'
                            : warna === 'red' ? 'text-red-500'
                            : 'text-brand';
                    }

                    return 'text-brand';
                },
                namaLangkah(pengajuan, langkah) {
                    return ['Diajukan', 'Direview', pengajuan.label3, 'Selesai'][langkah];
                },
            }));
        });
    </script>
@endpush
