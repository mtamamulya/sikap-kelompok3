@extends('layouts.mahasiswa-tim')

@section('title', isset($prestasi) ? 'Edit prestasi' : 'Tambah prestasi')

@section('content')
@php
    // Helper tampilan: pesan galat dan kelas input (border merah saat ada galat)
    $galat = fn (string $f) => $errors->has($f) ? '<p class="text-xs text-[#FF3F3F]">' . e($errors->first($f)) . '</p>' : '';
    $in    = fn (string $f) => 'h-[38px] w-full rounded-lg border bg-white px-3 text-sm text-[#0A0A0A] outline-none transition placeholder:text-[#A8A8A8] focus:border-[#2277FF] focus:ring-2 focus:ring-[#2277FF]/15 '
                             . ($errors->has($f) ? 'border-[#FF3F3F]' : 'border-[#E5E7EB]');
    $label = 'text-[13px] font-semibold text-[#111827]';
    $hint  = 'text-xs text-[#6B7280]';
    $wajib = '<span class="text-[#FF3F3F]">*</span>';
    $chev  = '<svg class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-[#6B7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6"/></svg>';
    $cert  = '<svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0-6 0"/><path d="M13 17.5v4.5l2-1.5l2 1.5v-4.5"/><path d="M10 19h-5a2 2 0 0 1-2-2v-10a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-1 1.73"/><path d="M6 9l12 0"/><path d="M6 12l3 0"/><path d="M6 15l2 0"/></svg>';
    $cardCls = 'rounded-xl border border-[#E5E7EB] bg-white shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]';
    $radioCls = 'flex cursor-pointer items-center gap-2.5 text-sm';

    // Pilihan jenis dokumen harus sama dengan validasi di MahasiswaController.
    $jenisDokList = $jenisDokList ?? ['Sertifikat', 'Dokumentasi kegiatan'];
    $selDok = 'h-[38px] w-full appearance-none rounded-lg border border-[#E5E7EB] bg-white pl-3 pr-9 text-sm text-[#0A0A0A] outline-none transition focus:border-[#2277FF] focus:ring-2 focus:ring-[#2277FF]/15';

    // Anggota tim. Kirim $daftarMahasiswa dan $peranList dari controller (cadangan di bawah ini hanya contoh).
    $daftarMahasiswa = $daftarMahasiswa ?? ['Ivan Sigit Santoso', 'Anisa Aulia Nurusyifa', config('mahasiswa.nama'), 'Dewi Lestari', 'Fajar Nugroho', 'Rizky Pratama', 'Salsabila Putri'];
    $peranList       = $peranList ?? ['Ketua', 'Anggota'];

    // Mode edit: controller mengirim $prestasi (data yang sudah ada). Tanpa itu, form dalam mode tambah.
    // Kunci yang dibaca: id, sumber, kegiatan, nama_lomba, penyelenggara, lokasi, dosen, tingkat, capaian, jenis,
    // kategori, tanggal_mulai, tanggal_selesai, bentuk, anggota[[nama, peran]], deskripsi, dokumen[[id, jenis, nama, ukuran, url]]
    $edit        = isset($prestasi);
    $v           = fn (string $f, $default = '') => old($f, $prestasi[$f] ?? $default);
    $dokumenLama = $prestasi['dokumen'] ?? [];
    $dokumenInput = old('dokumen', []);
    $dokumenInput = is_array($dokumenInput) ? array_values($dokumenInput) : [];
    $urlSimpan   = $edit
        ? (Route::has('mahasiswa.prestasi.update') ? route('mahasiswa.prestasi.update', $prestasi['id']) : '#')
        : route('mahasiswa.prestasi.simpan');
@endphp

