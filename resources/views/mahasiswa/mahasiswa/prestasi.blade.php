@extends('layouts.mahasiswa-tim')

@section('title', 'Prestasi mahasiswa')

@section('content')
@php
    // ===== Data contoh. Ganti dengan data dari controller saat database sudah ada. =====
    // Urutan: nama, kategori, bentuk, capaian, tingkat, jenis, penyelenggara, tanggal, kegiatan terkait (opsional)
    $rows = [
        ['Lomba Inovasi Internet of Things', 'Teknologi', 'Tim', 'Juara 2', 'Nasional', 'Akademik', 'Forum Inovasi Teknologi', '2026-09-24', 'Lomba inovasi IoT 2026'],
        ['Kompetisi Desain Poster Digital', 'Desain', 'Individu', 'Juara 1', 'Kabupaten/Kota', 'Nonakademik', 'BEM Politeknik', '2026-08-18'],
        ['Hackathon Smart City Nusantara', 'Teknologi', 'Tim', 'Juara 1', 'Nasional', 'Akademik', 'Kementerian Kominfo', '2026-07-12', 'Hackathon Smart City 2026'],
        ['Capture The Flag Keamanan Siber', 'Keamanan siber', 'Tim', 'Juara 3', 'Provinsi', 'Akademik', 'Komunitas Siber Jateng', '2026-06-05'],
        ['Kejuaraan Bulu Tangkis Antar Kampus', 'Olahraga', 'Individu', 'Juara 2', 'Kabupaten/Kota', 'Nonakademik', 'KONI Kota Semarang', '2026-05-20'],
        ['Kontes Robot Indonesia Regional', 'Robotika', 'Tim', 'Juara 1', 'Provinsi', 'Akademik', 'Puslapdik Kemdikbud', '2026-04-14', 'Persiapan KRI 2026'],
        ['Lomba Fotografi Budaya Jawa', 'Seni', 'Individu', 'Harapan 1', 'Provinsi', 'Nonakademik', 'Dinas Kebudayaan Jateng', '2026-03-08'],
        ['Kompetisi Pemrograman Mobile', 'Pemrograman', 'Individu', 'Juara 3', 'Nasional', 'Akademik', 'Himpunan Mahasiswa Informatika', '2026-02-22'],
        ['International Embedded Systems Contest', 'Sistem embedded', 'Tim', 'Finalis', 'Internasional', 'Akademik', 'IEEE Student Branch', '2026-01-30'],
        ['Festival Film Pendek Mahasiswa', 'Film', 'Tim', 'Juara 2', 'Provinsi', 'Nonakademik', 'Dewan Kesenian Jawa Tengah', '2025-11-16'],
        ['Lomba UI/UX Design Challenge', 'Desain', 'Individu', 'Juara 1', 'Nasional', 'Akademik', 'Ikatan Desainer Digital', '2025-10-04'],
        ['Kompetisi Debat Bahasa Inggris', 'Debat', 'Tim', 'Juara 3', 'Kabupaten/Kota', 'Nonakademik', 'English Club Politeknik', '2025-09-13'],
        ['Gemastik Divisi Pengembangan Aplikasi', 'Pemrograman', 'Tim', 'Finalis', 'Nasional', 'Akademik', 'Puspresnas', '2025-08-27', 'Gemastik 2025'],
        ['Lomba Karya Tulis Ilmiah', 'Penulisan ilmiah', 'Individu', 'Juara 2', 'Provinsi', 'Akademik', 'Universitas Diponegoro', '2025-07-19'],
        ['Turnamen Esports Mobile Legends', 'Esports', 'Tim', 'Juara 1', 'Kabupaten/Kota', 'Nonakademik', 'Dinas Pemuda dan Olahraga', '2025-06-07'],
        ['Asian IoT Innovation Challenge', 'Internet of Things', 'Tim', 'Harapan 2', 'Internasional', 'Akademik', 'Asia Tech Forum', '2025-05-25'],
        ['Lomba Cipta Lagu Mahasiswa', 'Musik', 'Individu', 'Juara 2', 'Nasional', 'Nonakademik', 'Radio Kampus Indonesia', '2025-04-12'],
        ['Kompetisi Jaringan Komputer', 'Jaringan', 'Tim', 'Juara 1', 'Provinsi', 'Akademik', 'Cisco Networking Academy', '2025-03-15'],
        ['Pekan Olahraga Mahasiswa Futsal', 'Olahraga', 'Tim', 'Juara 3', 'Provinsi', 'Nonakademik', 'Badan Olahraga Mahasiswa', '2025-02-09'],
        ['Business Plan Competition', 'Kewirausahaan', 'Tim', 'Juara 2', 'Nasional', 'Akademik', 'Kemenparekraf', '2024-11-23'],
        ['Lomba Ilustrasi Digital', 'Seni', 'Individu', 'Juara 1', 'Kabupaten/Kota', 'Nonakademik', 'Komunitas Ilustrator Semarang', '2024-10-06'],
        ['Kompetisi Data Science', 'Data science', 'Tim', 'Finalis', 'Internasional', 'Akademik', 'Kaggle Student Community', '2024-08-17'],
        ['Olimpiade Matematika Terapan', 'Matematika', 'Individu', 'Harapan 1', 'Provinsi', 'Akademik', 'Forum Matematika Jateng', '2024-05-18'],
        ['Lomba Desain Logo Kampus', 'Desain', 'Individu', 'Juara 3', 'Kabupaten/Kota', 'Nonakademik', 'Humas Politeknik', '2023-11-11'],
        ['Kontes Programming Kompetitif', 'Algoritma', 'Individu', 'Juara 2', 'Nasional', 'Akademik', 'ICPC Indonesia', '2023-09-02'],
        ['Lomba Cerdas Cermat Teknologi', 'Teknologi', 'Tim', 'Juara 1', 'Kabupaten/Kota', 'Akademik', 'Dinas Pendidikan', '2023-03-26'],
    ];

    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    $namaMhs = $nama ?? config('mahasiswa.nama');

    // Prestasi tim yang dibuat orang lain, di mana mahasiswa ini hanya DITAG sebagai anggota.
    // Untuk prestasi ini hanya tombol "lihat" yang tersedia (tanpa edit dan hapus).
    // Data asli: bandingkan pembuat prestasi dengan auth()->id(), atau cek mahasiswa sebagai anggota.
    $ditagIds = [3, 9, 13, 16];

    $prestasi = collect($rows)->map(function ($r, $i) use ($bulan, $ditagIds, $namaMhs) {
        $tgl   = \Illuminate\Support\Carbon::parse($r[7]);
        $ditag = in_array($i + 1, $ditagIds, true);

        return [
            'id' => $i + 1,
            'nama' => $r[0],
            'kategori' => $r[1],
            'bentuk' => $r[2],
            'capaian' => $r[3],
            'tingkat' => $r[4],
            'jenis' => $r[5],
            'penyelenggara' => $r[6],
            'tanggal' => $tgl->format('d') . ' ' . $bulan[$tgl->month - 1] . ' ' . $tgl->year,
            'tahun' => $tgl->year,
            'kegiatan' => $r[8] ?? 'Tidak terkait',
            'bukti' => $r[9] ?? [],
            'ditag' => $ditag,
            'pembuat' => $ditag ? 'Ivan Sigit Santoso' : $namaMhs,
            'bisaUbah' => ! $ditag, // hanya pembuat yang boleh edit dan hapus
        ];
    })->all();

    $total = count($prestasi);
    $akademik = collect($prestasi)->where('jenis', 'Akademik')->count();
    $nonakademik = collect($prestasi)->where('jenis', 'Nonakademik')->count();

    // URL aksi memakai route mahasiswa.prestasi.* yang terdaftar di routes/web.php.
    $urlEdit  = Route::has('mahasiswa.prestasi.edit')  ? route('mahasiswa.prestasi.edit', '__ID__')  : null;
    $urlHapus = Route::has('mahasiswa.prestasi.hapus') ? route('mahasiswa.prestasi.hapus', '__ID__') : null;

    // Ikon inline (Heroicons outline) supaya tidak perlu paket tambahan.
    $paths = [
        'plus' => 'M12 4.5v15m7.5-7.5h-15',
        'search' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
        'down' => 'm19.5 8.25-7.5 7.5-7.5-7.5',
        'link' => 'M13.19 8.53 9.84 11.88a3 3 0 0 0 4.24 4.24l3.54-3.54a5 5 0 0 0-7.07-7.07L8.52 7.54m2.29 7.93 3.35-3.35a3 3 0 0 0-4.24-4.24l-3.54 3.54a5 5 0 0 0 7.07 7.07l2.03-2.03',
        'info' => 'm11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z',
        'book' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
        'laptop' => 'M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25',
        'award' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
        'close' => 'M6 18 18 6M6 6l12 12',
        'external' => 'M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25',
        'eye' => 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
        'pencil' => 'm16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487Zm0 0L19.5 7.125',
        'trash' => 'm14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0',
    ];
    $icon = fn (string $n, string $c = 'size-5') => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="' . $c . '" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="' . $paths[$n] . '"/></svg>';

    $filter = 'h-10 w-full rounded-lg border border-[#E5E7EB] bg-white text-sm text-[#0A0A0A] outline-none transition-colors hover:border-[#C7D2E5] focus:border-[#2277FF] focus:ring-2 focus:ring-[#2277FF]/15';
    $th = 'px-4 text-left text-xs font-semibold text-[#6B7280]';
    $td = 'px-4 py-3 align-middle text-[13px] text-[#4B5563]';
    $pageBtn = 'rounded-lg border border-[#E5E7EB] bg-white px-3 py-1.5 text-xs font-semibold text-[#0062FF] transition hover:bg-[#EFF6FF] disabled:cursor-not-allowed disabled:text-[#A8A8A8] disabled:hover:bg-white';

    // Tombol aksi di tabel: kotak 32px, warna lembut (biru = lihat, oranye = edit, merah = hapus)
    $aksiBtn = 'flex h-8 shrink-0 items-center justify-center rounded-lg transition focus-visible:outline-2 focus-visible:outline-offset-2';

    // Teks panjang dipotong maksimal 2 baris supaya tinggi baris dan lebar kolom tidak bergeser
    $clamp = 'line-clamp-2 break-words';

    // Gaya isi kartu ringkasan: sama dengan dashboard mahasiswa
    $angkaCls  = 'text-3xl font-bold leading-none';
    $satuanCls = 'text-xs text-[#6B7280]';
