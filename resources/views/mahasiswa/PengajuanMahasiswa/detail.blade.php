@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@php
    // DATA TIRUAN. $id berasal dari alamat (/pengajuan/dosen/{id} atau /pengajuan/mahasiswa/{id}).
    // Nanti diganti data pengajuan dari controller / database oleh tim backend.
    // $asal diisi dari route: 'dosen' (Tinjau di Pengajuan Dosen) atau 'mahasiswa' (Tinjau di Pengajuan Mahasiswa).
    $asal  = $asal ?? 'mahasiswa';
    $dosen = $asal === 'dosen';

    // Contoh data. Urutannya sama dengan daftar di halaman Pengajuan (id 1–6), jadi tombol Tinjau
    // di baris ke-3 membuka contoh "Direvisi", baris ke-4 contoh "Ditolak", dst.
    $contohDosen = [
        1 => ['nama' => 'Penyuluhan Literasi Digital', 'pengaju' => 'Filda Huwani Dewi',  'kategori' => 'Pengabdian', 'status' => 'Diajukan', 'catatan' => null],
        2 => ['nama' => 'Seminar Nasional AI',         'pengaju' => 'Angga Wahyu Wibowo', 'kategori' => 'Pengabdian', 'status' => 'Direview', 'catatan' => null],
        3 => ['nama' => 'Pelatihan Industri',          'pengaju' => 'Kuwat Santoso',      'kategori' => 'Pengabdian', 'status' => 'Direvisi', 'catatan' => 'Lampirkan surat tugas yang sudah ditandatangani.'],
        4 => ['nama' => 'Woorkshop Penulisan Jurnal',  'pengaju' => 'Tahan Prahara',      'kategori' => 'Pengabdian', 'status' => 'Ditolak',  'catatan' => 'Jadwal bentrok dengan kegiatan fakultas pada tanggal yang sama.'],
        5 => ['nama' => 'Kunjungan Industri',          'pengaju' => 'Wiktasari',          'kategori' => 'Pengabdian', 'status' => 'Diterima', 'catatan' => null],
        6 => ['nama' => 'Kuliah Tamu',                 'pengaju' => 'Kuwat Santoso',      'kategori' => 'Pengabdian', 'status' => 'Selesai',  'catatan' => null],
    ];
    $contohMhs = [
        1 => ['nama' => 'Volunteer Bakti Sosial',     'pengaju' => 'Fajar Nugroho',   'nim' => '43325101', 'kategori' => 'Pengabdian', 'status' => 'Diajukan', 'catatan' => null],
        2 => ['nama' => 'Lomba KMIPN 2026',           'pengaju' => 'Mukhlis Pratama', 'nim' => '43325121', 'kategori' => 'Lomba',      'status' => 'Direview', 'catatan' => null],
        3 => ['nama' => 'Kunjungan Industri Bandung', 'pengaju' => 'Dimas Pratama',   'nim' => '43325133', 'kategori' => 'Pengabdian', 'status' => 'Direvisi', 'catatan' => 'Lengkapi surat izin dan jadwal kegiatan.'],
        4 => ['nama' => 'Lomba Desain Poster',        'pengaju' => 'Eka Putri',       'nim' => '43325140', 'kategori' => 'Lomba',      'status' => 'Ditolak',  'catatan' => 'Lomba tidak termasuk daftar kegiatan yang diakui program studi.'],
        5 => ['nama' => 'Magang Industri',            'pengaju' => 'Citra Lestari',   'nim' => '43325155', 'kategori' => 'Magang',     'status' => 'Diterima', 'catatan' => null],
        6 => ['nama' => 'Magang MSIB',                'pengaju' => 'Gita Maharani',   'nim' => '43325162', 'kategori' => 'Magang',     'status' => 'Selesai',  'catatan' => null],
    ];
    $cd      = ($dosen ? $contohDosen : $contohMhs)[max(1, min(6, (int) ($id ?? 5)))];
    $status  = $cd['status'];
    $catatan = $cd['catatan'];   // catatan revisi / alasan penolakan
    $pengaju = $cd['pengaju'];

    // Siapa yang meninjau: admin (untuk pengajuan dosen) atau dosen pembimbing (untuk pengajuan mahasiswa).
    $peninjau        = $dosen ? 'Admin' : 'Kuwat Santoso';
    $judulKeputusan  = $dosen ? 'Keputusan Admin' : 'Keputusan Anda';

    $bisaRevisi   = $dosen && $status === 'Direvisi';                          // dosen unggah dokumen perbaikan lalu ajukan kembali
    $bisaPutuskan = ! $dosen && in_array($status, ['Diajukan', 'Direview']);  // dosen pembimbing memutuskan pengajuan mahasiswa
    $interaktif   = $bisaRevisi || $bisaPutuskan;
    $infoDiganti  = $dosen && $status === 'Direvisi';                          // kartu Informasi diganti kartu Keputusan Admin

    $info = [
        'nama'          => $cd['nama'],
        'jenis'         => $dosen ? 'Kegiatan Dosen' : 'Kegiatan Mahasiswa',
        'kategori'      => $cd['kategori'],
        'penyelenggara' => $dosen ? 'PT Mitra Teknologi' : 'Politeknik Negeri Semarang',
        'tanggal'       => '10 – 12 Oktober 2026',
        'status'        => $status,
    ];
    if (! $dosen) {
        $info['pengaju']    = $cd['pengaju'] . ' · NIM ' . $cd['nim'];
        $info['pembimbing'] = 'Kuwat Santoso';
    }

    // Riwayat status, dari yang paling awal. tipe: biru | oranye | merah | hijau
    $w = '12 Agustus 2026 · 09.14';
    $riwayat = [['judul' => 'Diajukan', 'tipe' => 'biru', 'waktu' => $w, 'oleh' => $pengaju, 'catatan' => 'Pengajuan kegiatan beserta proposal', 'saatIni' => false]];
    if ($status !== 'Diajukan') {
        $riwayat[] = ['judul' => 'Direview', 'tipe' => 'biru', 'waktu' => $w, 'oleh' => $peninjau, 'catatan' => null, 'saatIni' => false];
    }
    $akhir = [
        'Diajukan' => null,
        'Direview' => null,
        'Direvisi' => ['judul' => 'Direvisi', 'tipe' => 'oranye', 'catatan' => $catatan],
        'Ditolak'  => ['judul' => 'Ditolak',  'tipe' => 'merah',  'catatan' => $catatan],
        'Diterima' => ['judul' => 'Diterima', 'tipe' => 'hijau',  'catatan' => null],
        'Selesai'  => ['judul' => 'Selesai',  'tipe' => 'hijau',  'catatan' => null],
    ][$status];
    if ($akhir) {
        $riwayat[] = $akhir + ['waktu' => $w, 'oleh' => $peninjau, 'saatIni' => false];
    }
    $riwayat[count($riwayat) - 1]['saatIni'] = true;

    $namaBerkas = 'Proposal-' . \Illuminate\Support\Str::slug($info['nama']) . '.pdf';
    $dokumen = [
        ['file' => $namaBerkas, 'jenis' => 'Proposal', 'ukuran' => '1,2 MB', 'url' => '#'],
        ['file' => $namaBerkas, 'jenis' => 'Proposal', 'ukuran' => '1,2 MB', 'url' => '#'],
    ];

    $ikon = [
        'biru'   => 'bg-brand text-white',
        'hijau'  => 'bg-lime-500 text-white',
        'oranye' => 'bg-orange-400 text-white',
        'merah'  => 'bg-red-500 text-white',
    ];
    $warnaJudul = ['biru' => 'text-gray-900', 'hijau' => 'text-lime-600', 'oranye' => 'text-orange-800', 'merah' => 'text-red-600'];

    // Warna badge status di kartu Informasi Kegiatan
    $badge = [
        'Diajukan' => 'border-blue-200 bg-blue-100 text-blue-700',
        'Direview' => 'border-blue-200 bg-blue-100 text-blue-700',
        'Direvisi' => 'border-orange-200 bg-orange-100 text-orange-700',
        'Ditolak'  => 'border-red-200 bg-red-100 text-red-600',
        'Diterima' => 'border-green-200 bg-green-100 text-green-700',
        'Selesai'  => 'border-green-200 bg-green-100 text-green-700',
    ];

    $cardClass = 'flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4';
