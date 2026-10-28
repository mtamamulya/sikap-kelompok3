@extends('layouts.app')

@section('title', 'Edit Pengajaran')

@php
    // DATA TIRUAN. $id berasal dari alamat (/pengajaran/{id}/edit).
    // Nanti diganti data pengajaran dari controller / database oleh tim backend.
    $daftarTahunAkademik = ['2025/2026 Ganjil', '2025/2026 Genap', '2024/2025 Ganjil', '2024/2025 Genap'];
    $daftarProdi  = ['D3 Teknik Informatika', 'D4 Teknik Informatika', 'D4 Teknologi Rekayasa Komputer', 'D3 Teknik Elektronika', 'D4 Teknik Telekomunikasi'];
    $daftarKelas  = ['TI-1A', 'TI-1B', 'TI-1C', 'TI-2A', 'TI-2B', 'TI-2C', 'TI-3A', 'TI-3B', 'TI-3C'];
    $daftarMatkul = ['Kecerdasan Buatan', 'Jaringan Komputer', 'Sistem Basis Data', 'Internet of Things', 'Computer Vision', 'Sistem Operasi'];

    // Dosen pengampu = akun yang login. Nanti diganti nama user dari backend.
    $dosenLogin = 'Kuwat Santoso';

    $inputClass   = 'h-11 w-full rounded-lg border border-gray-200 bg-white px-3.5 text-[15px] text-gray-900 placeholder:text-gray-400';
    $selectClass  = 'h-11 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-10 pl-3.5 text-[15px] text-gray-900';
    $lockedClass  = 'h-11 w-full rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3.5 text-[15px] text-gray-500';
    $labelClass   = 'text-[15px] font-medium';
    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
    $chevron      = '<svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
