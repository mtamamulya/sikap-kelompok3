@extends('layouts.app')

@section('title', 'Penelitian')

@section('content')

@php
    // DATA TIRUAN. Nanti diganti data dari controller / database oleh tim backend.
    $penelitian = [
        ['judul' => 'Deteksi hoaks dengan ML',                       'ketua' => 'Angga Wahyu Wibowo', 'tahun' => 2026, 'dana' => 'Hibah Internal', 'anggota' => 3],
        ['judul' => 'Sistem rekomendasi materi pembelajaran berbasis AI', 'ketua' => 'Kuwat Santoso',  'tahun' => 2026, 'dana' => 'Mandiri',        'anggota' => 3],
        ['judul' => 'Analisis sentimen ulasan aplikasi kampus',      'ketua' => 'Wiktasari',          'tahun' => 2025, 'dana' => 'Hibah Nasional', 'anggota' => 3],
        ['judul' => 'Deteksi hoaks dengan ML',                       'ketua' => 'Tahan Prahara',      'tahun' => 2025, 'dana' => 'Hibah Internal', 'anggota' => 3],
        ['judul' => 'Sistem rekomendasi materi pembelajaran berbasis AI', 'ketua' => 'Prayitno',       'tahun' => 2025, 'dana' => 'Mandiri',        'anggota' => 3],
        ['judul' => 'Analisis sentimen ulasan aplikasi kampus',      'ketua' => 'Arif Setyawan',      'tahun' => 2025, 'dana' => 'Hibah Nasional', 'anggota' => 3],
        ['judul' => 'Deteksi hoaks dengan ML',                       'ketua' => 'Kuwat Santoso',      'tahun' => 2024, 'dana' => 'Hibah Internal', 'anggota' => 3],
        ['judul' => 'Sistem rekomendasi materi pembelajaran berbasis AI', 'ketua' => 'Wiktasari',      'tahun' => 2024, 'dana' => 'Mandiri',        'anggota' => 3],
        ['judul' => 'Analisis sentimen ulasan aplikasi kampus',      'ketua' => 'Arif Setyawan',      'tahun' => 2024, 'dana' => 'Hibah Nasional', 'anggota' => 3],
        ['judul' => 'Deteksi hoaks dengan ML',                       'ketua' => 'Tahan Prahara',      'tahun' => 2024, 'dana' => 'Hibah Internal', 'anggota' => 3],
    ];

    $daftarTahun = [2026, 2025, 2024, 2023, 2022];
    $daftarDana  = ['Hibah Internal', 'Hibah Nasional', 'Mandiri'];

    // Pagination tiruan: halaman aktif 1 dari 10.
    $halaman = 1;
    $total   = 10;
    $nomor   = [1, 2, '...', 9, 10];

    $selectClass = 'h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-9 pl-3.5 text-[15px] text-gray-900';
    $pageBtn     = 'flex size-8 items-center justify-center rounded border text-sm font-semibold';
@endphp

    {{-- Judul halaman + tombol tambah --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Penelitian</h1>
            <p class="text-2xl text-gray-400">Dashboard / Penelitian</p>
        </div>
        <a href="{{ route('penelitian.create') }}" class="mt-2 flex h-11 items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Penelitian
        </a>
    </div>

    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm">

        {{-- Filter. Pakai method GET supaya backend nanti tinggal membaca q, tahun, dana. --}}
        <form method="GET" action="#" class="flex flex-wrap items-center gap-4">
            <div class="relative min-w-60 flex-[1_1_320px]">
                <label for="q" class="sr-only">Cari judul penelitian</label>
                <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/>
                </svg>
                <input id="q" name="q" type="search" placeholder="Cari judul penelitian..."
                       class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-3 pl-10 text-[15px] placeholder:text-gray-400">
            </div>

            <div class="relative w-full sm:w-46">
                <label for="tahun" class="sr-only">Filter tahun</label>
                <select id="tahun" name="tahun" class="{{ $selectClass }}">
                    <option value="">Semua Tahun</option>
                    @foreach ($daftarTahun as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </div>

            <div class="relative w-full sm:w-46">
                <label for="dana" class="sr-only">Filter sumber dana</label>
                <select id="dana" name="dana" class="{{ $selectClass }}">
                    <option value="">Sumber Dana</option>
                    @foreach ($daftarDana as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto rounded-md border border-gray-300">
            <table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2 py-3 font-medium">Judul</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2 py-3 font-medium">Ketua</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2 py-3 font-medium">Tahun Pelaksanaan</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2 py-3 font-medium">Sumber Dana</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2 py-3 font-medium">Anggota</th>
                        <th scope="col" class="w-1/6 border-b border-gray-300 px-2 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penelitian as $p)
                        <tr class="border-b border-gray-300 last:border-b-0 hover:bg-gray-50">
                            <td class="truncate px-2 py-3" title="{{ $p['judul'] }}">{{ $p['judul'] }}</td>
                            <td class="truncate px-2 py-3">{{ $p['ketua'] }}</td>
                            <td class="px-2 py-3">{{ $p['tahun'] }}</td>
                            <td class="px-2 py-3">{{ $p['dana'] }}</td>
                            <td class="px-2 py-3">
                                <span class="inline-block rounded-full border border-blue-200 bg-blue-100 px-2.5 py-0.5 text-[13px] text-blue-700">{{ $p['anggota'] }} orang</span>
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex items-center gap-3">
                                    {{-- nomor baris dipakai sebagai id tiruan; nanti diganti $p->id --}}
                                    <a href="{{ route('penelitian.show', $loop->iteration) }}" aria-label="Lihat detail {{ $p['judul'] }}"
                                       class="flex size-8 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('penelitian.edit', $loop->iteration) }}" aria-label="Edit {{ $p['judul'] }}"
                                       class="flex size-8 items-center justify-center rounded-lg bg-orange-100 text-orange-400 hover:bg-orange-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                    </a>
                                    {{-- Hapus: membuka modal konfirmasi (components/modal-hapus). action '#' nanti diganti route hapus dari backend. --}}
                                    <button type="button" aria-label="Hapus {{ $p['judul'] }}"
                                            x-on:click="$dispatch('minta-hapus', { jenis: 'penelitian', nama: @js($p['judul']), action: '#' })"
                                            class="flex size-8 cursor-pointer items-center justify-center rounded-lg bg-red-100 text-red-500 hover:bg-red-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 7h14M10 7V5h4v2M7 7l1 12h8l1-12M10 11v5M14 11v5"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination (tampilan saja; nanti diganti $penelitian->links() dari Laravel) --}}
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

    <x-modal-hapus />

@endsection