@endphp

@section('content')

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl font-bold tracking-tight">Detail Pengajuan</h1>
            <p class="text-2xl text-gray-400">Dashboard / Pengajuan / {{ $dosen ? 'Dosen' : 'Mahasiswa' }} / Detail</p>
        </div>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route($dosen ? 'pengajuan.dosen' : 'pengajuan.mahasiswa') }}"
           class="mt-2 flex h-11 items-center gap-2 rounded-md bg-blue-100 px-4 text-base font-medium text-brand hover:bg-blue-200">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 rounded-lg bg-white p-4.5 shadow-sm lg:grid-cols-2 lg:items-start"
         @if ($interaktif) x-data="tinjauan(@js($status), @js($pengaju), @js($peninjau))" @endif>

        {{-- KIRI: RIWAYAT STATUS --}}
        <section class="{{ $cardClass }}" aria-labelledby="h-riwayat">
            <h2 id="h-riwayat" class="text-xl font-semibold">Riwayat Status</h2>

            <ol>
                @foreach ($riwayat as $r)
                    <li class="flex gap-3">
                        {{-- ikon + garis penghubung --}}
                        <div class="flex flex-col items-center">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full {{ $ikon[$r['tipe']] }}">
                                @if ($r['tipe'] === 'oranye')
                                    <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                @elseif ($r['tipe'] === 'merah')
                                    <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                @else
                                    <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                @endif
                            </span>
                            @if (! $loop->last)
                                <span class="min-h-6 w-0.5 flex-1 bg-brand" aria-hidden="true"></span>
                            @elseif ($interaktif)
                                <span x-show="kembali" x-cloak class="min-h-6 w-0.5 flex-1 bg-brand" aria-hidden="true"></span>
                            @endif
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col gap-1 text-[15px] {{ $loop->last ? '' : 'pb-5' }}"
                             @if ($loop->last && $interaktif) :class="kembali ? 'pb-5' : ''" @endif>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-semibold {{ $warnaJudul[$r['tipe']] }}">{{ $r['judul'] }}</h3>
                                @if ($r['saatIni'])
                                    <span @if ($interaktif) x-show="!kembali" @endif class="rounded-full bg-lime-100 px-2 py-0.5 text-xs text-lime-700">Status saat ini</span>
                                @endif
                            </div>
                            <p class="mt-1">{{ $r['waktu'] }}</p>
                            <p>Oleh {{ $r['oleh'] }}</p>

                            @if ($r['catatan'])
                                <p class="mt-2 rounded-md px-3 py-2.5 text-xs {{ $r['tipe'] === 'oranye' ? 'bg-orange-300 text-gray-900' : ($r['tipe'] === 'merah' ? 'bg-red-400 text-gray-900' : 'bg-gray-100 text-gray-600') }}">{{ $r['catatan'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach

                @if ($interaktif)
                    {{-- Langkah baru yang muncul setelah dosen mengambil keputusan / mengajukan kembali (simulasi) --}}
                    <li x-show="kembali" x-cloak class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full text-white" :class="entri.ikonBg">
                                <svg x-show="entri.tipe === 'biru' || entri.tipe === 'hijau'" class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                <svg x-show="entri.tipe === 'oranye'" x-cloak class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                <svg x-show="entri.tipe === 'merah'" x-cloak class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                            </span>
                        </div>
                        <div class="flex min-w-0 flex-1 flex-col gap-1 text-[15px]">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-semibold" :class="entri.judulWarna" x-text="entri.judul"></h3>
                                <span class="rounded-full bg-lime-100 px-2 py-0.5 text-xs text-lime-700">Status saat ini</span>
                            </div>
                            <p class="mt-1" x-text="entri.waktu"></p>
                            <p x-text="`Oleh ${entri.oleh}`"></p>
                            <p x-show="entri.catatan" class="mt-2 rounded-md px-3 py-2.5 text-xs" :class="entri.catatanBg" x-text="entri.catatan"></p>
                        </div>
                    </li>
                @endif
            </ol>
        </section>

        {{-- KANAN --}}
        <div class="flex flex-col gap-5">

            @if ($bisaPutuskan)
                {{-- Dosen pembimbing memutuskan pengajuan mahasiswa. Tombol hanya simulasi: nanti backend menyimpan keputusan. --}}
                <section class="{{ $cardClass }}" aria-labelledby="h-keputusan">
                    <h2 id="h-keputusan" class="text-xl font-semibold">{{ $judulKeputusan }}</h2>

                    <div x-show="!kembali" class="flex flex-col gap-4 px-4 pb-3">
                        <div class="flex flex-col gap-2">
                            <label for="catatan" class="text-[15px]">Catatan revisi</label>
                            <textarea id="catatan" rows="3" x-model="catatan" x-on:input="galat = ''"
                                      placeholder="Wajib diisi jika meminta revisi atau menolak"
                                      :aria-invalid="galat ? 'true' : 'false'" aria-describedby="galat-catatan"
                                      :class="galat ? 'border-red-500' : 'border-gray-300'"
                                      class="min-h-20 w-full resize-y rounded-md border bg-white px-3 py-2.5 text-[15px] placeholder:text-gray-400"></textarea>
                            <p id="galat-catatan" x-show="galat" x-cloak x-text="galat" class="text-sm text-red-600"></p>
                        </div>
                        <div class="flex flex-col gap-3">
                            <button type="button" x-on:click="putuskan('Diterima')"
                                    class="h-10 w-full cursor-pointer rounded-md border border-brand bg-white text-base text-brand hover:bg-blue-50">Setujui</button>
                            <button type="button" x-on:click="putuskan('Direvisi')"
                                    class="h-10 w-full cursor-pointer rounded-md border border-orange-300 bg-white text-base text-orange-600 hover:bg-orange-50">Minta revisi</button>
                            <button type="button" x-on:click="putuskan('Ditolak')"
                                    class="h-10 w-full cursor-pointer rounded-md border border-red-300 bg-white text-base text-red-600 hover:bg-red-50">Tolak</button>
                        </div>
                    </div>

                    <div x-show="kembali" x-cloak class="flex flex-col gap-2 px-4 pb-3 text-[15px]">
                        <p>Keputusan Anda: <span class="font-semibold" x-text="statusSekarang"></span></p>
                        <p x-show="catatan.trim()" class="rounded-md bg-gray-50 px-3 py-2.5 text-gray-700" x-text="catatan"></p>
                    </div>
                </section>
            @endif

            @if ($status === 'Direvisi')
                {{-- Direvisi: tampilkan catatan revisi (hanya dibaca, tanpa tombol). Untuk pengajuan dosen, kartu ini
                     menggantikan kartu Informasi Kegiatan. --}}
                <section class="{{ $cardClass }}" aria-labelledby="h-keputusan-revisi">
                    <h2 id="h-keputusan-revisi" class="text-xl font-semibold">{{ $judulKeputusan }}</h2>
                    <div class="flex flex-col gap-2 px-4 pb-3">
                        <label for="catatan-revisi" class="text-[15px]">Catatan revisi</label>
                        <textarea id="catatan-revisi" rows="4" readonly
                                  class="min-h-24 w-full resize-none rounded-md border border-gray-200 bg-gray-50 px-3 py-2.5 text-[15px] text-gray-900">{{ $catatan }}</textarea>
                        @unless ($dosen)
                            <p class="text-[15px] text-gray-400">Menunggu mahasiswa memperbaiki dan mengajukan kembali.</p>
                        @endunless
                    </div>
                </section>
            @endif

            @if ($bisaRevisi)
                {{-- Unggah dokumen perbaikan lalu ajukan kembali. Hanya simulasi: nanti backend menyimpan file dan mengubah status. --}}
                <section class="{{ $cardClass }}" aria-labelledby="h-perbaikan">
                    <h2 id="h-perbaikan" class="text-xl font-semibold">Dokumen Perbaikan</h2>

                    <div x-show="!kembali" class="flex flex-col gap-4 px-4 pb-3">
                        {{-- label dibuat relative supaya jendela pilih file tidak menggeser halaman --}}
                        <label class="relative flex cursor-pointer flex-col items-center gap-1.5 rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center hover:bg-gray-100 has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-brand">
                            <svg class="size-7 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V5M7.5 9.5L12 5l4.5 4.5M5 19h14"/></svg>
                            <span class="text-[15px] font-medium">Pilih file dari perangkat</span>
                            <span class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, atau PNG. Maksimal 5 MB per file.</span>
                            <input type="file" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" x-on:change="pilih($event)" class="sr-only">
                        </label>

                        <p x-show="galat" x-cloak x-text="galat" role="alert" class="text-sm text-red-600"></p>

                        <ul x-show="baru.length" x-cloak class="flex flex-col">
                            <template x-for="(f, i) in baru" :key="f.id">
                                <li class="flex flex-wrap items-center gap-3 border-b border-gray-200 py-2.5 last:border-b-0">
                                    <div class="flex min-w-0 flex-1 flex-col">
                                        <span class="truncate text-[15px] font-medium" x-text="f.nama" :title="f.nama"></span>
                                        <span class="text-xs text-gray-500" x-text="f.ukuran"></span>
                                    </div>
                                    <select x-model="f.jenis" :aria-label="`Jenis dokumen ${f.nama}`"
                                            class="h-9 rounded-md border border-gray-300 bg-white px-2 text-sm">
                                        <option>Proposal</option><option>Surat tugas</option><option>Sertifikat</option><option>Lainnya</option>
                                    </select>
                                    <button type="button" x-on:click="hapus(i)" :aria-label="`Hapus ${f.nama}`"
                                            class="h-9 cursor-pointer rounded-md px-3 text-sm text-red-600 hover:bg-red-50">Hapus</button>
                                </li>
                            </template>
                        </ul>

                        <button type="button" x-on:click="ajukan()"
                                class="flex h-10 w-full cursor-pointer items-center justify-center rounded-md bg-brand text-base text-white hover:bg-blue-700">
                            Ajukan kembali
                        </button>
                        <p class="text-[15px] text-gray-400">Pastikan dokumen sudah sesuai catatan revisi sebelum diajukan kembali.</p>
                    </div>

                    <p x-show="kembali" x-cloak class="px-4 pb-3 text-[15px] text-green-700">
                        Pengajuan sudah diajukan kembali dan menunggu review admin.
                    </p>
                </section>
            @endif

            @if ($status === 'Ditolak')
                {{-- Ditolak: tampilkan alasan penolakan --}}
                <section class="flex flex-col gap-2 rounded-lg border border-red-200 bg-red-50 p-4" aria-labelledby="h-tolak">
                    <h2 id="h-tolak" class="flex items-center gap-2 text-xl font-semibold text-red-700">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 7.5v5.5M12 16.5v.01"/></svg>
                        Alasan Penolakan
                    </h2>
                    <p class="px-1 text-[15px] text-gray-900">{{ $catatan }}</p>
                </section>
            @endif

            @unless ($infoDiganti)
                {{-- Tombol "Tandai selesai" hanya simulasi dan hanya ada kalau pengajuan sudah Diterima / Selesai.
                     Nanti backend yang mengubah status. --}}
                <section class="{{ $cardClass }}" aria-labelledby="h-info"
                         x-data="{ selesai: @js($status === 'Selesai') }">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="h-info" class="text-xl font-semibold">Informasi Kegiatan</h2>
                        @if ($bisaPutuskan)
                            <span class="rounded-md border px-2 py-0.5 text-xs" :class="warnaStatus[selesai ? 'Selesai' : statusSekarang]"
                                  x-text="selesai ? 'Selesai' : statusSekarang"></span>
                        @else
                            <span class="rounded-md border px-2 py-0.5 text-xs {{ $badge[$status] }}"
                                  x-text="selesai ? 'Selesai' : @js($status)"></span>
                        @endif
                    </div>

                    <dl class="grid grid-cols-2 gap-x-4 gap-y-5 px-4 text-[15px]">
                        <div class="col-span-2 flex flex-col gap-2"><dt>Nama kegiatan</dt><dd class="font-medium">{{ $info['nama'] }}</dd></div>
                        <div class="flex flex-col gap-2"><dt>Jenis kegiatan</dt><dd class="font-medium">{{ $info['jenis'] }}</dd></div>
                        <div class="flex flex-col gap-2"><dt>Kategori</dt><dd class="font-medium">{{ $info['kategori'] }}</dd></div>
                        @unless ($dosen)
                            <div class="col-span-2 flex flex-col gap-2"><dt>Diajukan oleh</dt><dd class="font-medium">{{ $info['pengaju'] }}</dd></div>
                            <div class="col-span-2 flex flex-col gap-2"><dt>Dosen pembimbing</dt><dd class="font-medium">{{ $info['pembimbing'] }}</dd></div>
                        @endunless
                        <div class="col-span-2 flex flex-col gap-2"><dt>Penyelenggara</dt><dd class="font-medium">{{ $info['penyelenggara'] }}</dd></div>
                        <div class="col-span-2 flex flex-col gap-2"><dt>Tanggal pelaksanaan</dt><dd class="font-medium">{{ $info['tanggal'] }}</dd></div>
                    </dl>

                    @if ($bisaPutuskan || in_array($status, ['Diterima', 'Selesai']))
                        <div class="flex flex-col gap-3 px-4 pb-3"
                             @if ($bisaPutuskan) x-show="statusSekarang === 'Diterima'" x-cloak @endif>
                            <button type="button" x-on:click="selesai = true" :disabled="selesai"
                                    class="flex h-10 w-full cursor-pointer items-center justify-center gap-1.5 rounded-md bg-brand text-base text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                <span x-text="selesai ? 'Sudah ditandai selesai' : 'Tandai selesai'"></span>
                            </button>
                            <p class="text-[15px] text-gray-400">Tombol ini aktif setelah kegiatan dilaksanakan dan dokumen pendukung lengkap.</p>
                        </div>
                    @endif
                </section>
            @endunless

            <section class="{{ $cardClass }}" aria-labelledby="h-dok">
                <h2 id="h-dok" class="text-xl font-semibold">Dokumentasi Terlampir</h2>

                <ul class="flex flex-col">
                    @foreach ($dokumen as $d)
                        <li class="flex items-center justify-between gap-3 py-2.5 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                            <div class="flex min-w-0 flex-col gap-1">
                                <span class="truncate text-lg font-medium" title="{{ $d['file'] }}">{{ $d['file'] }}</span>
                                <span class="text-[15px] text-gray-400">{{ $d['jenis'] }} · {{ $d['ukuran'] }}</span>
                            </div>
                            <a href="{{ $d['url'] }}" download="{{ $d['file'] }}" aria-label="Unduh {{ $d['file'] }}"
                               class="flex size-10 shrink-0 items-center justify-center rounded-md bg-brand text-white hover:bg-blue-700">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v13M6.5 11.5L12 17l5.5-5.5"/></svg>
                            </a>
                        </li>
                    @endforeach

                    @if ($bisaRevisi)
                        {{-- dokumen perbaikan yang baru diajukan --}}
                        <template x-for="f in (kembali ? baru : [])" :key="f.id">
                            <li class="flex items-center justify-between gap-3 border-t border-gray-200 py-2.5">
                                <div class="flex min-w-0 flex-col gap-1">
                                    <span class="truncate text-lg font-medium" x-text="f.nama" :title="f.nama"></span>
                                    <span class="text-[15px] text-gray-400" x-text="`${f.jenis} · ${f.ukuran}`"></span>
                                </div>
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-md bg-brand text-white" aria-hidden="true">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v13M6.5 11.5L12 17l5.5-5.5"/></svg>
                                </span>
                            </li>
                        </template>
                    @endif
                </ul>
            </section>
        </div>
    </div>

@endsection

@if ($interaktif)
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Satu komponen untuk dua keadaan:
        //  - dosen pembimbing memutuskan pengajuan mahasiswa (putuskan)
        //  - dosen mengunggah dokumen perbaikan lalu mengajukan kembali (pilih, hapus, ajukan)
        // Semuanya simulasi tampilan. Nanti backend yang menyimpan data.
        Alpine.data('tinjauan', (status, pengaju, peninjau) => ({
            statusSekarang: status,
            catatan: '',
            galat: '',
            kembali: false,   // true setelah ada langkah baru di riwayat
            entri: {},
            baru: [],
            nomor: 0,

            warnaStatus: {
                Diajukan: 'border-blue-200 bg-blue-100 text-blue-700',
                Direview: 'border-blue-200 bg-blue-100 text-blue-700',
                Direvisi: 'border-orange-200 bg-orange-100 text-orange-700',
                Ditolak:  'border-red-200 bg-red-100 text-red-600',
                Diterima: 'border-green-200 bg-green-100 text-green-700',
                Selesai:  'border-green-200 bg-green-100 text-green-700',
            },
            gaya: {
                biru:   { ikonBg: 'bg-brand',      judulWarna: 'text-gray-900',   catatanBg: 'bg-gray-100 text-gray-600' },
                oranye: { ikonBg: 'bg-orange-400', judulWarna: 'text-orange-800', catatanBg: 'bg-orange-300 text-gray-900' },
                merah:  { ikonBg: 'bg-red-500',    judulWarna: 'text-red-600',    catatanBg: 'bg-red-400 text-gray-900' },
                hijau:  { ikonBg: 'bg-lime-500',   judulWarna: 'text-lime-600',   catatanBg: 'bg-gray-100 text-gray-600' },
            },

            sekarang() {
                const t = new Date();
                return t.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) + ' · ' +
                       t.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace(':', '.');
            },
            tambahRiwayat(judul, tipe, oleh, catatan) {
                this.entri = { judul, tipe, oleh, catatan, waktu: this.sekarang(), ...this.gaya[tipe] };
                this.kembali = true;
            },

            // Dosen pembimbing: Setujui / Minta revisi / Tolak
            putuskan(hasil) {
                if (hasil !== 'Diterima' && this.catatan.trim() === '') {
                    this.galat = 'Catatan wajib diisi jika meminta revisi atau menolak.';
                    this.$nextTick(() => document.getElementById('catatan').focus());
                    return;
                }
                this.galat = '';
                this.statusSekarang = hasil;
                const tipe  = { Diterima: 'hijau', Direvisi: 'oranye', Ditolak: 'merah' }[hasil];
                this.tambahRiwayat(hasil, tipe, peninjau, this.catatan.trim() || null);
                const pesan = {
                    Diterima: ['sukses', 'Pengajuan disetujui'],
                    Direvisi: ['info',   'Permintaan revisi terkirim'],
                    Ditolak:  ['sukses', 'Pengajuan ditolak'],
                }[hasil];
                this.$dispatch('tampil-alert', { tipe: pesan[0], judul: pesan[1] });
            },

            // Dosen: unggah dokumen perbaikan
            pilih(e) {
                this.galat = '';
                for (const f of e.target.files) {
                    if (f.size > 5 * 1024 * 1024) { this.galat = `${f.name} lebih dari 5 MB.`; continue; }
                    const kb = f.size / 1024;
                    this.baru.push({
                        id: ++this.nomor, nama: f.name, jenis: 'Proposal',
                        ukuran: kb >= 1024 ? (kb / 1024).toFixed(1).replace('.', ',') + ' MB' : Math.max(1, Math.round(kb)) + ' KB',
                    });
                }
                e.target.value = '';
            },
            hapus(i) { this.baru.splice(i, 1); },
            ajukan() {
                if (this.baru.length === 0) { this.galat = 'Pilih minimal satu dokumen perbaikan.'; return; }
                this.galat = '';
                this.tambahRiwayat('Diajukan kembali', 'biru', pengaju, `Dokumen perbaikan: ${this.baru.length} file`);
                this.$dispatch('tampil-alert', { tipe: 'sukses', judul: 'Pengajuan diajukan kembali', pesan: 'Menunggu review admin.' });
            },
        }));
    });
</script>
@endpush
@endif