@extends('layouts.app')

@section('title', 'Detail Kegiatan')

@section('content')
<div dosen-shell>

@php
    // DATA TIRUAN. $id berasal dari alamat (/kegiatan/{id}).
    // Nanti diganti data kegiatan dari controller / database oleh tim backend.
    // Contoh data. Urutannya sama dengan daftar di halaman Kegiatan (id 1–10), jadi ikon mata di baris ke-2
    // membuka contoh "Direvisi", baris ke-3 contoh "Disetujui", dst. Isi halaman menyesuaikan statusnya.
    $contoh = [
        1  => ['nama' => 'Seminar nasional AI',  'kategori' => 'Lomba',              'jenis' => 'Kegiatan dosen',       'mulai' => '12 September 2026', 'selesai' => '14 September 2026', 'status' => 'Diajukan',  'catatan' => null],
        2  => ['nama' => 'Pelatihan industri',   'kategori' => 'Magang',             'jenis' => 'Kegiatan Prodi',       'mulai' => '20 Agustus 2026',   'selesai' => '22 Agustus 2026',   'status' => 'Direvisi',  'catatan' => 'Lampirkan surat tugas yang sudah ditandatangani dan perjelas jadwal kegiatan.'],
        3  => ['nama' => 'Kunjungan industri',   'kategori' => 'Kunjungan Industri', 'jenis' => 'Prodi dan mahasiswa', 'mulai' => '3 Agustus 2026',    'selesai' => '3 Agustus 2026',    'status' => 'Disetujui', 'catatan' => null],
        4  => ['nama' => 'Lomba karya ilmiah',   'kategori' => 'Lomba',              'jenis' => 'Kegiatan mahasiswa',   'mulai' => '15 Juli 2026',      'selesai' => '16 Juli 2026',      'status' => 'Ditolak',   'catatan' => 'Lomba tidak termasuk daftar kegiatan yang diakui program studi.'],
        5  => ['nama' => 'Computer Vision',      'kategori' => 'Lomba',              'jenis' => 'Kegiatan mahasiswa',   'mulai' => '3 Agustus 2026',    'selesai' => '5 Agustus 2026',    'status' => 'Diajukan',  'catatan' => null],
        6  => ['nama' => 'Sistem Operasi',       'kategori' => 'Kunjungan Industri', 'jenis' => 'Prodi dan mahasiswa', 'mulai' => '3 Agustus 2026',    'selesai' => '3 Agustus 2026',    'status' => 'Direvisi',  'catatan' => 'Daftar anggota belum lengkap. Tambahkan mahasiswa yang ikut.'],
        7  => ['nama' => 'Jaringan Komputer',    'kategori' => 'Lomba',              'jenis' => 'Kegiatan Prodi',       'mulai' => '12 September 2026', 'selesai' => '14 September 2026', 'status' => 'Disetujui', 'catatan' => null],
        8  => ['nama' => 'Sistem Basis Data',    'kategori' => 'Lomba',              'jenis' => 'Kegiatan dosen',       'mulai' => '12 September 2026', 'selesai' => '14 September 2026', 'status' => 'Ditolak',   'catatan' => 'Jadwal bentrok dengan kegiatan fakultas pada tanggal yang sama.'],
        9  => ['nama' => 'Sistem Operasi',       'kategori' => 'Kunjungan Industri', 'jenis' => 'Prodi dan mahasiswa', 'mulai' => '20 Agustus 2026',   'selesai' => '20 Agustus 2026',   'status' => 'Diajukan',  'catatan' => null],
        10 => ['nama' => 'Internet of Things',   'kategori' => 'Magang',             'jenis' => 'Prodi dan mahasiswa',  'mulai' => '3 Agustus 2026',    'selesai' => '30 September 2026', 'status' => 'Direvisi',  'catatan' => 'Dokumen proposal belum memuat rencana anggaran.'],
    ];
    $c = $contoh[max(1, min(10, (int) ($id ?? 1)))];
    $k = [
        'kategori'      => $c['kategori'],
        'jenis'         => $c['jenis'],
        'nama'          => $c['nama'],
        'penyelenggara' => 'Politeknik Negeri Semarang',
        'tahun'         => 2026,
        'mulai'         => $c['mulai'],
        'selesai'       => $c['selesai'],
        'status'        => $c['status'],
    ];
    $catatan = $c['catatan'];   // catatan revisi / alasan penolakan dari reviewer

    $warna = [
        'Diajukan'  => 'border-blue-200 bg-blue-100 text-blue-700',
        'Direvisi'  => 'border-orange-200 bg-orange-100 text-orange-700',
        'Disetujui' => 'border-green-200 bg-green-100 text-green-700',
        'Ditolak'   => 'border-red-200 bg-red-100 text-red-600',
    ];

    $anggota = [
        ['nama' => 'Kuwat Santoso , S.T., M.Kom.', 'status' => 'Dosen'],
        ['nama' => 'Mukhlis Pratama Mulya',        'status' => 'Mahasiswa'],
    ];

    // Dokumen. 'tautan' = alamat dokumen di cloud (Google Drive dll.), dibuka di tab baru.
    $dokumen = [
        [
            'nama'       => 'Proposal Kegiatan',
            'jenis'      => 'Proposal',
            'keterangan' => 'Ini adalah proposal kegiatan seminar',
            'tautan'     => 'https://drive.google.com/drive/folders/contoh',
        ],
    ];

    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
