@extends('layouts.app')

@section('title', 'Ajukan Kegiatan')

@php
    // DATA TIRUAN. Nanti diganti data dari controller / database oleh tim backend.
    $daftarKategori = ['Lomba', 'Magang', 'Kunjungan Industri', 'Seminar', 'Pelatihan'];
    $daftarJenis    = ['Kegiatan dosen', 'Kegiatan Prodi', 'Dosen dan mahasiswa', 'Kegiatan mahasiswa'];

    // Calon anggota yang bisa dicari: nama => status
    $kandidat = [
        'Kuwat Santoso , S.T., M.Kom.' => 'Dosen',
        'Wiktasari'                    => 'Dosen',
        'Angga Wahyu Wibowo'           => 'Dosen',
        'Arif Setyawan'                => 'Dosen',
        'Mukhlis Pratama Mulya'        => 'Mahasiswa',
    ];

    // Pengaju = akun yang login. Nanti diganti nama user dari backend.
    $pengaju = 'Kuwat Santoso , S.T., M.Kom.';

    $inputClass   = 'h-11 w-full rounded-lg border border-gray-200 bg-white px-3.5 text-[15px] text-gray-900 placeholder:text-gray-400';
    $selectClass  = 'h-11 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-10 pl-3.5 text-[15px] text-gray-900';
    $labelClass   = 'text-[15px] font-medium';
    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
    $chevron      = '<svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