@endphp

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-3xl font-bold tracking-tight">Edit Pengajaran</h1>
        <p class="text-2xl text-gray-400">Dashboard / Pengajaran / Edit Pengajaran</p>
    </div>

    {{--
        Form ini memakai Alpine.js (lihat layouts/app.blade.php) untuk hal-hal kecil:
        Semester terisi otomatis dari Tahun akademik, Ringkasan ikut berubah, dokumen bisa ditambah / dihapus. Form ini terisi data pengajaran yang diedit.
        enctype="multipart/form-data" wajib supaya file bisa dikirim ke backend.
        Atribut name="..." sudah disiapkan supaya backend nanti tinggal membaca datanya.
    --}}
    <form x-data="formEditPengajaran()" x-on:submit.prevent="simpan()"
          method="POST" action="#" enctype="multipart/form-data"
          class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">
        @csrf
        @method('PUT')

        {{-- 1. KELAS DAN PERIODE --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-kelas">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <h2 id="h-kelas" class="text-[15px] font-semibold">Kelas dan Periode</h2>
            </div>

            <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="tahunAkademik" class="{{ $labelClass }}">Tahun akademik</label>
                    <div class="relative">
                        <select id="tahunAkademik" name="tahun_akademik" required x-model="tahunAkademik" class="{{ $selectClass }}">
                            @foreach ($daftarTahunAkademik as $t)
                                <option value="{{ $t }}" @selected($loop->first)>{{ str_replace('/', ' / ', $t) }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="semester" class="{{ $labelClass }}">Semester</label>
                    {{-- terisi otomatis dari Tahun akademik, tidak bisa diketik --}}
                    <input id="semester" name="semester" type="text" readonly :value="semester"
                           placeholder="Terisi otomatis" class="{{ $lockedClass }}">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="prodi" class="{{ $labelClass }}">Program studi</label>
                    <div class="relative">
                        <select id="prodi" name="program_studi" required x-model="prodi" class="{{ $selectClass }}">
                            <option value="">Pilih program studi</option>
                            @foreach ($daftarProdi as $p)
                                <option>{{ $p }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="kelas" class="{{ $labelClass }}">Kelas</label>
                    <div class="relative">
                        <select id="kelas" name="kelas" required x-model="kelas" class="{{ $selectClass }}">
                            <option value="">Pilih kelas</option>
                            @foreach ($daftarKelas as $k)
                                <option>{{ $k }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. MATA KULIAH DAN DOSEN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-matkul">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">2</span>
                <h2 id="h-matkul" class="text-[15px] font-semibold">Mata Kuliah dan Dosen</h2>
            </div>

            <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="matkul" class="{{ $labelClass }}">Mata kuliah</label>
                    <div class="relative">
                        <select id="matkul" name="mata_kuliah" required x-model="matkul" class="{{ $selectClass }}">
                            <option value="">Pilih mata kuliah</option>
                            @foreach ($daftarMatkul as $m)
                                <option>{{ $m }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="dosen" class="{{ $labelClass }}">Dosen pengampu</label>
                    <div class="relative">
                        {{-- select yang di-disable tidak ikut terkirim, jadi nilainya dikirim lewat input hidden --}}
                        <select id="dosen" disabled aria-describedby="dosen-info"
                                class="h-11 w-full appearance-none rounded-lg border border-dashed border-gray-300 bg-gray-50 pr-10 pl-3.5 text-[15px] text-gray-700">
                            <option>{{ $dosenLogin }} (akun Anda)</option>
                        </select>
                        <input type="hidden" name="dosen_pengampu" value="{{ $dosenLogin }}">
                        {!! $chevron !!}
                    </div>
                </div>
            </div>
            <p id="dosen-info" class="text-[15px] text-gray-400">Dosen pengampu terisi otomatis dari akun yang login.</p>
        </section>

        {{-- RINGKASAN: ikut berubah saat pilihan di atas diisi --}}
        <div role="status" class="flex items-center gap-2.5 rounded-lg bg-blue-100 px-4 py-3.5 text-sm text-brand">
            <svg class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5.5M12 7.5v.5"/></svg>
            <span>Ringkasan : <span x-text="ringkasan"></span></span>
        </div>

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
                                {{-- nama file lama, supaya backend tahu file lama dipertahankan kalau tidak ada file baru --}}
                                <input type="hidden" :name="`dokumen[${i}][file_lama]`" :value="d.fileLama">
                                <span class="flex items-center gap-2 text-[13px] font-medium text-brand">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 18a4.5 4.5 0 0 1-.6-8.96A6 6 0 0 1 18 8.5a4 4 0 0 1-.5 7.97M12 20v-8M9 15l3-3 3 3"/></svg>
                                    <span x-text="d.namaFile || 'Pilih file dari perangkat'"></span>
                                </span>
                                <span class="text-xs text-gray-500"
                                      x-text="d.fileLama && d.namaFile === d.fileLama ? 'File saat ini. Klik untuk mengganti dengan file lain (maksimal 5 MB).' : 'pdf, jpg, jpeg, png, doc, docx, xls, xlsx, txt, maksimal 5 MB.'"></span>
                            </label>
                            <template x-if="d.errorFile">
                                <p role="alert" class="text-sm text-red-600" x-text="d.errorFile"></p>
                            </template>
                        </div>

                        <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label :for="`nama-${d.id}`" class="{{ $labelClass }}">Nama dokumen</label>
                                <input :id="`nama-${d.id}`" :name="`dokumen[${i}][nama]`" type="text" required x-model="d.nama"
                                       placeholder="Contoh : Proposal pengabdian" class="{{ $inputClass }}">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label :for="`jenis-${d.id}`" class="{{ $labelClass }}">Jenis dokumen</label>
                                <div class="relative">
                                    <select :id="`jenis-${d.id}`" :name="`dokumen[${i}][jenis]`" required x-model="d.jenis" class="{{ $selectClass }}">
                                        <option value="">Pilih jenis dokumen</option>
                                        <option>RPS</option>
                                        <option>Materi Ajar</option>
                                        <option>Soal Ujian</option>
                                        <option>Surat Tugas</option>
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
                    Perubahan tersimpan (simulasi). Data belum masuk database.
                </p>
            </template>
            <a href="{{ route('pengajaran.index') }}"
               class="flex h-11 items-center rounded-md bg-red-500 px-6 text-base text-white hover:bg-red-600">Batal</a>
            <button type="submit"
                    class="h-11 cursor-pointer rounded-md bg-brand px-6 text-base text-white hover:bg-blue-700">Simpan</button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formEditPengajaran', () => ({
            tahunAkademik: @js($daftarTahunAkademik[0]),
            // Nilai awal = data pengajaran yang sedang diedit (data tiruan)
            prodi: 'D4 Teknologi Rekayasa Komputer', kelas: 'TI-2B', matkul: 'Jaringan Komputer',

            // Semester = kata terakhir dari Tahun akademik ("2025/2026 Ganjil" => "Ganjil")
            get semester() { return this.tahunAkademik.split(' ').pop(); },

            // Ringkasan: gabungan pilihan yang sudah diisi
            get ringkasan() {
                if (!this.prodi && !this.kelas && !this.matkul) return '-';
                return [
                    this.matkul,
                    this.kelas && `Kelas ${this.kelas}`,
                    this.prodi,
                    this.tahunAkademik,
                ].filter(Boolean).join(' · ');
            },

            // Dokumen. Dokumen 1 sudah tersimpan (data tiruan), sisanya bisa ditambah / dihapus.
            // fileLama = nama file yang sudah ada di server. Kalau tidak memilih file baru, file lama tetap dipakai.
            nextId: 2,
            dokumen: [
                { id: 1, fileLama: 'SK Pengajaran.pdf', namaFile: 'SK Pengajaran.pdf', errorFile: '',
                  nama: 'Surat Tugas Mengajar', jenis: 'Surat Tugas',
                  keterangan: 'Ini adalah surat mengajar selama 1 semester', tautan: '' },
            ],
            tambahDokumen() {
                this.dokumen.push({ id: this.nextId++, fileLama: '', namaFile: '', errorFile: '', nama: '', jenis: '', keterangan: '', tautan: '' });
            },
            hapusDokumen(i) { this.dokumen.splice(i, 1); },
            pilihFile(event, d) {
                const file = event.target.files[0];
                d.errorFile = '';
                if (!file) { d.namaFile = d.fileLama; return; }
                if (file.size > 5 * 1024 * 1024) {
                    // terlalu besar: batalkan pilihan file
                    event.target.value = '';
                    d.namaFile = d.fileLama;
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