@extends('layouts.app')

@section('title', 'Tambah Penelitian')

@php
    $inputClass  = 'h-11 w-full rounded-lg border border-gray-200 bg-white px-3.5 text-[15px] text-gray-900 placeholder:text-gray-400';
    $selectClass = 'h-11 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-10 pl-3.5 text-[15px] text-gray-900';
    $labelClass  = 'text-[15px] font-medium';
    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass    = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
@endphp

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-4xl font-bold tracking-tight">Tambah Penelitian</h1>
        <p class="text-2xl text-gray-400">Dashboard / Penelitian / Tambah</p>
    </div>

    {{--
        Form ini memakai Alpine.js (lihat layouts/app.blade.php) untuk hal-hal kecil:
        Ranah terisi otomatis dari Skim, dan daftar anggota bisa ditambah / dihapus.
        Atribut name="..." sudah disiapkan supaya backend nanti tinggal membaca datanya.
    --}}
    <form x-data="formPenelitian()" x-on:submit.prevent="simpan()"
          method="POST" action="#"
          class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">
        @csrf

        {{-- 1. INFORMASI PENELITIAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-info">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <h2 id="h-info" class="text-[15px] font-semibold">Informasi Penelitian</h2>
            </div>

            <div class="flex flex-col gap-2">
                <label for="judul" class="{{ $labelClass }}">Judul penelitian</label>
                <input id="judul" name="judul" type="text" required x-model="judul"
                       placeholder="Deteksi hoaks dengan machine learning" class="{{ $inputClass }}">
            </div>

            <div class="flex flex-col gap-2">
                <label for="afiliasi" class="{{ $labelClass }}">Afiliasi</label>
                <input id="afiliasi" name="afiliasi" type="text" required x-model="afiliasi"
                       placeholder="Program Studi / Jurusan / Perguruan Tinggi" class="{{ $inputClass }}">
            </div>

            <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="skim" class="{{ $labelClass }}">Skim</label>
                    <div class="relative">
                        <select id="skim" name="skim" required x-model="skim" class="{{ $selectClass }}">
                            <option value="">Pilih skim</option>
                            <template x-for="nama in Object.keys(ranahMap)" :key="nama">
                                <option :value="nama" x-text="nama"></option>
                            </template>
                        </select>
                        <svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="ranah" class="{{ $labelClass }}">Ranah</label>
                    <input id="ranah" name="ranah" type="text" readonly x-model="ranah"
                           placeholder="Terisi otomatis dari skim"
                           class="{{ $inputClass }} bg-gray-50 text-gray-500">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="bidang" class="{{ $labelClass }}">Kelompok bidang</label>
                    <div class="relative">
                        <select id="bidang" name="kelompok_bidang" required x-model="bidang" class="{{ $selectClass }}">
                            <option value="">Pilih kelompok bidang</option>
                            <option>Teknologi Informasi</option>
                            <option>Energi</option>
                            <option>Pendidikan</option>
                            <option>Kesehatan</option>
                            <option>Pertanian</option>
                        </select>
                        <svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="sebelumnya" class="{{ $labelClass }}">Penelitian sebelumnya (opsional)</label>
                    <div class="relative">
                        <select id="sebelumnya" name="penelitian_sebelumnya" x-model="sebelumnya"
                                x-on:change="isiDariSebelumnya()" class="{{ $selectClass }}">
                            <option value="">Tidak ada, Penelitian baru</option>
                            <option value="p1">Deteksi hoaks dengan ML (2025)</option>
                            <option value="p2">Analisis sentimen ulasan aplikasi kampus (2024)</option>
                        </select>
                        <svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. PELAKSANAAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-laksana">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">2</span>
                <h2 id="h-laksana" class="text-[15px] font-semibold">Pelaksanaan</h2>
            </div>

            <div class="grid grid-cols-1 gap-4.5 md:grid-cols-3">
                <div class="flex flex-col gap-2">
                    <label for="tahun" class="{{ $labelClass }}">Tahun anggaran</label>
                    <div class="relative">
                        <select id="tahun" name="tahun_anggaran" class="{{ $selectClass }}">
                            @foreach ([2024, 2025, 2026, 2027] as $t)
                                <option value="{{ $t }}" @selected($t === 2026)>{{ $t }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="tahunKe" class="{{ $labelClass }}">Tahun pelaksanaan ke-</label>
                    <input id="tahunKe" name="tahun_pelaksanaan_ke" type="number" min="1" required x-model="tahunKe"
                           placeholder="1" class="{{ $inputClass }}">
                </div>
                <div class="flex flex-col gap-2">
                    <label for="lama" class="{{ $labelClass }}">Lama kegiatan (tahun)</label>
                    <input id="lama" name="lama_kegiatan" type="number" min="1" required x-model="lama"
                           placeholder="1" class="{{ $inputClass }}">
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label for="lokasi" class="{{ $labelClass }}">Lokasi</label>
                <input id="lokasi" name="lokasi" type="text" required x-model="lokasi"
                       placeholder="Kota / kabupaten lokasi kegiatan" class="{{ $inputClass }}">
            </div>

            <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="noSk" class="{{ $labelClass }}">Nomor SK penugasan</label>
                    <input id="noSk" name="nomor_sk" type="text" required placeholder="Nomor SK" class="{{ $inputClass }}">
                </div>
                <div class="flex flex-col gap-2">
                    <label for="tglSk" class="{{ $labelClass }}">Tanggal SK penugasan</label>
                    <input id="tglSk" name="tanggal_sk" type="date" required class="{{ $inputClass }}">
                </div>
            </div>
        </section>

        {{-- 3. PENDANAAN DAN MITRA --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-dana">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">3</span>
                <h2 id="h-dana" class="text-[15px] font-semibold">Pendanaan dan Mitra</h2>
            </div>

            <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                @foreach ([
                    ['dana_dikti', 'Dana DIKTI'],
                    ['dana_perguruan_tinggi', 'Dana perguruan tinggi'],
                    ['dana_institusi_lain', 'Dana institusi lain'],
                    ['in_kind', 'In kind'],
                ] as [$nama, $label])
                    <div class="flex flex-col gap-2">
                        <label for="{{ $nama }}" class="{{ $labelClass }}">{{ $label }}</label>
                        {{-- Titik ribuan otomatis. Backend nanti membuang titiknya sebelum disimpan. --}}
                        <input id="{{ $nama }}" name="{{ $nama }}" type="text" inputmode="numeric" placeholder="Rp"
                               x-on:input="$event.target.value = $event.target.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                               class="{{ $inputClass }}">
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col gap-2">
                <label for="mitra" class="{{ $labelClass }}">Mitra (opsional)</label>
                <input id="mitra" name="mitra" type="text" x-model="mitra"
                       placeholder="Nama mitra kerja sama" class="{{ $inputClass }}">
            </div>
        </section>

        {{-- 4. ANGGOTA PENELITIAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-anggota">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">4</span>
                <h2 id="h-anggota" class="text-[15px] font-semibold">Anggota Penelitian</h2>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="relative min-w-56 flex-[3_1_260px]">
                    <label for="cari" class="sr-only">Cari dosen atau mahasiswa</label>
                    <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
                    <input id="cari" type="text" list="kandidat" x-model="baru.nama" placeholder="Cari dosen atau mahasiswa..."
                           class="{{ $inputClass }} pl-10">
                    <datalist id="kandidat">
                        <option value="Kuwat Santoso"></option>
                        <option value="Wiktasari"></option>
                        <option value="Mukhlis Pratama Mulya"></option>
                        <option value="Angga Wahyu Wibowo"></option>
                        <option value="Arif Setyawan"></option>
                    </datalist>
                </div>

                <div class="relative min-w-40 flex-[1_1_160px]">
                    <label for="peran" class="sr-only">Peran</label>
                    <select id="peran" x-model="baru.peran" class="{{ $selectClass }}">
                        <option value="">Pilih peran</option>
                        <option>Anggota</option>
                        <option>Narasumber</option>
                    </select>
                    <svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </div>

                <div class="relative min-w-40 flex-[1_1_160px]">
                    <label for="statusA" class="sr-only">Status</label>
                    <select id="statusA" x-model="baru.status" class="{{ $selectClass }}">
                        <option value="">Pilih status</option>
                        <option>Dosen</option>
                        <option>Mahasiswa</option>
                    </select>
                    <svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </div>

                <button type="button" x-on:click="tambah()"
                        class="flex h-11 cursor-pointer items-center gap-2 rounded-lg bg-brand px-5 text-base font-medium text-white hover:bg-blue-700">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah
                </button>
            </div>

            <template x-if="errorAnggota">
                <p role="alert" class="text-sm text-red-600" x-text="errorAnggota"></p>
            </template>

            <div class="overflow-x-auto rounded-md border border-gray-300">
                <table class="w-full min-w-[520px] border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th scope="col" class="w-1/4 px-2 py-2.5 font-medium">Nama</th>
                            <th scope="col" class="w-1/4 px-2 py-2.5 font-medium">Peran</th>
                            <th scope="col" class="w-1/4 px-2 py-2.5 font-medium">Status</th>
                            <th scope="col" class="w-1/4 px-2 py-2.5 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(a, i) in anggota" :key="a.nama">
                            <tr class="border-t border-gray-300">
                                <td class="px-2 py-2.5">
                                    <span x-text="a.nama"></span>
                                    {{-- data yang dikirim ke backend --}}
                                    <input type="hidden" :name="`anggota[${i}][nama]`" :value="a.nama">
                                    <input type="hidden" :name="`anggota[${i}][peran]`" :value="a.peran">
                                    <input type="hidden" :name="`anggota[${i}][status]`" :value="a.status">
                                </td>
                                <td class="px-2 py-2.5" x-text="a.peran"></td>
                                <td class="px-2 py-2.5" x-text="a.status"></td>
                                <td class="px-2 py-1.5">
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

        {{-- TOMBOL --}}
        <div class="flex flex-wrap items-center justify-end gap-3">
            <template x-if="tersimpan">
                <p role="status" class="mr-auto rounded-lg border border-green-200 bg-green-50 px-3.5 py-2.5 text-sm text-green-800">
                    Tersimpan (simulasi). Data belum masuk database.
                </p>
            </template>
            <a href="{{ route('penelitian.index') }}"
               class="flex h-11 items-center rounded-md bg-red-500 px-6 text-base text-white hover:bg-red-600">Batal</a>
            <button type="submit"
                    class="h-11 cursor-pointer rounded-md bg-brand px-6 text-base text-white hover:bg-blue-700">Simpan</button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formPenelitian', () => ({
            judul: '', afiliasi: '', skim: '', bidang: '', sebelumnya: '',
            lokasi: '', mitra: '', tahunKe: '', lama: '',

            // Skim => Ranah
            ranahMap: {
                'Penelitian Dasar Unggulan Perguruan Tinggi': 'Penelitian Dasar',
                'Penelitian Terapan': 'Penelitian Terapan',
                'Penelitian Pengembangan': 'Penelitian Pengembangan',
            },
            get ranah() { return this.ranahMap[this.skim] || ''; },

            // Data tiruan untuk "Penelitian sebelumnya"
            dataSebelumnya: {
                p1: { judul: 'Deteksi hoaks dengan ML', afiliasi: 'Jurusan Teknik Elektro / Politeknik Negeri Semarang',
                      skim: 'Penelitian Terapan', bidang: 'Teknologi Informasi', lokasi: 'Kota Semarang', mitra: '', tahunKe: '2', lama: '3' },
                p2: { judul: 'Analisis sentimen ulasan aplikasi kampus', afiliasi: 'Jurusan Teknik Elektro / Politeknik Negeri Semarang',
                      skim: 'Penelitian Pengembangan', bidang: 'Pendidikan', lokasi: 'Kota Semarang', mitra: '', tahunKe: '2', lama: '2' },
            },
            isiDariSebelumnya() {
                const d = this.dataSebelumnya[this.sebelumnya];
                if (d) Object.assign(this, d);
            },

            // Anggota
            // Ketua = akun yang login, selalu ada dan terkunci (tidak bisa dihapus).
            // Anggota lain kosong di awal, muncul setelah ditambahkan.
            // Nanti backend mengganti nama ini dengan nama user yang login.
            anggota: [
                { nama: 'Kuwat Santoso', peran: 'Ketua', status: 'Dosen', kunci: true },
            ],
            baru: { nama: '', peran: '', status: '' },
            errorAnggota: '',
            tambah() {
                const nama = this.baru.nama.trim();
                if (!nama || !this.baru.peran || !this.baru.status) {
                    this.errorAnggota = 'Isi nama, peran, dan status sebelum menambahkan anggota.';
                    return;
                }
                if (this.anggota.some(a => a.nama.toLowerCase() === nama.toLowerCase())) {
                    this.errorAnggota = 'Anggota ini sudah ada di daftar.';
                    return;
                }
                this.anggota.push({ nama, peran: this.baru.peran, status: this.baru.status, kunci: false });
                this.baru = { nama: '', peran: '', status: '' };
                this.errorAnggota = '';
            },
            hapus(i) { this.anggota.splice(i, 1); },

            // Simpan (simulasi)
            tersimpan: false,
            simpan() { this.tersimpan = true; },
        }));
    });
</script>
@endpush