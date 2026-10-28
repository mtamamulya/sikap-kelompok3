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
    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm">

        <form method="GET" action="#" class="flex flex-wrap gap-4">
            <label class="text-sm">Status kegiatan
                <select name="status" class="ml-2 rounded border border-gray-300 px-3 py-2">
                    <option value="">Semua Status</option>
                    @foreach (['Diajukan', 'Direvisi', 'Disetujui', 'Ditolak'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </label>
        </form>

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
                    @foreach ($kegiatan as $k)
                        <tr class="border-b border-gray-300 last:border-b-0 hover:bg-gray-50">
                            <td class="truncate px-2.5 py-3" title="{{ $k['nama'] }}">{{ $k['nama'] }}</td>
                            <td class="truncate px-2.5 py-3"  >{{ $k['kategori'] }}</td>
                            <td class="truncate px-2.5 py-3"  >{{ $k['jenis'] }}</td>
                            <td class="px-2.5 py-3"  >{{ $k['tanggal'] }}</td>
                            <td class="px-2.5 py-3">
                                <span class="inline-block rounded-md border px-2 py-0.5 text-[13px] {{ match ($k['status']) { 'Diajukan' => 'border-blue-200 bg-blue-100 text-blue-700', 'Direvisi' => 'border-orange-200 bg-orange-100 text-orange-700', 'Disetujui' => 'border-green-200 bg-green-100 text-green-700', default => 'border-red-200 bg-red-100 text-red-600' } }}">{{ $k['status'] }}</span>
                            </td>
                            <td class="px-2.5 py-2">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('kegiatan.show', $k['id']) }}" aria-label="Lihat detail {{ $k['nama'] }}"
                                       class="flex size-8 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('kegiatan.edit', $k['id']) }}" aria-label="Edit {{ $k['nama'] }}"
                                       class="flex size-8 items-center justify-center rounded-lg bg-orange-100 text-orange-400 hover:bg-orange-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
