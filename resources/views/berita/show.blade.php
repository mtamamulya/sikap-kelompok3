@extends('layouts.app')

@section('title', 'Detail Berita')

@php
    // DATA TIRUAN dari berita/data.php. $id berasal dari alamat /pengajuan/berita/{id}.
    // Urutannya sama dengan daftar di halaman Pengajuan Berita, jadi tombol mata di baris ke-2 membuka contoh "Direvisi", dst.
    // Nanti diganti data berita dari controller / database oleh tim backend.
    $semua   = require resource_path('views/berita/data.php');
    $b       = $semua[max(1, min(count($semua), (int) ($id ?? 2))) - 1];
    $status  = $b['status'];
    $catatan = $b['catatan'];   // catatan revisi / alasan penolakan
    $admin   = 'Admin';

    // Status => keadaan 4 langkah (Diajukan, Direview, Keputusan, Dipublikasikan).
    // d = sudah lewat (centang biru), a = sedang berjalan, m = belum, h = selesai hijau
    $alur = [
        'Diajukan'       => ['langkah' => ['a', 'm', 'm', 'm'], 'warna' => 'biru',   'label3' => 'Keputusan'],
        'Direvisi'       => ['langkah' => ['d', 'd', 'a', 'm'], 'warna' => 'oranye', 'label3' => 'Direvisi'],
        'Ditolak'        => ['langkah' => ['d', 'd', 'a', 'm'], 'warna' => 'merah',  'label3' => 'Ditolak'],
        'Dipublikasikan' => ['langkah' => ['d', 'd', 'd', 'h'], 'warna' => 'hijau',  'label3' => 'Disetujui'],
    ];
    $a = $alur[$status] ?? null;   // Draft belum punya alur

    // Warna lingkaran & teks tiap langkah
    $lingkaran = function (string $s, string $warna, bool $ketiga) {
        if ($s === 'd') return 'border-brand bg-brand text-white';
        if ($s === 'h') return 'border-lime-500 bg-lime-500 text-white';
        if ($s === 'a') {
            if ($ketiga && $warna === 'oranye') return 'border-orange-400 bg-white text-orange-500';
            if ($ketiga && $warna === 'merah')  return 'border-red-500 bg-white text-red-500';
            return 'border-brand bg-white text-brand';
        }
        return 'border-gray-400 bg-white text-gray-700';
    };
    $teksLangkah = function (string $s, string $warna, bool $ketiga) {
        if ($s === 'm') return 'text-gray-700';
        if ($s === 'h') return 'text-lime-600';
        if ($s === 'a') {
            if ($ketiga && $warna === 'oranye') return 'text-orange-600';
            if ($ketiga && $warna === 'merah')  return 'text-red-600';
            return 'text-brand';
        }
        return 'text-brand';
    };
    $namaLangkah = $a ? ['Diajukan', 'Direview admin', $a['label3'], 'Dipublikasikan'] : [];

    // Riwayat status, dari yang paling awal. tipe: biru | oranye | merah | hijau
    $riwayat = [];
    if ($status !== 'Draft') {
        $riwayat[] = ['judul' => 'Diajukan oleh Anda', 'tipe' => 'biru', 'waktu' => '22 Oktober 2026 · 09.05', 'catatan' => null];
        if ($status !== 'Diajukan') {
            $riwayat[] = ['judul' => 'Direview admin', 'tipe' => 'biru', 'waktu' => '23 Oktober 2026 · 14.30', 'catatan' => null];
            $riwayat[] = [
                'judul'   => ['Direvisi' => 'Diminta revisi', 'Ditolak' => 'Ditolak', 'Dipublikasikan' => 'Dipublikasikan'][$status],
                'tipe'    => ['Direvisi' => 'oranye', 'Ditolak' => 'merah', 'Dipublikasikan' => 'hijau'][$status],
                'waktu'   => '25 Oktober 2026 · 10.12',
                'catatan' => $catatan,
            ];
        }
        $riwayat = array_reverse($riwayat);   // terbaru di atas
    }
    $titik = ['biru' => 'bg-brand', 'oranye' => 'bg-orange-400', 'merah' => 'bg-red-500', 'hijau' => 'bg-lime-500'];

    $badge = [
        'Draft'          => 'border-gray-300 bg-gray-100 text-gray-700',
        'Diajukan'       => 'border-blue-200 bg-blue-100 text-blue-700',
        'Direvisi'       => 'border-orange-200 bg-orange-100 text-orange-700',
        'Dipublikasikan' => 'border-green-200 bg-green-100 text-green-700',
        'Ditolak'        => 'border-red-200 bg-red-100 text-red-600',
    ];
    $cardClass = 'flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4';
@endphp

