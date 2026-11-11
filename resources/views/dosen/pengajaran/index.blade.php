@extends('layouts.app')

@section('title', 'Pengajaran')

@section('content')

@php
    // Bagian @php lama (pengajaran, daftarKelas, dll) DIHAPUS karena data sekarang diambil lewat API.
    $selectClass = 'h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-9 pl-3.5 text-[15px] text-gray-900';
@endphp

    {{-- Judul halaman + tombol tambah --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-4xl font-bold tracking-tight">Pengajaran</h1>
            <p class="text-2xl text-gray-400">Dashboard / Pengajaran</p>
        </div>
        <a href="{{ route('pengajaran.create') }}" class="mt-2 flex h-11 items-center gap-2 rounded-md bg-brand px-4 text-base font-medium text-white hover:bg-blue-700">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pengajaran
        </a>
    </div>

    {{-- x-data sekarang dipanggil tanpa parameter PHP --}}
    <section class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm"
             x-data="pengajaran()">

        <div class="flex flex-wrap items-center gap-4">
            <div class="relative min-w-60 flex-[1_1_320px]">
                <label for="q" class="sr-only">Cari mata kuliah</label>
                <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
                <input id="q" name="q" type="search" x-model="cari" x-on:input="halaman = 1" placeholder="Cari mata kuliah atau kelas..."
                       class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-3 pl-10 text-[15px] placeholder:text-gray-400">
            </div>

            <div class="relative w-full sm:w-46">
                <label for="kelas" class="sr-only">Filter kelas</label>
                <select id="kelas" x-model="f.kelas" x-on:change="halaman = 1" class="{{ $selectClass }}">
                    <option value="">Semua Kelas</option>
                    {{-- Menggunakan template x-for bawaan Alpine, menggantikan @foreach PHP --}}
                    <template x-for="o in opsiKelas" :key="o">
                        <option :value="o" x-text="o"></option>
                    </template>
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </div>

            <div class="relative w-full sm:w-46">
                <label for="tahun" class="sr-only">Filter tahun akademik</label>
                <select id="tahun" x-model="f.tahun" x-on:change="halaman = 1" class="{{ $selectClass }}">
                    <option value="">Semua Tahun</option>
                    <template x-for="o in opsiTahun" :key="o">
                        <option :value="o" x-text="o"></option>
                    </template>
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </div>

            <div class="relative w-full sm:w-46">
                <label for="semester" class="sr-only">Filter semester</label>
                <select id="semester" x-model="f.semester" x-on:change="halaman = 1" class="{{ $selectClass }}">
                    <option value="">Semua Semester</option>
                    <template x-for="o in opsiSemester" :key="o">
                        <option :value="o" x-text="o"></option>
                    </template>
                </select>
                <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </div>
        </div>

        {{-- Indikator Loading (Opsional tapi bagus untuk UX API) --}}
        <div x-show="isLoading" class="py-10 text-center text-sm text-gray-500">
            Mengambil data dari server...
        </div>

        {{-- Tabel (Sama persis seperti kodemu, hanya dibungkus x-show agar disembunyikan saat loading) --}}
        <div x-show="!isLoading" class="overflow-x-auto rounded-md border border-gray-300">
            <table class="w-full min-w-[900px] table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <x-th-urut kolom="matkul" label="Mata Kuliah" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="kelas" label="Kelas" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="tahun" label="Tahun Akademik" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <x-th-urut kolom="semester" label="Semester" class="w-1/5 border-b border-gray-300 p-0 font-medium" />
                        <th scope="col" class="w-1/5 border-b border-gray-300 px-2.5 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="k in tampil" :key="k.id">
                        <tr class="border-b border-gray-300 last:border-b-0 hover:bg-gray-50">
                            <td class="truncate px-2.5 py-3" :title="k.matkul" x-text="k.matkul"></td>
                            <td class="px-2.5 py-3" x-text="k.kelas"></td>
                            <td class="px-2.5 py-3" x-text="k.tahun"></td>
                            <td class="px-2.5 py-3" x-text="k.semester"></td>
                            <td class="px-2.5 py-2">
                                <div class="flex items-center gap-3">
                                    <a :href="`{{ url('/pengajaran') }}/${k.id}`" :aria-label="`Lihat detail ${k.matkul} kelas ${k.kelas}`"
                                       class="flex size-8 items-center justify-center rounded-lg bg-blue-100 text-brand hover:bg-blue-200">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a :href="`{{ url('/pengajaran') }}/${k.id}/edit`" :aria-label="`Edit ${k.matkul} kelas ${k.kelas}`"
                                       class="flex size-8 items-center justify-center rounded-lg bg-orange-100 text-orange-400 hover:bg-orange-200">
                                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4l11-11a2.8 2.8 0 0 0-4-4L4 16zM13.5 6.5l4 4"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <p x-show="terfilter.length === 0 && !isLoading" x-cloak class="px-4 py-10 text-center text-sm text-gray-500">
                Tidak ada pengajaran yang cocok dengan pencarian atau filter ini.
            </p>
        </div>

        <x-tabel-footer />
    </section>

    <x-tabel-js />

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pengajaran', () => tabelData({
            // Default awal berupa array kosong
            daftar: [], 
            opsiKelas: [],
            opsiTahun: [],
            opsiSemester: [],
            isLoading: true, // Status untuk UX

            cari: '',
            f: { kelas: '', tahun: '', semester: '' },

            get terfilter() {
                const q = this.cari.trim().toLowerCase();
                return this.daftar.filter(k => (q === '' || k.matkul.toLowerCase().includes(q) || k.kelas.toLowerCase().includes(q)) &&
                    (this.f.kelas === '' || k.kelas === this.f.kelas) &&
                    (this.f.tahun === '' || k.tahun === this.f.tahun) &&
                    (this.f.semester === '' || k.semester === this.f.semester));
            },

            // Fungsi init() akan dijalankan otomatis oleh Alpine saat komponen dimuat di browser
            async init() {
                try {
                    // Ambil token dari localStorage (Sesuaikan jika token login disave di tempat lain oleh teman frontend-mu)
                    const token = localStorage.getItem('token_auth_kelompok3') || '';

                    // Prefix API proyek ini: /api/v1 (lihat bootstrap/app.php)
                    const respon = await fetch('/api/v1/pengajaran', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + token
                        }
                    });

                    const json = await respon.json();

                    if (json.status === 'success') {
                        // Memasukkan data JSON dari Backend API ke dalam variabel Alpine Frontend
                        this.daftar = json.data.pengajaran;
                        this.opsiKelas = json.data.daftarKelas;
                        this.opsiTahun = json.data.daftarTahun;
                        this.opsiSemester = json.data.daftarSemester;
                    }
                } catch (error) {
                    console.error("Gagal mengambil API Pengajaran:", error);
                } finally {
                    this.isLoading = false; // Matikan loading saat selesai
                }
            }
        }, { perHalaman: 5, kunci: {} }));
    });
</script>
@endpush