@endphp

    {{-- Judul halaman + tombol kembali --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Detail Kegiatan</h1>
            <p class="text-2xl text-gray-400">Dashboard / Kegiatan / Detail Kegiatan</p>
        </div>
        <div class="mt-2 flex items-center gap-3">
            @if ($k['status'] === 'Direvisi')
                <a href="{{ route('kegiatan.edit', $id ?? 1) }}"
                   class="flex h-11 items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                    Perbaiki kegiatan
                </a>
            @endif
            <a href="{{ route('kegiatan.index') }}"
               class="flex h-11 items-center gap-2 rounded-md bg-blue-100 px-4 text-base font-medium text-brand hover:bg-blue-200">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Halaman ini hanya untuk membaca, jadi tidak ada formulir atau tombol simpan --}}
    <div class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">

        {{-- Pesan sesuai status pengajuan --}}
        @if ($k['status'] === 'Diajukan')
            <div role="status" class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-[15px] text-blue-800">
                <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 7v5l3 2"/></svg>
                <div><p class="font-semibold">Menunggu verifikasi</p><p>Pengajuan sudah terkirim dan sedang menunggu ditinjau.</p></div>
            </div>
        @elseif ($k['status'] === 'Direvisi')
            <div role="status" class="flex items-start gap-3 rounded-lg border border-orange-200 bg-orange-50 p-4 text-[15px] text-orange-900">
                <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                <div class="flex flex-col gap-1">
                    <p class="font-semibold">Perlu direvisi</p>
                    <p><span class="font-medium">Catatan revisi:</span> {{ $catatan }}</p>
                    <p class="text-orange-800">Perbaiki data atau dokumen lewat tombol "Perbaiki kegiatan", lalu ajukan kembali.</p>
                </div>
            </div>
        @elseif ($k['status'] === 'Disetujui')
            <div role="status" class="flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-[15px] text-green-800">
                <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M8 12.5l3 3 5-6"/></svg>
                <div><p class="font-semibold">Kegiatan disetujui</p><p>Pengajuan sudah diverifikasi dan kegiatan ini tercatat.</p></div>
            </div>
        @elseif ($k['status'] === 'Ditolak')
            <div role="status" class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-[15px] text-red-800">
                <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 7.5v5.5M12 16.5v.01"/></svg>
                <div class="flex flex-col gap-1"><p class="font-semibold">Pengajuan ditolak</p><p><span class="font-medium">Alasan penolakan:</span> {{ $catatan }}</p></div>
            </div>
        @endif

        {{-- 1. INFORMASI KEGIATAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-info">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <h2 id="h-info" class="text-[15px] font-semibold">Informasi Kegiatan</h2>
            </div>

            <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <x-detail-field label="Kategori" :value="$k['kategori']" icon="chevron" />
                <x-detail-field label="Jenis kegiatan" :value="$k['jenis']" icon="chevron" />
                <div class="md:col-span-2"><x-detail-field label="Nama kegiatan" :value="$k['nama']" /></div>
                <x-detail-field label="Penyelenggara" :value="$k['penyelenggara']" />
                <x-detail-field label="Tahun penyelenggaraan" :value="$k['tahun']" icon="chevron" />
                <x-detail-field label="Tanggal mulai" :value="$k['mulai']" icon="kalender" />
                <x-detail-field label="Tanggal selesai" :value="$k['selesai']" icon="kalender" />

                <div class="flex flex-col gap-2 md:col-span-2">
                    <dt class="text-[15px] font-medium">Status pengajuan</dt>
                    <dd><span class="inline-block rounded-md border px-2 py-0.5 text-[13px] {{ $warna[$k['status']] }}">{{ $k['status'] }}</span></dd>
                </div>
            </dl>
        </section>

        {{-- 2. ANGGOTA KEGIATAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-anggota">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">2</span>
                <h2 id="h-anggota" class="text-[15px] font-semibold">Anggota Kegiatan</h2>
            </div>

            <div class="overflow-x-auto rounded-md border border-gray-300">
                <table class="w-full min-w-[400px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th scope="col" class="w-1/2 px-2.5 py-2.5 font-medium">Nama Anggota</th>
                            <th scope="col" class="w-1/2 px-2.5 py-2.5 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($anggota as $a)
                            <tr class="border-t border-gray-300">
                                <td class="px-2.5 py-2.5">{{ $a['nama'] }}</td>
                                <td class="px-2.5 py-2.5">{{ $a['status'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 3. DOKUMEN PENDUKUNG
             Di halaman Detail tidak ada kotak unggah. Dokumen ditampilkan sebagai tautan yang dibuka di tab baru (tidak ada unggah file).
             Dokumen ditambah / diganti di halaman Edit. --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-dokumen">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">3</span>
                <h2 id="h-dokumen" class="text-[15px] font-semibold">Dokumen Pendukung</h2>
            </div>

            @forelse ($dokumen as $d)
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="bg-[#02215c] px-3.5 py-3 text-white">
                        <h3 class="text-sm font-semibold">Dokumen {{ $loop->iteration }}</h3>
                    </div>

                    <div class="flex flex-col gap-4 p-3.5">
                        {{-- File: nama file + tombol Lihat dan Unduh --}}
                        <div class="flex flex-col gap-2">
                            <span class="text-[15px] font-medium">Tautan dokumen</span>
                            @if (! empty($d['tautan']))
                                <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-brand">
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1 1M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1-1"/></svg>
                                        </span>
                                        <span class="truncate text-[15px]" title="{{ $d['tautan'] }}">{{ $d['tautan'] }}</span>
                                    </div>
                                    <a href="{{ $d['tautan'] }}" target="_blank" rel="noopener noreferrer"
                                       class="flex h-9 items-center gap-1.5 rounded-lg bg-brand px-3.5 text-sm font-medium text-white hover:bg-blue-700">
                                        Buka dokumen
                                        <svg class="size-[16px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4h6v6M20 4l-9 9M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"/></svg>
                                        <span class="sr-only">(dibuka di tab baru)</span>
                                    </a>
                                </div>
                            @else
                                <p class="rounded-lg border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500">Belum ada tautan dokumen.</p>
                            @endif
                        </div>

                        <dl class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                            <x-detail-field label="Nama dokumen" :value="$d['nama']" />
                            <x-detail-field label="Jenis dokumen" :value="$d['jenis']" icon="chevron" />
                            <!-- <div class="md:col-span-2"><x-detail-field label="Keterangan" :value="$d['keterangan']" multiline /></div> -->
                        </dl>
                    </div>
                </div>
            @empty
                <p class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">Belum ada dokumen pendukung.</p>
            @endforelse
        </section>
    </div>

@endsection