@section('content')

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Detail Berita</h1>
            <p class="text-2xl text-gray-400">Dashboard / Pengajuan / Berita / Detail</p>
        </div>
        <a href="{{ route('berita.index') }}"
           class="mt-2 flex h-11 items-center gap-2 rounded-md bg-blue-100 px-4 text-base font-medium text-brand hover:bg-blue-200">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Tahapan pengajuan (tidak tampil untuk Draft karena belum diajukan) --}}
    @if ($a)
        <section class="rounded-lg bg-white p-4.5 shadow-sm" aria-label="Tahapan pengajuan">
            <ol class="flex">
                @foreach ($a['langkah'] as $n => $s)
                    @php $ketiga = $n === 2; @endphp
                    <li class="flex flex-col items-start gap-2 {{ $n === 3 ? 'shrink-0' : 'flex-1' }}">
                        <div class="flex w-full items-center">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full border-2 text-xs font-medium {{ $lingkaran($s, $a['warna'], $ketiga) }}">
                                @if ($s === 'd' || $s === 'h')
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                @else
                                    0{{ $n + 1 }}
                                @endif
                            </span>
                            @if ($n < 3)
                                <span class="mx-2 h-px flex-1 bg-gray-400" aria-hidden="true"></span>
                            @endif
                        </div>
                        <span class="text-xs font-medium {{ $teksLangkah($s, $a['warna'], $ketiga) }}">{{ $namaLangkah[$n] }}</span>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_24rem] lg:items-start">

        {{-- KIRI: ISI BERITA --}}
        <article class="{{ $cardClass }} gap-4 p-5" aria-labelledby="h-berita">
            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-md border px-2 py-0.5 text-xs {{ $badge[$status] }}">{{ $status }}</span>
                <span class="text-[13px] text-gray-600">{{ $b['kategori'] }} &middot; {{ $status === 'Draft' ? 'Dibuat' : 'Diajukan' }} {{ $b['tanggal'] }}</span>
            </div>
            <h2 id="h-berita" class="text-2xl leading-snug font-bold">{{ $b['judul'] }}</h2>

            <div class="flex h-56 items-center justify-center gap-2 rounded-lg bg-gray-200 text-sm text-gray-600">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-8 8"/></svg>
                Foto sampul
            </div>

            <div class="flex flex-col gap-3 text-[15px] leading-relaxed text-gray-700">
                @foreach (preg_split("/\n\n+/", $b['isi']) as $p)
                    <p>{{ $p }}</p>
                @endforeach
            </div>
        </article>

        {{-- KANAN --}}
        <div class="flex flex-col gap-5">

            @if ($status === 'Draft')
                <section class="{{ $cardClass }}" aria-labelledby="h-draft">
                    <h2 id="h-draft" class="text-xl font-semibold">Berita masih draf</h2>
                    <p class="text-[15px] text-gray-600">Berita ini belum diajukan ke admin. Lengkapi lalu ajukan supaya bisa direview.</p>
                    <a href="{{ route('berita.edit', $b['id']) }}"
                       class="flex h-10 w-full items-center justify-center rounded-md bg-brand text-base text-white hover:bg-blue-700">Lanjutkan &amp; ajukan</a>
                </section>
            @endif

            @if ($status === 'Diajukan')
                <div role="status" class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-[15px] text-blue-800">
                    <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 7v5l3 2"/></svg>
                    <div><p class="font-semibold">Menunggu review admin</p><p>Admin akan memutuskan apakah berita ini dipublikasikan. Hasilnya tampil di halaman ini.</p></div>
                </div>
            @endif

            @if ($status === 'Direvisi')
                {{-- Direvisi: catatan revisi admin + tombol untuk memperbaiki dan mengajukan kembali --}}
                <section class="{{ $cardClass }}" aria-labelledby="h-keputusan">
                    <h2 id="h-keputusan" class="text-xl font-semibold">Keputusan Admin</h2>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-md border border-orange-200 bg-orange-100 px-2 py-0.5 text-xs text-orange-700">Perlu direvisi</span>
                        <span class="text-[13px] text-gray-600">25 Oktober 2026</span>
                    </div>
                    <div class="rounded-md bg-orange-300 px-3.5 py-3 text-gray-900">
                        <p class="text-[13px] font-semibold">Catatan reviewer</p>
                        <p class="mt-1 text-sm">{{ $catatan }}</p>
                    </div>
                    <p class="text-[13px] text-gray-600">Direview oleh {{ $admin }}</p>
                    <a href="{{ route('berita.edit', $b['id']) }}"
                       class="flex h-10 w-full items-center justify-center rounded-md bg-brand text-base text-white hover:bg-blue-700">Perbaiki &amp; ajukan kembali</a>
                </section>
            @endif

            @if ($status === 'Ditolak')
                <section class="flex flex-col gap-2 rounded-lg border border-red-200 bg-red-50 p-4" aria-labelledby="h-tolak">
                    <h2 id="h-tolak" class="flex items-center gap-2 text-xl font-semibold text-red-700">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 7.5v5.5M12 16.5v.01"/></svg>
                        Alasan Penolakan
                    </h2>
                    <p class="px-1 text-[15px] text-gray-900">{{ $catatan }}</p>
                </section>
            @endif

            @if ($status === 'Dipublikasikan')
                <section class="flex flex-col gap-2 rounded-lg border border-green-200 bg-green-50 p-4" aria-labelledby="h-terbit">
                    <h2 id="h-terbit" class="flex items-center gap-2 text-xl font-semibold text-green-700">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M8 12.5l3 3 5-6"/></svg>
                        Berita dipublikasikan
                    </h2>
                    <p class="px-1 text-[15px] text-gray-900">Disetujui admin pada 25 Oktober 2026 dan sudah tampil di website.</p>
                </section>
            @endif

            @if ($riwayat)
                <section class="{{ $cardClass }}" aria-labelledby="h-riwayat">
                    <h2 id="h-riwayat" class="text-xl font-semibold">Riwayat Status</h2>
                    <ol class="flex flex-col gap-4 text-[15px]">
                        @foreach ($riwayat as $r)
                            <li class="flex gap-3">
                                <span class="mt-1.5 size-2.5 shrink-0 rounded-full {{ $titik[$r['tipe']] }}" aria-hidden="true"></span>
                                <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-semibold">{{ $r['judul'] }}</h3>
                                        @if ($loop->first)
                                            <span class="rounded-full bg-lime-100 px-2 py-0.5 text-xs text-lime-700">Status saat ini</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-600">{{ $r['waktu'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>
    </div>

@endsection