@endphp

{{-- x-data dipasang di pembungkus supaya kartu ringkasan ikut berubah saat prestasi dihapus --}}
<div class="flex flex-col gap-6" x-data="prestasiList()">

    {{-- Pesan sukses setelah menyimpan prestasi --}}
    @if (session('status'))
        <div class="rounded-xl border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm font-medium text-[#4C7D08]">{{ session('status') }}</div>
    @endif

    {{-- Judul --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px] text-[#111827]">Prestasi Mahasiswa</h1>
            <p class="text-sm text-[#6B7280]">Kelola catatan pencapaian akademik dan nonakademik Anda.</p>
        </div>
        <a href="{{ route('mahasiswa.prestasi.tambah') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#2277FF] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#1A63D9]">
            {!! $icon('plus') !!}
            Tambah prestasi
        </a>
    </div>

    {{-- Kartu ringkasan: komponen x-mahasiswa.stat-card; angka mengikuti daftar yang tampil --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
        <x-mahasiswa.stat-card label="Total prestasi" accent="#2277FF" icon-bg="#EFF6FF" icon-color="#2563EB">
            <x-slot:icon>{!! $icon('award') !!}</x-slot:icon>
            <div class="flex flex-wrap items-end gap-x-2 gap-y-1">
                <span class="{{ $angkaCls }}" x-text="semua.length">{{ $total }}</span>
                <span class="{{ $satuanCls }}">prestasi</span>
            </div>
        </x-mahasiswa.stat-card>

        <x-mahasiswa.stat-card label="Akademik" accent="#8B5CF6" icon-bg="#F5F3FF" icon-color="#7C3AED">
            <x-slot:icon>{!! $icon('book') !!}</x-slot:icon>
            <div class="flex flex-wrap items-end gap-x-2 gap-y-1">
                <span class="{{ $angkaCls }}" x-text="hitung('Akademik')">{{ $akademik }}</span>
                <span class="{{ $satuanCls }}">prestasi</span>
            </div>
        </x-mahasiswa.stat-card>

        <x-mahasiswa.stat-card label="Nonakademik" accent="#F59E0B" icon-bg="#FFFBEB" icon-color="#D97706">
            <x-slot:icon>{!! $icon('laptop') !!}</x-slot:icon>
            <div class="flex flex-wrap items-end gap-x-2 gap-y-1">
                <span class="{{ $angkaCls }}" x-text="hitung('Nonakademik')">{{ $nonakademik }}</span>
                <span class="{{ $satuanCls }}">prestasi</span>
            </div>
        </x-mahasiswa.stat-card>
    </div>

    {{-- Daftar prestasi: kartu bergaya dashboard --}}
    <section class="flex flex-col gap-5 rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)] sm:p-6">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold text-[#111827]">Daftar Prestasi</h2>
            <p class="text-sm text-[#6B7280]">Semua prestasi beserta capaian, penyelenggara, dan bukti pendukung.</p>
        </div>

        <form x-on:submit.prevent class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-[240px] flex-1">
                <label for="q" class="sr-only">Cari prestasi</label>
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[#6B7280]">{!! $icon('search', 'size-4') !!}</span>
                <input id="q" x-model.debounce.200ms="q" placeholder="Cari judul prestasi..."
                       class="{{ $filter }} pl-10 pr-3.5 placeholder:text-[#A8A8A8]">
            </div>

            @foreach ([
                ['tingkat', 'Tingkat', 'Semua tingkat', ['Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional']],
                ['jenis', 'Jenis', 'Semua jenis', ['Akademik', 'Nonakademik']],
                ['tahun', 'Tahun', 'Semua tahun', range(now()->year, now()->year - 9)],
            ] as [$id, $judul, $placeholder, $pilihan])
                <div class="relative w-full sm:w-[180px]">
                    <label for="{{ $id }}" class="sr-only">{{ $judul }}</label>
                    <select id="{{ $id }}" x-model="{{ $id }}" class="{{ $filter }} appearance-none pl-3.5 pr-9">
                        <option value="">{{ $placeholder }}</option>
                        @foreach ($pilihan as $opsi)
                            <option value="{{ $opsi }}">{{ $opsi }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280]">{!! $icon('down', 'size-4') !!}</span>
                </div>
            @endforeach

            <button type="button" x-on:click="reset()"
                    class="h-10 rounded-lg border border-[#E5E7EB] bg-white px-4 text-sm font-semibold text-[#0062FF] transition hover:bg-[#EFF6FF] active:scale-95 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2277FF]">
                Reset
            </button>
        </form>

        {{-- Tabel: 7 kolom, lebar dikunci (table-fixed + colgroup), tinggi baris tetap 80px,
             tinggi minimum = header 44px + 8 baris x 80px + border 2px --}}
        <div class="hidden overflow-x-auto rounded-xl border border-[#E5E7EB] md:block md:min-h-[686px]">
            <table class="w-full min-w-[900px] table-fixed border-collapse">
                <colgroup>
                    <col class="w-[26%]"> {{-- Prestasi / lomba --}}
                    <col class="w-[10%]"> {{-- Capaian --}}
                    <col class="w-[13%]"> {{-- Tingkat --}}
                    <col class="w-[12%]"> {{-- Jenis --}}
                    <col class="w-[17%]"> {{-- Penyelenggara --}}
                    <col class="w-[7%]">  {{-- Tahun --}}
                    <col class="w-[15%]"> {{-- Aksi --}}
                </colgroup>
                <thead class="bg-[#F9FAFB]">
                    <tr class="h-11 border-b border-[#E5E7EB]">
                        @foreach (['Prestasi / lomba', 'Capaian', 'Tingkat', 'Jenis', 'Penyelenggara', 'Tahun'] as $h)
                            <th scope="col" class="{{ $th }} whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                        <th scope="col" class="{{ $th }} whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F3]">
                    <template x-for="p in tampil" :key="p.id">
                        <tr class="h-[80px] transition-colors hover:bg-[#F5F8FF]">
                            {{-- Prestasi / lomba --}}
                            <td class="{{ $td }}">
                                <div class="{{ $clamp }} text-sm font-semibold leading-5 text-[#111827]" x-text="p.nama" x-bind:title="p.nama"></div>
                                <div class="mt-1 flex items-center gap-1.5 text-xs text-[#6B7280]">
                                    <span class="truncate" x-text="p.kategori + ' • ' + p.bentuk"></span>
                                    <span x-show="p.ditag" class="shrink-0 rounded-md bg-[#EEF4FF] px-1.5 py-0.5 text-[11px] font-semibold text-[#0162DD]">Ditag</span>
                                </div>
                            </td>

                            {{-- Capaian --}}
                            <td class="{{ $td }}">
                                <span class="inline-flex max-w-full items-center truncate rounded-full bg-[#F3F4F6] px-2.5 py-1 text-xs font-semibold text-[#111827]" x-text="p.capaian"></span>
                            </td>

                            {{-- Tingkat --}}
                            <td class="{{ $td }}">
                                <span class="inline-flex max-w-full items-center truncate rounded-md border border-[#E5E7EB] bg-white px-2 py-0.5 text-xs font-medium text-[#374151]" x-text="p.tingkat"></span>
                            </td>

                            {{-- Jenis --}}
                            <td class="{{ $td }}">
                                <span class="inline-flex max-w-full items-center truncate rounded-full border px-2.5 py-0.5 text-xs font-medium"
                                      x-bind:class="p.jenis === 'Akademik'
                                          ? 'border-[#B8D0FA] bg-[#EEF4FF] text-[#0162DD]'
                                          : 'border-[#F5D9A8] bg-[#FFF7E6] text-[#B25E09]'"
                                      x-text="p.jenis"></span>
                            </td>

                            {{-- Penyelenggara --}}
                            <td class="{{ $td }}"><div class="{{ $clamp }}" x-text="p.penyelenggara" x-bind:title="p.penyelenggara"></div></td>

                            {{-- Tahun --}}
                            <td class="{{ $td }} whitespace-nowrap font-medium" x-text="p.tahun"></td>

                            {{-- Aksi: semua bisa melihat; edit dan hapus hanya untuk pembuat (bukan anggota yang ditag).
                                 Lebar grup dikunci 112px dan diletakkan di tengah kolom, jadi tombol selalu rapi.
                                 Anggota yang ditag: satu tombol "Lihat" selebar grup. --}}
                            <td class="px-2 py-3 align-middle">
                                <div class="mx-auto flex w-[112px] items-center justify-center gap-2">
                                    <button type="button" title="Lihat detail dan bukti" aria-label="Lihat detail dan bukti"
                                            x-on:click="lihatBukti(p)"
                                            class="{{ $aksiBtn }} bg-[#E0EAFD] text-[#2277FF] hover:bg-[#CDDEFB] focus-visible:outline-[#2277FF]"
                                            x-bind:class="p.bisaUbah ? 'w-8' : 'w-full gap-1.5 text-xs font-semibold'">
                                        {!! $icon('eye', 'size-[18px]') !!}
                                        <span x-show="!p.bisaUbah">Lihat</span>
                                    </button>

                                    <template x-if="p.bisaUbah">
                                        <a x-bind:href="urlEdit(p)" title="Edit prestasi" aria-label="Edit prestasi"
                                           class="{{ $aksiBtn }} w-8 bg-[#FDEBD3] text-[#F59E0B] hover:bg-[#FBDDB4] focus-visible:outline-[#F59E0B]">
                                            {!! $icon('pencil', 'size-[18px]') !!}
                                        </a>
                                    </template>

                                    <template x-if="p.bisaUbah">
                                        <button type="button" title="Hapus prestasi" aria-label="Hapus prestasi"
                                                x-on:click="mintaHapus(p)"
                                                class="{{ $aksiBtn }} w-8 bg-[#FBCBCB] text-[#FF3F3F] hover:bg-[#F8B4B4] focus-visible:outline-[#FF3F3F]">
                                            {!! $icon('trash', 'size-[18px]') !!}
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="terfilter.length === 0" x-cloak>
                        <td colspan="7" class="h-56 px-4 text-center text-sm text-[#6B7280]">
                            <span x-show="semua.length === 0">Belum ada prestasi yang tercatat.</span>
                            <span x-show="semua.length > 0">Tidak ada prestasi yang cocok dengan filter.</span>
                            <button type="button" x-show="semua.length > 0" x-on:click="reset()" class="font-semibold text-[#0062FF] hover:underline">Reset filter</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 md:hidden">
            <div x-show="terfilter.length === 0" x-cloak class="rounded-xl border border-dashed border-[#D1D5DB] px-4 py-8 text-center text-sm text-[#6B7280]">
                <span x-show="semua.length === 0">Belum ada prestasi yang tercatat.</span>
                <span x-show="semua.length > 0">Tidak ada prestasi yang cocok dengan filter.</span>
                <button type="button" x-show="semua.length > 0" x-on:click="reset()" class="ml-1 font-semibold text-[#0062FF] hover:underline">Reset filter</button>
            </div>
            <template x-for="p in tampil" :key="p.id">
                <article class="flex min-w-0 flex-col gap-3 rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-sm">
                    <div class="min-w-0">
                        <div class="break-words text-sm font-semibold leading-5 text-[#111827]" x-text="p.nama"></div>
                        <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-[#6B7280]">
                            <span x-text="p.kategori + ' • ' + p.bentuk"></span>
                            <span x-show="p.ditag" class="rounded-md bg-[#EEF4FF] px-1.5 py-0.5 text-[11px] font-semibold text-[#0162DD]">Ditag</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-[#F3F4F6] px-2.5 py-1 text-xs font-semibold text-[#111827]" x-text="p.capaian"></span>
                        <span class="rounded-md border border-[#E5E7EB] px-2 py-1 text-xs font-medium text-[#374151]" x-text="p.tingkat"></span>
                        <span class="rounded-full border px-2.5 py-1 text-xs font-medium"
                              x-bind:class="p.jenis === 'Akademik' ? 'border-[#B8D0FA] bg-[#EEF4FF] text-[#0162DD]' : 'border-[#F5D9A8] bg-[#FFF7E6] text-[#B25E09]'"
                              x-text="p.jenis"></span>
                    </div>
                    <div class="flex min-w-0 items-start justify-between gap-3 border-t border-[#EEF0F3] pt-3 text-xs">
                        <div class="min-w-0">
                            <div class="break-words font-medium text-[#374151]" x-text="p.penyelenggara"></div>
                            <div class="mt-1 text-[#6B7280]" x-text="p.tahun"></div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <button type="button" title="Lihat detail dan bukti" aria-label="Lihat detail dan bukti"
                                    x-on:click="lihatBukti(p)"
                                    class="{{ $aksiBtn }} size-10 bg-[#E0EAFD] text-[#2277FF] hover:bg-[#CDDEFB] focus-visible:outline-[#2277FF]">
                                {!! $icon('eye', 'size-[18px]') !!}
                            </button>
                            <template x-if="p.bisaUbah">
                                <a x-bind:href="urlEdit(p)" title="Edit prestasi" aria-label="Edit prestasi"
                                   class="{{ $aksiBtn }} size-10 bg-[#FDEBD3] text-[#F59E0B] hover:bg-[#FBDDB4] focus-visible:outline-[#F59E0B]">
                                    {!! $icon('pencil', 'size-[18px]') !!}
                                </a>
                            </template>
                            <template x-if="p.bisaUbah">
                                <button type="button" title="Hapus prestasi" aria-label="Hapus prestasi"
                                        x-on:click="mintaHapus(p)"
                                        class="{{ $aksiBtn }} size-10 bg-[#FBCBCB] text-[#FF3F3F] hover:bg-[#F8B4B4] focus-visible:outline-[#FF3F3F]">
                                    {!! $icon('trash', 'size-[18px]') !!}
                                </button>
                            </template>
                        </div>
                    </div>
                </article>
            </template>
        </div>

        <div class="flex flex-col gap-3 text-xs text-[#6B7280] sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <span x-text="terfilter.length
                    ? `${dari}–${sampai} dari ${terfilter.length} prestasi ditampilkan`
                    : '0 prestasi ditampilkan'"></span>
                <button type="button" x-show="dihapus.length > 0" x-cloak x-on:click="pulihkan()"
                        class="font-semibold text-[#0062FF] hover:underline">Pulihkan data contoh</button>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button type="button" class="{{ $pageBtn }}" x-on:click="halaman--" x-bind:disabled="halaman <= 1">Sebelumnya</button>
                <span x-text="`Halaman ${halaman} dari ${totalHalaman}`"></span>
                <button type="button" class="{{ $pageBtn }}" x-on:click="halaman++" x-bind:disabled="halaman >= totalHalaman">Berikutnya</button>
            </div>
        </div>
    </section>

    {{-- Form tersembunyi untuk menghapus (dipakai saat route hapus sudah dibuat) --}}
    <form x-ref="formHapus" method="POST" action="#" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    {{-- Notifikasi setelah menghapus, dengan tombol Urungkan --}}
    <div x-show="toast" x-cloak x-transition
         class="fixed bottom-6 right-6 z-50 flex max-w-sm items-center gap-3 rounded-xl bg-[#111827] px-4 py-3 text-sm text-white shadow-lg"
         role="status" aria-live="polite">
        <span class="min-w-0 flex-1 truncate" x-text="toast"></span>
        <button type="button" x-show="terakhir" x-on:click="urungkan()" class="shrink-0 font-semibold text-[#8FB8FF] hover:underline">Urungkan</button>
    </div>

    {{-- ================= MODAL: KONFIRMASI HAPUS ================= --}}
    <template x-if="hapusPrestasi">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             role="alertdialog" aria-modal="true" aria-labelledby="judul-hapus"
             x-on:keydown.escape.window="batalHapus()">
            <div class="absolute inset-0 bg-black/50" x-on:click="batalHapus()"></div>

            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex size-11 items-center justify-center rounded-full bg-[#FBCBCB] text-[#FF3F3F]">
                    {!! $icon('trash', 'size-6') !!}
                </div>
                <h2 id="judul-hapus" class="mt-4 text-lg font-bold text-[#111827]">Hapus prestasi?</h2>
                <p class="mt-2 text-sm leading-5 text-[#6B7280]">
                    Prestasi <span class="font-semibold text-[#111827]" x-text="hapusPrestasi.nama"></span>
                    beserta bukti pendukungnya akan dihapus dari daftar.
                </p>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" x-on:click="batalHapus()"
                            class="h-[38px] rounded-lg border border-[#E5E7EB] bg-white px-5 text-sm font-semibold text-[#111827] transition hover:bg-[#F3F4F6]">Batal</button>
                    <button type="button" x-on:click="konfirmasiHapus()"
                            class="h-[38px] rounded-lg bg-[#FF3F3F] px-5 text-sm font-semibold text-white transition hover:bg-[#E03535]">Ya, hapus</button>
                </div>
            </div>
        </div>
    </template>

    {{-- ================= MODAL: DETAIL PRESTASI ================= --}}
    <template x-if="buktiPrestasi">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             role="dialog" aria-modal="true" aria-labelledby="judul-detail"
             x-on:keydown.escape.window="tutupBukti()">

            {{-- Latar gelap: klik untuk menutup --}}
            <div class="absolute inset-0 bg-black/50" x-on:click="tutupBukti()"></div>

            <div class="relative z-10 flex max-h-[90vh] w-full max-w-xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- Kepala --}}
                <div class="flex items-start justify-between gap-4 border-b border-[#E5E7EB] px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-[#6B7280]">Detail prestasi</p>
                        <h2 id="judul-detail" class="text-lg font-bold leading-6 text-[#111827]" x-text="buktiPrestasi.nama"></h2>
                    </div>
                    <button type="button" aria-label="Tutup" x-on:click="tutupBukti()"
                            class="shrink-0 rounded-lg p-1.5 text-[#6B7280] transition hover:bg-[#F3F4F6] hover:text-[#111827]">
                        {!! $icon('close', 'size-5') !!}
                    </button>
                </div>

                {{-- Isi --}}
                <div class="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto p-5">
                    {{-- Catatan untuk anggota yang ditag --}}
                    <div x-show="buktiPrestasi.ditag" class="flex gap-2.5 rounded-xl bg-[#EEF4FF] p-3.5 text-[#003385]">
                        <span class="mt-0.5 shrink-0">{!! $icon('info', 'size-5') !!}</span>
                        <p class="text-[13px] leading-5">Anda ditandai sebagai anggota tim oleh <span class="font-semibold" x-text="buktiPrestasi.pembuat"></span>. Prestasi ini hanya dapat dilihat, tidak dapat diubah atau dihapus.</p>
                    </div>

                    {{-- Semua informasi tampil penuh terlebih dahulu --}}
                    <div class="rounded-xl border border-[#E5E7EB] p-4">
                        <h3 class="text-sm font-semibold text-[#111827]">Informasi prestasi</h3>
                        <dl class="mt-3 flex flex-col gap-2.5 text-[13px]">
                            <template x-for="d in detail" :key="d[0]">
                                <div class="flex justify-between gap-4">
                                    <dt class="shrink-0 text-[#6B7280]" x-text="d[0]"></dt>
                                    <dd class="text-right font-medium text-[#111827]" x-text="d[1]"></dd>
                                </div>
                            </template>
                        </dl>
                    </div>

                    {{-- Link bukti ditempatkan di bawah seluruh informasi prestasi --}}
                    <div class="rounded-xl border border-[#E5E7EB] p-4">
                        <h3 class="text-sm font-semibold text-[#111827]">Tautan bukti prestasi</h3>
                        <p x-show="buktiPrestasi.bukti.length === 0" class="mt-3 text-[13px] text-[#6B7280]">
                            Belum ada tautan bukti yang ditambahkan.
                        </p>
                        <div class="mt-3 flex flex-col gap-2">
                            <template x-for="(b, i) in buktiPrestasi.bukti" :key="i">
                                <div class="flex min-w-0 items-center gap-3 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] p-3">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-[#EEF4FF] text-[#0162DD]">
                                        {!! $icon('link', 'size-5') !!}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[13px] font-semibold text-[#111827]" x-text="b.jenis || 'Dokumen bukti'"></p>
                                        <template x-if="b.url">
                                            <a x-bind:href="b.url" target="_blank" rel="noopener noreferrer"
                                               class="block truncate text-xs text-[#6B7280] hover:text-[#0062FF]"
                                               x-bind:title="b.url" x-text="b.url"></a>
                                        </template>
                                        <p x-show="!b.url" class="text-xs text-[#6B7280]">Tautan bukti belum tersedia.</p>
                                    </div>
                                    <a x-show="b.url" x-bind:href="b.url" target="_blank" rel="noopener noreferrer"
                                       class="flex h-8 shrink-0 items-center gap-1.5 rounded-lg border border-[#E5E7EB] bg-white px-3 text-xs font-semibold text-[#0062FF] transition hover:bg-[#EFF6FF]">
                                        {!! $icon('external', 'size-4') !!} Buka
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Kaki --}}
                <div class="flex justify-end border-t border-[#E5E7EB] px-5 py-3">
                    <button type="button" x-on:click="tutupBukti()"
                            class="h-[38px] rounded-lg border border-[#E5E7EB] bg-white px-5 text-sm font-semibold text-[#111827] transition hover:bg-[#F3F4F6]">Tutup</button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    // Dideklarasikan global supaya bisa dipakai x-data (Alpine dimuat setelah script ini).
    const DATA_PRESTASI = @js($prestasi);
    const URL_EDIT = @js($urlEdit);   // null jika route belum dibuat
    const URL_HAPUS = @js($urlHapus); // null jika route belum dibuat
    const KUNCI_HAPUS = 'prestasi-mhs-dihapus';

    // localStorage bisa tidak tersedia (mode privat, dll.), jadi dibungkus try/catch.
    function bacaDihapus() {
        try { return JSON.parse(localStorage.getItem(KUNCI_HAPUS) || '[]'); } catch (e) { return []; }
    }
    function simpanDihapus(ids) {
        try { localStorage.setItem(KUNCI_HAPUS, JSON.stringify(ids)); } catch (e) {}
    }

    function prestasiList() {
        return {
            semua: [],
            dihapus: [],       // id prestasi yang sudah dihapus (mode contoh)
            q: '',
            tingkat: '',
            jenis: '',
            tahun: '',
            halaman: 1,
            perHalaman: 8,

            // Modal dan notifikasi
            buktiPrestasi: null,
            hapusPrestasi: null,
            toast: '',
            terakhir: null,    // prestasi yang baru dihapus (untuk Urungkan)
            timerToast: null,

            init() {
                this.dihapus = bacaDihapus();
                this.semua = DATA_PRESTASI.filter(p => !this.dihapus.includes(p.id));

                // Setiap filter berubah, kembali ke halaman 1.
                ['q', 'tingkat', 'jenis', 'tahun'].forEach(k => this.$watch(k, () => this.halaman = 1));
            },

            hitung(jenis) {
                return this.semua.filter(p => p.jenis === jenis).length;
            },

            get terfilter() {
                const kata = this.q.trim().toLowerCase();

                return this.semua.filter(p =>
                    (!kata || `${p.nama} ${p.penyelenggara} ${p.kategori}`.toLowerCase().includes(kata)) &&
                    (!this.tingkat || p.tingkat === this.tingkat) &&
                    (!this.jenis || p.jenis === this.jenis) &&
                    (!this.tahun || String(p.tahun) === this.tahun)
                );
            },

            get totalHalaman() {
                return Math.max(1, Math.ceil(this.terfilter.length / this.perHalaman));
            },

            get tampil() {
                const awal = (this.halaman - 1) * this.perHalaman;
                return this.terfilter.slice(awal, awal + this.perHalaman);
            },

            get dari() {
                return (this.halaman - 1) * this.perHalaman + 1;
            },

            get sampai() {
                return Math.min(this.halaman * this.perHalaman, this.terfilter.length);
            },

            reset() {
                this.q = '';
                this.tingkat = '';
                this.jenis = '';
                this.tahun = '';
                this.halaman = 1;
            },

            // Kunci scroll halaman saat modal terbuka
            kunci(aktif) {
                document.body.classList.toggle('overflow-hidden', aktif);
            },

            tampilToast(pesan, bisaUrungkan = false) {
                this.toast = pesan;
                if (!bisaUrungkan) this.terakhir = null;
                clearTimeout(this.timerToast);
                this.timerToast = setTimeout(() => { this.toast = ''; this.terakhir = null; }, 6000);
            },

            // ----- Aksi: edit -----
            urlEdit(p) {
                return URL_EDIT ? URL_EDIT.replace('__ID__', p.id) : '#';
            },

            // ----- Aksi: hapus (hanya pembuat; anggota yang ditag tidak punya tombol ini) -----
            mintaHapus(p) {
                if (!p.bisaUbah) return;
                this.hapusPrestasi = p;
                this.kunci(true);
            },

            batalHapus() {
                this.hapusPrestasi = null;
                this.kunci(false);
            },

            konfirmasiHapus() {
                const p = this.hapusPrestasi;
                if (!p || !p.bisaUbah) return;

                // Route hapus sudah ada: kirim form DELETE ke server.
                if (URL_HAPUS) {
                    const form = this.$refs.formHapus;
                    form.action = URL_HAPUS.replace('__ID__', p.id);
                    form.submit();
                    return;
                }

                // Mode contoh (route belum ada): hanya baris yang diklik yang dihapus,
                // dan keputusan ini diingat di browser supaya tidak muncul lagi saat halaman dimuat ulang.
                this.dihapus = [...this.dihapus, p.id];
                simpanDihapus(this.dihapus);
                this.semua = this.semua.filter(x => x.id !== p.id);

                this.terakhir = p;
                this.batalHapus();
                if (this.halaman > this.totalHalaman) this.halaman = this.totalHalaman;
                this.tampilToast('Prestasi "' + p.nama + '" dihapus.', true);
            },

            urungkan() {
                const p = this.terakhir;
                if (!p) return;

                this.dihapus = this.dihapus.filter(id => id !== p.id);
                simpanDihapus(this.dihapus);
                this.semua = [...this.semua, p].sort((a, b) => a.id - b.id);
                this.terakhir = null;
                this.tampilToast('Penghapusan dibatalkan.');
            },

            pulihkan() {
                this.dihapus = [];
                simpanDihapus([]);
                this.semua = [...DATA_PRESTASI];
                this.terakhir = null;
                this.tampilToast('Data contoh dipulihkan.');
            },

            // ----- Aksi: lihat detail dan bukti -----
            get detail() {
                const p = this.buktiPrestasi;
                if (!p) return [];
                return [
                    ['Capaian', p.capaian],
                    ['Tingkat', p.tingkat],
                    ['Jenis', p.jenis],
                    ['Bentuk', p.bentuk],
                    ['Kategori', p.kategori],
                    ['Penyelenggara', p.penyelenggara],
                    ['Tanggal', p.tanggal],
                    ['Kegiatan terkait', p.kegiatan],
                    ['Dibuat oleh', p.pembuat],
                ];
            },

            lihatBukti(p) {
                this.buktiPrestasi = p;
                this.kunci(true);
            },

            tutupBukti() {
                this.buktiPrestasi = null;
                this.kunci(false);
            },
        };
    }
</script>
@endsection