<div class="flex flex-col gap-6">

    {{-- Judul --}}
    <div class="flex flex-col gap-3">
        <a href="{{ route('mahasiswa.prestasi') }}" class="flex w-fit items-center gap-2 text-sm font-semibold text-[#0062FF] hover:underline">
            <span class="flex size-6 items-center justify-center rounded-full bg-[#EEF4FF]">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6"/></svg>
            </span>
            Kembali ke prestasi
        </a>
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold leading-tight tracking-[-0.2px] sm:text-[32px] sm:leading-[38px]">{{ $edit ? 'Edit prestasi' : 'Tambah prestasi' }}</h1>
            <p class="text-base text-[#6B7280]">{{ $edit ? 'Perbarui informasi pencapaian dan bukti pendukung.' : 'Lengkapi informasi pencapaian dan tautan bukti pendukung.' }}</p>
        </div>
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">

        {{-- ================= FORMULIR ================= --}}
        <form method="POST" action="{{ $urlSimpan }}" novalidate
              x-data="formPrestasi(@js([
                  'sumber'          => $v('sumber', 'mandiri'),
                  'kegiatan'        => $v('kegiatan'),
                  'namaLomba'       => $v('nama_lomba'),
                  'penyelenggara'   => $v('penyelenggara'),
                  'deskripsi'       => $v('deskripsi'),
                  'kegiatanList'    => $kegiatanList,
                  'bentuk'          => $v('bentuk', 'Individu'),
                  'anggota'         => $v('anggota', []),
                  'daftarMahasiswa' => $daftarMahasiswa,
                  'dokumenLama'     => $dokumenLama,
                  'dokumenInput'    => $dokumenInput,
                  'errors'          => $errors->getMessages(),
              ]))"
              x-on:submit="cekKirim($event)"
              class="flex flex-col gap-4">
            @csrf
            @if ($edit)
                @method('PUT')
            @endif

            {{-- Kartu isi formulir --}}
            <div class="{{ $cardCls }} flex flex-col gap-6 p-5 sm:p-6">

                <p class="{{ $hint }}">* Wajib diisi • Kolom bertanda opsional dapat dikosongkan</p>

                {{-- ===== Sumber kegiatan ===== --}}
                <section class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-lg font-semibold">Sumber kegiatan</h2>
                        <p class="text-[13px] text-[#6B7280]">Kegiatan terkait bersifat opsional. Anda tetap dapat mencatat prestasi secara mandiri.</p>
                    </div>

                    <div class="grid gap-2.5 sm:grid-cols-2">
                        <label class="{{ $radioCls }}">
                            <input type="radio" name="sumber" value="sebelumnya" x-model="sumber" class="size-4 accent-[#2277FF]">
                            Dari kegiatan sebelumnya
                        </label>
                        <label class="{{ $radioCls }}">
                            <input type="radio" name="sumber" value="mandiri" x-model="sumber" class="size-4 accent-[#2277FF]">
                            Tanpa kegiatan sebelumnya
                        </label>
                    </div>

                    <div class="flex flex-col gap-2 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] p-3.5 transition-opacity"
                         x-bind:class="sumber === 'mandiri' ? 'opacity-70' : ''">
                        <label for="kegiatan" class="{{ $label }}">Kegiatan sebelumnya (opsional)</label>
                        <div class="relative">
                            <input id="kegiatan" name="kegiatan" list="daftar-kegiatan" autocomplete="off"
                                   x-model="kegiatan" x-on:input="pilihKegiatan()" x-bind:disabled="sumber === 'mandiri'"
                                   placeholder="Cari dan pilih kegiatan"
                                   class="{{ $in('kegiatan') }} pr-10 disabled:cursor-not-allowed disabled:bg-[#F3F4F6]">
                            <svg class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-[#6B7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0-14 0"/><path d="M21 21l-6-6"/></svg>
                            <datalist id="daftar-kegiatan">
                                @foreach ($kegiatanList as $k)
                                    <option value="{{ $k['label'] }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        {!! $galat('kegiatan') !!}
                        <div class="flex flex-col gap-1 {{ $hint }}">
                            <p>Aktif jika memilih 'Dari kegiatan sebelumnya'. Tidak wajib untuk prestasi mandiri.</p>
                            <p>Contoh pilihan: KMIPN VIII 2026 • Lomba</p>
                            <p>Nama lomba dan penyelenggara dapat diambil dari kegiatan terpilih, lalu dilengkapi sesuai sertifikat.</p>
                        </div>
                    </div>
                </section>

                <hr class="border-[#E5E7EB]">

                {{-- ===== Informasi prestasi ===== --}}
                <section class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-lg font-semibold">Informasi prestasi</h2>
                        <p class="text-[13px] text-[#6B7280]">Isikan nama lomba dan capaian sesuai dokumen bukti.</p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="nama_lomba" class="{{ $label }}">Nama lomba {!! $wajib !!}</label>
                        <input id="nama_lomba" name="nama_lomba" x-model="namaLomba" placeholder="Contoh: Techomfest 2026" class="{{ $in('nama_lomba') }}">
                        {!! $galat('nama_lomba') !!}
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="penyelenggara" class="{{ $label }}">Penyelenggara {!! $wajib !!}</label>
                            <input id="penyelenggara" name="penyelenggara" x-model="penyelenggara" placeholder="Nama institusi penyelenggara" class="{{ $in('penyelenggara') }}">
                            {!! $galat('penyelenggara') !!}
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="lokasi" class="{{ $label }}">Lokasi {!! $wajib !!}</label>
                            <input id="lokasi" name="lokasi" value="{{ $v('lokasi') }}" placeholder="Lokasi penyelenggaraan" class="{{ $in('lokasi') }}">
                            {!! $galat('lokasi') !!}
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="dosen" class="{{ $label }}">Dosen pembimbing (opsional)</label>
                            <input id="dosen" name="dosen" value="{{ $v('dosen') }}" placeholder="Nama dosen pembimbing" class="{{ $in('dosen') }}">
                            {!! $galat('dosen') !!}
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="kategori" class="{{ $label }}">Kategori / bidang (opsional)</label>
                            <input id="kategori" name="kategori" value="{{ $v('kategori') }}" placeholder="Contoh: CTF" class="{{ $in('kategori') }}">
                            {!! $galat('kategori') !!}
                        </div>
                    </div>

                    {{-- Tingkat, capaian, jenis --}}
                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach ([
                            ['tingkat', 'Tingkat kejuaraan', 'Pilih tingkat',  $tingkatList, 'Dari kabupaten/kota hingga internasional'],
                            ['capaian', 'Capaian peringkat', 'Pilih capaian',  $capaianList, 'Juara 1, 2, 3, dan seterusnya'],
                            ['jenis',   'Jenis prestasi',    'Pilih jenis',    $jenisList,   'Akademik atau nonakademik'],
                        ] as [$f, $judul, $placeholder, $opsi, $ket])
                            <div class="flex flex-col gap-1.5">
                                <label for="{{ $f }}" class="{{ $label }}">{{ $judul }} {!! $wajib !!}</label>
                                <div class="relative">
                                    <select id="{{ $f }}" name="{{ $f }}" class="{{ $in($f) }} appearance-none pr-9">
                                        <option value="">{{ $placeholder }}</option>
                                        @foreach ($opsi as $o)
                                            <option value="{{ $o }}" @selected($v($f) === $o)>{{ $o }}</option>
                                        @endforeach
                                    </select>
                                    {!! $chev !!}
                                </div>
                                {!! $galat($f) ?: '<span class="' . $hint . '">' . e($ket) . '</span>' !!}
                            </div>
                        @endforeach
                    </div>

                    {{-- Tanggal --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="tanggal_mulai" class="{{ $label }}">Tanggal mulai {!! $wajib !!}</label>
                            <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ $v('tanggal_mulai') }}" class="{{ $in('tanggal_mulai') }}">
                            {!! $galat('tanggal_mulai') ?: '<span class="' . $hint . '">Sesuai bukti prestasi.</span>' !!}
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="tanggal_selesai" class="{{ $label }}">Tanggal selesai {!! $wajib !!}</label>
                            <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ $v('tanggal_selesai') }}" class="{{ $in('tanggal_selesai') }}">
                            {!! $galat('tanggal_selesai') ?: '<span class="' . $hint . '">Sesuaikan dengan tanggal perolehan.</span>' !!}
                        </div>
                    </div>

                    {{-- Bentuk partisipasi --}}
                    <div class="flex flex-col gap-2.5">
                        <span class="{{ $label }}">Bentuk partisipasi {!! $wajib !!}</span>
                        <div class="grid gap-2.5 sm:grid-cols-2">
                            @foreach (['Individu', 'Tim'] as $b)
                                <label class="{{ $radioCls }}">
                                    <input type="radio" name="bentuk" value="{{ $b }}" x-model="bentuk" class="size-4 accent-[#2277FF]">
                                    {{ $b }}
                                </label>
                            @endforeach
                        </div>
                        {!! $galat('bentuk') !!}

                        {{-- ===== Anggota tim (muncul saat memilih "Tim") ===== --}}
                        <div x-show="bentuk === 'Tim'" x-cloak x-transition class="flex flex-col gap-2.5">
                            <span class="{{ $label }}">Anggota {!! $wajib !!}</span>

                            <div class="flex flex-col gap-3 rounded-lg border border-[#E5E7EB] bg-white p-3.5">

                                {{-- Cari nama, pilih peran, tambah --}}
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <div class="relative min-w-0 flex-1" x-on:click.outside="buka = false">
                                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[#A8A8A8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0-14 0"/><path d="M21 21l-6-6"/></svg>
                                        <input type="text" autocomplete="off" placeholder="Cari nama anggota ..."
                                               x-model="cari"
                                               x-on:focus="buka = true"
                                               x-on:input="buka = true; galatAnggota = ''"
                                               x-on:keydown.escape="buka = false"
                                               x-on:keydown.enter.prevent="tambahAnggota()"
                                               class="{{ $in('anggota') }} pl-9">

                                        <ul x-show="buka && hasil.length" x-cloak
                                            class="absolute z-20 mt-1 max-h-52 w-full overflow-auto rounded-lg border border-[#E5E7EB] bg-white py-1 shadow-lg">
                                            <template x-for="n in hasil" x-bind:key="n">
                                                <li>
                                                    <button type="button" x-on:click="pilihNama(n)"
                                                            class="block w-full px-3 py-2 text-left text-sm hover:bg-[#F2F6FD]" x-text="n"></button>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                    <div class="relative sm:w-36">
                                        <select x-model="peran" x-on:change="galatAnggota = ''" aria-label="Peran anggota" class="{{ $selDok }}">
                                            <option value="">Pilih peran</option>
                                            @foreach ($peranList as $p)
                                                <option value="{{ $p }}">{{ $p }}</option>
                                            @endforeach
                                        </select>
                                        {!! $chev !!}
                                    </div>

                                    <button type="button" x-on:click="tambahAnggota()"
                                            class="flex h-[38px] items-center justify-center gap-1.5 rounded-lg bg-[#2277FF] px-4 text-sm font-medium text-white transition hover:bg-[#1A63D9]">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                        Tambah
                                    </button>
                                </div>

                                <p x-show="galatAnggota" x-cloak x-text="galatAnggota" class="-mt-1 text-xs text-[#FF3F3F]"></p>
                                {!! $galat('anggota') !!}

                                {{-- Tabel anggota: baru tampil setelah ada anggota yang ditambahkan --}}
                                <div x-show="anggota.length > 0" x-cloak class="overflow-hidden rounded-lg border border-[#E5E7EB] text-sm text-[#1C2024]">
                                    <div class="hidden grid-cols-[minmax(0,1fr)_120px_64px] bg-[#F9FAFB] text-xs font-medium text-[#6B7280] sm:grid">
                                        <div class="px-3 py-2">Nama</div>
                                        <div class="px-3 py-2">Peran</div>
                                        <div class="px-3 py-2">Aksi</div>
                                    </div>

                                    <template x-for="(a, i) in anggota" x-bind:key="a.id">
                                        <div class="grid grid-cols-[minmax(0,1fr)_120px_64px] items-center border-t border-[#E5E7EB] max-sm:grid-cols-[minmax(0,1fr)_64px]">
                                            <div class="min-w-0 px-3 py-2 max-sm:col-span-2 max-sm:truncate" x-text="a.nama"></div>
                                            <div class="px-3 py-2" x-text="a.peran"></div>
                                            <div class="px-3 py-1.5">
                                                <input type="hidden" x-bind:name="'anggota[' + i + '][nama]'" x-bind:value="a.nama" x-bind:disabled="bentuk !== 'Tim'">
                                                <input type="hidden" x-bind:name="'anggota[' + i + '][peran]'" x-bind:value="a.peran" x-bind:disabled="bentuk !== 'Tim'">
                                                <button type="button" aria-label="Hapus anggota" x-on:click="hapusAnggota(a.id)"
                                                        class="rounded-md bg-[#FBCBCB] p-1.5 text-[#FF3F3F] transition hover:bg-[#F8B4B4]">
                                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12"/><path d="M9 7v-3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="deskripsi" class="{{ $label }}">Deskripsi (opsional)</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="1000" x-model="deskripsi"
                                  placeholder="Jelaskan pencapaian, kontribusi Anda, dan informasi tambahan yang relevan."
                                  class="w-full resize-y rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition placeholder:text-[#A8A8A8] focus:border-[#2277FF] focus:ring-2 focus:ring-[#2277FF]/15 {{ $errors->has('deskripsi') ? 'border-[#FF3F3F]' : 'border-[#E5E7EB]' }}"></textarea>
                        <span class="{{ $hint }}">Maksimal 1.000 karakter • <span x-text="deskripsi.length"></span>/1.000</span>
                        {!! $galat('deskripsi') !!}
                    </div>
                </section>

                <hr class="border-[#E5E7EB]">

                {{-- ===== Sertifikat / bukti (tautan) ===== --}}
                <section class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <h2 class="{{ $label }}">Bukti prestasi {!! $wajib !!}</h2>
                        <p class="text-[13px] text-[#6B7280]">Tempel tautan Google Drive atau OneDrive, pilih jenisnya, lalu klik Tambah. Maksimal 5 dokumen.</p>
                    </div>

                    <div class="flex flex-col gap-3 rounded-lg border border-[#E5E7EB] bg-white p-3.5">
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <div class="relative min-w-0 flex-1">
                                <label for="link-dokumen" class="sr-only">Tautan bukti prestasi</label>
                                <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[#A8A8A8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 15l6 -6"/><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/></svg>
                                <input id="link-dokumen" type="url" inputmode="url" autocomplete="off" spellcheck="false"
                                       x-model.trim="linkBaru"
                                       x-on:input="galatDok = ''"
                                       x-on:keydown.enter.prevent="tambahDok()"
                                       placeholder="https://drive.google.com/... atau https://1drv.ms/..."
                                       class="h-[38px] w-full rounded-lg border bg-white pl-9 pr-3 text-sm text-[#0A0A0A] outline-none transition placeholder:text-[#A8A8A8] focus:border-[#2277FF] focus:ring-2 focus:ring-[#2277FF]/15"
                                       x-bind:class="galatDok ? 'border-[#FF3F3F]' : 'border-[#E5E7EB]'">
                            </div>

                            <div class="relative sm:w-56">
                                <select x-model="jenisBaru" x-on:change="galatDok = ''" aria-label="Jenis dokumen" class="{{ $selDok }}">
                                    <option value="">Pilih jenis dokumen</option>
                                    @foreach ($jenisDokList as $jd)
                                        <option value="{{ $jd }}">{{ $jd }}</option>
                                    @endforeach
                                </select>
                                {!! $chev !!}
                            </div>

                            <button type="button" x-on:click="tambahDok()"
                                    class="flex h-[38px] items-center justify-center gap-1.5 rounded-lg bg-[#2277FF] px-4 text-sm font-medium text-white transition hover:bg-[#1A63D9]">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                Tambah
                            </button>
                        </div>

                        <p x-show="galatDok" x-cloak x-text="galatDok" class="text-xs text-[#FF3F3F]"></p>
                        <p x-show="!galatDok" class="{{ $hint }}">Gunakan tautan HTTPS dan pastikan akses berbagi memungkinkan tautan dibuka.</p>
                        @foreach ($errors->getMessages() as $field => $pesanList)
                            @if (str_starts_with($field, 'dokumen.'))
                                @foreach ($pesanList as $pesan)
                                    <p class="text-xs text-[#FF3F3F]">{{ $pesan }}</p>
                                @endforeach
                            @endif
                        @endforeach
                        {!! $galat('dokumen') !!}
                    </div>

                    {{-- Dokumen tersimpan dan tautan baru ditampilkan dalam satu daftar. --}}
                    <div x-show="dokLama.length + docs.length > 0" x-cloak class="overflow-hidden rounded-lg border border-[#E5E7EB] text-sm text-[#1C2024]">
                        <div class="hidden grid-cols-[minmax(0,1fr)_170px_64px] bg-[#F9FAFB] text-xs font-medium text-[#6B7280] sm:grid">
                            <div class="px-3 py-2">Tautan dokumen</div>
                            <div class="px-3 py-2">Jenis</div>
                            <div class="px-3 py-2">Aksi</div>
                        </div>

                        <template x-for="d in dokLama" x-bind:key="'lama-' + d.id">
                            <div class="grid grid-cols-[minmax(0,1fr)_64px] items-center border-t border-[#E5E7EB] sm:grid-cols-[minmax(0,1fr)_170px_64px]">
                                <div class="min-w-0 px-3 py-2">
                                    <a x-show="d.url" x-bind:href="d.url" target="_blank" rel="noopener"
                                       class="block truncate text-[#0062FF] hover:underline" x-bind:title="d.url" x-text="d.url"></a>
                                    <span x-show="!d.url" class="block truncate" x-text="d.nama"></span>
                                    <span class="mt-1 block text-xs text-[#6B7280] sm:hidden" x-text="d.jenis || 'Dokumen'"></span>
                                </div>
                                <div class="hidden truncate px-3 py-2 sm:block" x-text="d.jenis || 'Dokumen'"></div>
                                <div class="px-3 py-1.5">
                                    <input type="hidden" name="dokumen_pertahankan[]" x-bind:value="d.id">
                                    <button type="button" aria-label="Hapus dokumen tersimpan" x-on:click="hapusLama(d.id)"
                                            class="rounded-md bg-[#FBCBCB] p-1.5 text-[#FF3F3F] transition hover:bg-[#F8B4B4]">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12"/><path d="M9 7v-3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <template x-for="(d, i) in docs" x-bind:key="d.id">
                            <div class="grid grid-cols-[minmax(0,1fr)_64px] items-center border-t border-[#E5E7EB] sm:grid-cols-[minmax(0,1fr)_170px_64px]">
                                <div class="min-w-0 px-3 py-2">
                                    <a x-bind:href="d.url" target="_blank" rel="noopener"
                                       class="block truncate text-[#0062FF] hover:underline" x-bind:title="d.url" x-text="d.url"></a>
                                    <span class="mt-1 block text-xs text-[#6B7280] sm:hidden" x-text="d.jenis"></span>
                                </div>
                                <div class="hidden truncate px-3 py-2 sm:block" x-text="d.jenis"></div>
                                <div class="px-3 py-1.5">
                                    <input type="hidden" x-bind:name="'dokumen[' + i + '][url]'" x-bind:value="d.url">
                                    <input type="hidden" x-bind:name="'dokumen[' + i + '][jenis]'" x-bind:value="d.jenis">
                                    <button type="button" aria-label="Hapus tautan" x-on:click="hapusDok(d.id)"
                                            class="rounded-md bg-[#FBCBCB] p-1.5 text-[#FF3F3F] transition hover:bg-[#F8B4B4]">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12"/><path d="M9 7v-3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <template x-for="id in dihapus" x-bind:key="id">
                        <input type="hidden" name="dokumen_hapus[]" x-bind:value="id">
                    </template>
                </section>
            </div>

            {{-- Tombol aksi: tepat di bawah bagian penambahan dokumen --}}
            <div class="flex justify-end gap-2">
                <a href="{{ route('mahasiswa.prestasi') }}"
                   class="flex h-[38px] items-center rounded-lg bg-[#FF3F3F] px-5 text-sm font-semibold text-white transition hover:bg-[#E03535]">Batal</a>
                <button type="submit"
                        class="flex h-[38px] items-center rounded-lg bg-[#2277FF] px-5 text-sm font-semibold text-white transition hover:bg-[#1A63D9]">{{ $edit ? 'Simpan perubahan' : 'Simpan' }}</button>
            </div>
        </form>

        {{-- ================= PANEL SAMPING ================= --}}
        <aside class="flex flex-col gap-4 lg:sticky lg:top-24">
            <div class="{{ $cardCls }} flex flex-col gap-4 p-5">
                <h2 class="text-lg font-semibold">Panduan pengisian</h2>
                @foreach ([
                    ['Kegiatan tidak wajib',      'Pilih Tanpa kegiatan sebelumnya untuk mengisi lomba dan penyelenggara secara mandiri.'],
                    ['Gunakan data sesuai bukti', 'Nama prestasi, peringkat, dan tanggal harus sesuai sertifikat atau dokumen pendukung.'],
                    ['Tentukan tingkat dan jenis', 'Pilih cakupan lomba dan kategori akademik atau nonakademik.'],
                ] as [$judul, $isi])
                    <div class="flex flex-col gap-1">
                        <h3 class="text-[13px] font-semibold text-[#111827]">{{ $judul }}</h3>
                        <p class="text-[13px] leading-5 text-[#6B7280]">{{ $isi }}</p>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col gap-2 rounded-xl bg-[#EEF4FF] p-4 text-[#003385]">
                <span class="text-[#0162DD]">{!! $cert !!}</span>
                <h3 class="text-sm font-semibold">Bukti yang dapat digunakan</h3>
                <p class="text-[13px] leading-5">Sertifikat, piagam penghargaan, atau surat resmi penyelenggara yang memuat identitas dan capaian.</p>
            </div>
        </aside>
    </div>
</div>

<script>
    // Dideklarasikan global supaya bisa dipakai x-data (Alpine dimuat setelah script ini).
    function formPrestasi(cfg) {
        return {
            sumber: cfg.sumber,
            kegiatan: cfg.kegiatan,
            namaLomba: cfg.namaLomba,
            penyelenggara: cfg.penyelenggara,
            deskripsi: cfg.deskripsi,
            kegiatanList: cfg.kegiatanList,

            // ----- Bentuk partisipasi & anggota tim -----
            bentuk: cfg.bentuk,
            daftarMahasiswa: cfg.daftarMahasiswa || [],
            anggota: (cfg.anggota || []).map((a, i) => ({ id: i + 1, nama: a.nama ?? '', peran: a.peran ?? '' })),
            nextAnggota: (cfg.anggota || []).length + 1,
            cari: '',
            peran: '',
            buka: false,
            galatAnggota: '',

            // Saran nama: yang cocok dengan ketikan dan belum ditambahkan.
            get hasil() {
                const q = this.cari.trim().toLowerCase();
                return this.daftarMahasiswa
                    .filter(n => !this.anggota.some(a => a.nama.toLowerCase() === n.toLowerCase()))
                    .filter(n => !q || n.toLowerCase().includes(q))
                    .slice(0, 6);
            },

            pilihNama(nama) {
                this.cari = nama;
                this.buka = false;
                this.galatAnggota = '';
            },

            tambahAnggota() {
                const nama = this.cari.trim();
                if (!nama) {
                    this.galatAnggota = 'Cari dan pilih nama anggota terlebih dahulu.';
                    return;
                }
                if (!this.peran) {
                    this.galatAnggota = 'Pilih peran anggota.';
                    return;
                }
                if (this.anggota.some(a => a.nama.toLowerCase() === nama.toLowerCase())) {
                    this.galatAnggota = 'Anggota tersebut sudah ditambahkan.';
                    return;
                }
                this.anggota.push({ id: this.nextAnggota++, nama, peran: this.peran });
                this.cari = '';
                this.peran = '';
                this.buka = false;
                this.galatAnggota = '';
            },

            hapusAnggota(id) {
                this.anggota = this.anggota.filter(a => a.id !== id);
            },

            // ----- Dokumen (tautan) -----
            maxDok: 5,
            dokLama: (cfg.dokumenLama || []).map(d => ({ ...d })),
            dihapus: [],
            docs: (cfg.dokumenInput || []).map((d, i) => ({
                    id: i + 1,
                    url: d.url || '',
                    jenis: d.jenis || '',
                    galat: (cfg.errors[`dokumen.${i}.url`] || [])[0] || '',
                    galatJenis: (cfg.errors[`dokumen.${i}.jenis`] || [])[0] || '',
                })),
            nextId: (cfg.dokumenInput || []).length + 1,
            linkBaru: '',
            jenisBaru: '',
            galatDok: '',

            // Jika kegiatan yang dipilih ada di daftar, isi nama lomba dan penyelenggara otomatis.
            pilihKegiatan() {
                const k = this.kegiatanList.find(x => x.label === this.kegiatan);
                if (k) {
                    this.namaLomba = k.nama;
                    this.penyelenggara = k.penyelenggara;
                }
            },

            linkValid(link) {
                try {
                    const url = new URL(link);
                    const host = url.hostname.toLowerCase();
                    const domainDiizinkan = [
                        'drive.google.com',
                        'docs.google.com',
                        '1drv.ms',
                        'onedrive.live.com',
                    ].includes(host) || host.endsWith('.sharepoint.com');

                    return url.protocol === 'https:' && domainDiizinkan;
                } catch {
                    return false;
                }
            },

            tambahDok() {
                const url = this.linkBaru.trim();
                this.galatDok = '';

                if (!url) {
                    this.galatDok = 'Isi tautan dokumen terlebih dahulu.';
                    return;
                }
                if (!this.linkValid(url)) {
                    this.galatDok = 'Gunakan tautan HTTPS Google Drive atau OneDrive yang valid.';
                    return;
                }
                if (!this.jenisBaru) {
                    this.galatDok = 'Pilih jenis dokumen.';
                    return;
                }
                if ([...this.docs, ...this.dokLama].some(d => d.url && d.url.toLowerCase() === url.toLowerCase())) {
                    this.galatDok = 'Tautan tersebut sudah ditambahkan.';
                    return;
                }
                if (this.docs.length + this.dokLama.length >= this.maxDok) {
                    this.galatDok = 'Maksimal ' + this.maxDok + ' dokumen.';
                    return;
                }

                this.docs.push({ id: this.nextId++, url, jenis: this.jenisBaru });
                this.linkBaru = '';
                this.jenisBaru = '';
            },

            hapusDok(id) {
                this.docs = this.docs.filter(d => d.id !== id);
                this.galatDok = '';
            },

            // Dokumen yang sudah tersimpan: ditandai untuk dihapus saat form disimpan.
            hapusLama(id) {
                this.dokLama = this.dokLama.filter(d => d.id !== id);
                this.dihapus.push(id);
                this.galatDok = '';
            },

            // Validasi sebelum kirim: minimal satu bukti, dan tautan baru harus ditambahkan ke daftar.
            cekKirim(e) {
                let valid = true;

                if (this.bentuk === 'Tim' && this.anggota.length === 0) {
                    this.galatAnggota = 'Tambahkan minimal satu anggota tim.';
                    valid = false;
                }

                if (this.dokLama.length + this.docs.length === 0) {
                    this.galatDok = 'Tambahkan minimal satu tautan bukti prestasi.';
                    valid = false;
                }

                if (this.linkBaru.trim() !== '' || this.jenisBaru !== '') {
                    this.galatDok = 'Klik Tambah untuk memasukkan tautan ke daftar, atau kosongkan kolom tautan dan jenis.';
                    valid = false;
                }

                if (!valid) e.preventDefault();
            },
        };
    }
</script>
@endsection