@endphp

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-3xl font-bold tracking-tight">Ajukan Kegiatan</h1>
        <p class="text-2xl text-gray-400">Dashboard / Kegiatan / Ajukan</p>
    </div>

    {{--
        Form ini memakai Alpine.js (lihat layouts/app.blade.php) untuk hal-hal kecil:
        anggota dan dokumen bisa ditambah / dihapus, tanggal selesai tidak boleh sebelum tanggal mulai.
        enctype="multipart/form-data" wajib supaya file bisa dikirim ke backend.
        Atribut name="..." sudah disiapkan supaya backend nanti tinggal membaca datanya.
    --}}
    <form x-data="formKegiatan(@js($kandidat), @js($pengaju))" x-on:submit.prevent="simpan()"
          method="POST" action="#" enctype="multipart/form-data"
          class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">
        @csrf

        {{-- 1. INFORMASI KEGIATAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-info">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <h2 id="h-info" class="text-[15px] font-semibold">Informasi Kegiatan</h2>
            </div>

            <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="kategori" class="{{ $labelClass }}">Kategori</label>
                    <div class="relative">
                        <select id="kategori" name="kategori" required class="{{ $selectClass }}">
                            <option value="">Pilih kategori</option>
                            @foreach ($daftarKategori as $k)
                                <option>{{ $k }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="jenis" class="{{ $labelClass }}">Jenis kegiatan</label>
                    <div class="relative">
                        <select id="jenis" name="jenis_kegiatan" required class="{{ $selectClass }}">
                            <option value="">Pilih jenis kegiatan</option>
                            @foreach ($daftarJenis as $j)
                                <option>{{ $j }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>

                <div class="flex flex-col gap-2 md:col-span-2">
                    <label for="nama" class="{{ $labelClass }}">Nama kegiatan</label>
                    <input id="nama" name="nama_kegiatan" type="text" required
                           placeholder="Seminar nasional AI 2026" class="{{ $inputClass }}">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="penyelenggara" class="{{ $labelClass }}">Penyelenggara</label>
                    <input id="penyelenggara" name="penyelenggara" type="text" required
                           placeholder="Nama institusi" class="{{ $inputClass }}">
                </div>
                <div class="flex flex-col gap-2">
                    <label for="tahun" class="{{ $labelClass }}">Tahun penyelenggaraan</label>
                    <div class="relative">
                        <select id="tahun" name="tahun_penyelenggaraan" class="{{ $selectClass }}">
                            @foreach ([2024, 2025, 2026, 2027] as $t)
                                <option value="{{ $t }}" @selected($t === 2026)>{{ $t }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="mulai" class="{{ $labelClass }}">Tanggal mulai</label>
                    <input id="mulai" name="tanggal_mulai" type="date" required x-model="mulai" class="{{ $inputClass }}">
                </div>
                <div class="flex flex-col gap-2">
                    <label for="selesai" class="{{ $labelClass }}">Tanggal selesai</label>
                    {{-- tidak boleh sebelum tanggal mulai --}}
                    <input id="selesai" name="tanggal_selesai" type="date" required x-model="selesai" :min="mulai" class="{{ $inputClass }}">
                </div>
            </div>
        </section>

        {{-- 2. ANGGOTA KEGIATAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-anggota">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">2</span>
                <h2 id="h-anggota" class="text-[15px] font-semibold">Anggota Kegiatan</h2>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="relative min-w-56 flex-[1_1_260px]">
                    <label for="cari" class="sr-only">Cari dosen atau mahasiswa</label>
                    <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
                    <input id="cari" type="text" list="daftar-kandidat" x-model="cari"
                           x-on:keydown.enter.prevent="tambah()"
                           placeholder="Cari dosen atau mahasiswa..." class="{{ $inputClass }} pl-10">
                    <datalist id="daftar-kandidat">
                        @foreach ($kandidat as $nama => $status)
                            <option value="{{ $nama }}">{{ $status }}</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="relative w-full sm:w-44">
                    <label for="status-anggota" class="sr-only">Status anggota</label>
                    <select id="status-anggota" x-model="status" class="{{ $selectClass }}">
                        <option value="">Pilih status</option>
                        <option>Dosen</option>
                        <option>Mahasiswa</option>
                    </select>
                    {!! $chevron !!}
                </div>

                <button type="button" x-on:click="tambah()"
                        class="flex h-11 cursor-pointer items-center gap-2 rounded-lg bg-brand px-5 text-base font-medium text-white hover:bg-blue-700">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Anggota
                </button>
            </div>

            <template x-if="errorAnggota">
                <p role="alert" class="text-sm text-red-600" x-text="errorAnggota"></p>
            </template>

            <div class="overflow-x-auto rounded-md border border-gray-300">
                <table class="w-full min-w-[480px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">Nama Anggota</th>
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">Status</th>
                            <th scope="col" class="w-1/3 px-2.5 py-2.5 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(a, i) in anggota" :key="a.nama">
                            <tr class="border-t border-gray-300">
                                <td class="px-2.5 py-2.5">
                                    <span x-text="a.nama"></span>
                                    {{-- data yang dikirim ke backend --}}
                                    <input type="hidden" :name="`anggota[${i}][nama]`" :value="a.nama">
                                    <input type="hidden" :name="`anggota[${i}][status]`" :value="a.status">
                                </td>
                                <td class="px-2.5 py-2.5" x-text="a.status"></td>
                                <td class="px-2.5 py-1.5">
                                    {{-- akun yang login (pengaju) tidak bisa dihapus --}}
                                    <template x-if="!a.kunci">
                                        <button type="button" x-on:click="hapus(i)" :aria-label="`Hapus ${a.nama}`"
                                                class="flex size-8 cursor-pointer items-center justify-center rounded-lg bg-red-100 text-red-500 hover:bg-red-200">
                                            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 7h14M10 7V5h4v2M7 7l1 12h8l1-12M10 11v5M14 11v5"/></svg>
                                        </button>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 3. UNGGAH DOKUMEN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-dokumen">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">3</span>
                <h2 id="h-dokumen" class="text-[15px] font-semibold">Unggah Dokumen</h2>
            </div>

            <template x-for="(d, i) in dokumen" :key="d.id">
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between gap-3 bg-[#02215c] px-3.5 py-3 text-white">
                        <h3 class="text-sm font-semibold" x-text="`Dokumen ${i + 1}`"></h3>
                        {{-- dokumen pertama tidak bisa dihapus --}}
                        <template x-if="i > 0">
                            <button type="button" x-on:click="hapusDokumen(i)" :aria-label="`Hapus Dokumen ${i + 1}`"
                                    class="flex cursor-pointer items-center gap-1.5 rounded-md bg-red-500 px-3.5 py-1.5 text-sm font-medium text-white hover:bg-red-600">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 7h14M10 7V5h4v2M7 7l1 12h8l1-12M10 11v5M14 11v5"/></svg>
                                Hapus
                            </button>
                        </template>
                    </div>

                    <div class="flex flex-col gap-4 p-3.5">
                        <div class="flex flex-col gap-2">
                            <span class="{{ $labelClass }}">File</span>
                            <label class="flex cursor-pointer flex-col items-center gap-2 rounded-md border border-dashed border-brand bg-white px-4 py-5 text-center hover:bg-blue-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand">
                                <input type="file" class="sr-only" :name="`dokumen[${i}][file]`"
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt"
                                       x-on:change="pilihFile($event, d)">
                                <span class="flex items-center gap-2 text-[13px] font-medium text-brand">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 18a4.5 4.5 0 0 1-.6-8.96A6 6 0 0 1 18 8.5a4 4 0 0 1-.5 7.97M12 20v-8M9 15l3-3 3 3"/></svg>
                                    <span x-text="d.namaFile || 'Pilih file dari perangkat'"></span>
                                </span>
                                <span class="text-xs text-gray-500">pdf, jpg, jpeg, png, doc, docx, xls, xlsx, txt, maksimal 5 MB.</span>
                            </label>
                            <template x-if="d.errorFile">
                                <p role="alert" class="text-sm text-red-600" x-text="d.errorFile"></p>
                            </template>
                        </div>

                        <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label :for="`nama-${d.id}`" class="{{ $labelClass }}">Nama dokumen</label>
                                <input :id="`nama-${d.id}`" :name="`dokumen[${i}][nama]`" type="text" required x-model="d.nama"
                                       placeholder="Contoh : Proposal kegiatan" class="{{ $inputClass }}">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label :for="`jenis-${d.id}`" class="{{ $labelClass }}">Jenis dokumen</label>
                                <div class="relative">
                                    <select :id="`jenis-${d.id}`" :name="`dokumen[${i}][jenis]`" required x-model="d.jenis" class="{{ $selectClass }}">
                                        <option value="">Pilih jenis dokumen</option>
                                        <option>Proposal</option>
                                        <option>Surat Tugas</option>
                                        <option>Sertifikat</option>
                                        <option>Laporan</option>
                                        <option>Dokumentasi</option>
                                        <option>Lainnya</option>
                                    </select>
                                    {!! $chevron !!}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label :for="`ket-${d.id}`" class="{{ $labelClass }}">Keterangan</label>
                            <textarea :id="`ket-${d.id}`" :name="`dokumen[${i}][keterangan]`" rows="3" x-model="d.keterangan"
                                      placeholder="Keterangan singkat tentang dokumen ini"
                                      class="w-full resize-y rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-[15px] text-gray-900 placeholder:text-gray-400"></textarea>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label :for="`tautan-${d.id}`" class="{{ $labelClass }}">Tautan dokumen (opsional)</label>
                            <input :id="`tautan-${d.id}`" :name="`dokumen[${i}][tautan]`" type="url" x-model="d.tautan"
                                   placeholder="https://" class="{{ $inputClass }}">
                        </div>
                    </div>
                </div>
            </template>

            <div>
                <button type="button" x-on:click="tambahDokumen()"
                        class="inline-flex h-10 cursor-pointer items-center gap-2 rounded-md border border-brand bg-white px-4 text-[15px] text-brand hover:bg-blue-50">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah dokumen
                </button>
            </div>
        </section>

        {{-- TOMBOL --}}
        <div class="flex flex-wrap items-center justify-end gap-3 pt-4">
            <template x-if="tersimpan">
                <p role="status" class="mr-auto rounded-lg border border-green-200 bg-green-50 px-3.5 py-2.5 text-sm text-green-800">
                    Tersimpan (simulasi). Data belum masuk database.
                </p>
            </template>
            <a href="{{ route('kegiatan.index') }}"
               class="flex h-11 items-center rounded-md bg-red-500 px-6 text-base text-white hover:bg-red-600">Batal</a>
            <button type="submit"
                    class="h-11 cursor-pointer rounded-md bg-brand px-6 text-base text-white hover:bg-blue-700">Simpan</button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formKegiatan', (kandidat, pengaju) => ({
            mulai: '', selesai: '',

            // Anggota: akun yang login (pengaju) selalu ada dan terkunci. Anggota lain kosong di awal.
            anggota: [
                { nama: pengaju, status: kandidat[pengaju] || 'Dosen', kunci: true },
            ],
            cari: '',
            status: '',
            errorAnggota: '',

            // Kalau nama yang diketik ada di daftar, status otomatis terisi (masih bisa diubah)
            init() {
                this.$watch('cari', v => {
                    const nama = Object.keys(kandidat).find(n => n.toLowerCase() === v.trim().toLowerCase());
                    if (nama) this.status = kandidat[nama];
                });
            },

            tambah() {
                const nama = this.cari.trim();
                if (!nama) {
                    this.errorAnggota = 'Ketik nama dosen atau mahasiswa terlebih dahulu.';
                    return;
                }
                if (!this.status) {
                    this.errorAnggota = 'Pilih status anggota (Dosen atau Mahasiswa) terlebih dahulu.';
                    return;
                }
                if (this.anggota.some(a => a.nama.toLowerCase() === nama.toLowerCase())) {
                    this.errorAnggota = 'Anggota ini sudah ada di daftar.';
                    return;
                }
                this.anggota.push({ nama, status: this.status, kunci: false });
                this.cari = '';
                this.status = '';
                this.errorAnggota = '';
            },
            hapus(i) { this.anggota.splice(i, 1); },

            // Dokumen. Dokumen 1 selalu ada, sisanya bisa ditambah / dihapus.
            nextId: 2,
            dokumen: [
                { id: 1, namaFile: '', errorFile: '', nama: '', jenis: '', keterangan: '', tautan: '' },
            ],
            tambahDokumen() {
                this.dokumen.push({ id: this.nextId++, namaFile: '', errorFile: '', nama: '', jenis: '', keterangan: '', tautan: '' });
            },
            hapusDokumen(i) { this.dokumen.splice(i, 1); },
            pilihFile(event, d) {
                const file = event.target.files[0];
                d.errorFile = '';
                if (!file) { d.namaFile = ''; return; }
                if (file.size > 5 * 1024 * 1024) {
                    // terlalu besar: batalkan pilihan file
                    event.target.value = '';
                    d.namaFile = '';
                    d.errorFile = 'Ukuran file melebihi 5 MB.';
                    return;
                }
                d.namaFile = file.name;
            },

            // Simpan (simulasi)
            tersimpan: false,
            simpan() { this.tersimpan = true; },
        }));
    });
</script>
@endpush