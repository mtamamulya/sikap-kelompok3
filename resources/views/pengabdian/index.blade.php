@extends('layouts.app')

@section('title', 'Pengabdian')

@section('content')

@php
    // DATA TIRUAN. Nanti diganti data dari controller / database oleh tim backend.
    $pengabdian = [
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Seminar Nasional AI',  'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2026],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Pelatihan Industri',   'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2026],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Kunjungan Industri',   'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2026],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Pelatihan Industri',   'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2025],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Kuliah Tamu',          'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2025],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Seminar Nasional AI',  'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2025],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Pelatihan Industri',   'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2025],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Kunjungan Industri',   'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2024],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Pelatihan Industri',   'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2024],
        ['nomor_sk' => '39/SK-REK/SP/1/2022', 'judul' => 'Kuliah Tamu',          'kategori' => 'Pengabdian Masyarakat', 'tahun' => 2024],
    ];

    $daftarTahun = [2026, 2025, 2024, 2023, 2022];

    // Pagination tiruan: halaman aktif 1 dari 10.
    $halaman = 1;
    $nomor   = [1, 2, '...', 9, 10];

    $selectClass = 'h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-9 pl-3.5 text-[15px] text-gray-900';
    $pageBtn     = 'flex size-8 items-center justify-center rounded border text-sm font-semibold';
@endphp

    {{-- Judul halaman + tombol tambah --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-4xl font-bold tracking-tight">Pengabdian</h1>
            <p class="text-2xl text-gray-400">Dashboard / Pengabdian</p>
        </div>
        <a href="{{ route('pengabdian.create') }}" class="mt-2 flex h-11 items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pengabdian
        </a>
    </div>

    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm">

        {{-- Filter. Pakai method GET supaya backend nanti tinggal membaca q dan tahun. --}}
        <form method="GET" action="#" class="flex flex-wrap items-center gap-4">
            <div class="relative min-w-60 flex-[1_1_320px]">
                <label for="q" class="sr-only">Cari judul pengabdian</label>
                <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/>
                </svg>
                <input id="q" name="q" type="search" placeholder="Cari judul pengabdian..."
                       class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-3 pl-10 text-[15px] placeholder:text-gray-400">
            </div>

            <div class="relative w-46">
                <label for="tahun" class="sr-only">Filter tahun</label>
                <select id="tahun" name="tahun" class="{{ $selectClass }}">
                    <option value="">Semua Tahun</option>
                    @foreach ($daftarTahun as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
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
                        <th scope="col" class="w-1/5 border-b border-gray-300 px-2 py-3 font-medium">Nomor SK</th>
                        <th scope="col" class="w-1/5 border-b border-gray-300 px-2 py-3 font-medium">Judul Kegiatan</th>
                        <th scope="col" class="w-1/5 border-b border-gray-300 px-2 py-3 font-medium">Kategori Kegiatan</th>
                        <th scope="col" class="w-1/5 border-b border-gray-300 px-2 py-3 font-medium">Tahun Pelaksanaan</th>
                        <th scope="col" class="w-1/5 border-b border-gray-300 px-2 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengabdian as $p)
                        <tr class="border-b border-gray-300 last:border-b-0 hover:bg-gray-50">
                            <td class="truncate px-2 py-3">{{ $p['nomor_sk'] }}</td>
                            <td class="truncate px-2 py-3" title="{{ $p['judul'] }}">{{ $p['judul'] }}</td>
                            <td class="truncate px-2 py-3">{{ $p['kategori'] }}</td>
                            <td class="px-2 py-3">{{ $p['tahun'] }}</td>
                            <td class="px-2 py-2">
                                <div class="flex items-center gap-3">
                                    {{-- nomor baris dipakai sebagai id tiruan; nanti diganti $p->id --}}
                                    <a href="{{ route('pengabdian.show', $loop->iteration) }}" aria-label="Lihat detail {{ $p['judul'] }}"
                                       class="flex size-8 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('pengabdian.edit', $loop->iteration) }}" aria-label="Edit {{ $p['judul'] }}"
                                       class="flex size-8 items-center justify-center rounded-lg bg-orange-100 text-orange-400 hover:bg-orange-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                    </a>
                                    {{-- Hapus: nanti jadi <form method="POST"> dengan @csrf dan @method('DELETE') --}}
                                    <button type="button" aria-label="Hapus {{ $p['judul'] }}"
                                            onclick="confirm('Hapus pengabdian ini?')"
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

        {{-- Pagination (tampilan saja; nanti diganti $pengabdian->links() dari Laravel) --}}
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