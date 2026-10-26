@extends('layouts.app')

@section('title', 'Tambah Prestasi')

@php
    // DATA TIRUAN. Nanti diganti data dari controller / database oleh tim backend.
    // Kegiatan yang pernah diikuti (untuk "Dari kegiatan sebelumnya")
    $kegiatanLama = [
        'KMIPN VIII 2026 · Lomba'        => ['nama' => 'KMIPN VIII 2026',      'penyelenggara' => 'Kemendikbudristek',          'mulai' => '2026-09-12', 'selesai' => '2026-09-14'],
        'Seminar nasional AI · Seminar'  => ['nama' => 'Seminar nasional AI',  'penyelenggara' => 'Politeknik Negeri Semarang', 'mulai' => '2026-09-12', 'selesai' => '2026-09-12'],
        'Lomba karya ilmiah · Lomba'     => ['nama' => 'Lomba karya ilmiah',   'penyelenggara' => 'Universitas Diponegoro',     'mulai' => '2026-07-15', 'selesai' => '2026-07-16'],
    ];
    $daftarKategori = ['Teknologi', 'IoT', 'Jaringan', 'Olahraga', 'Seni'];
    $kandidat = [
        'Kuwat Santoso , S.T., M.Kom.' => 'Dosen',
        'Wiktasari'                    => 'Dosen',
        'Ivan Sigit Santoso'           => 'Mahasiswa',
        'Anisa Aulia Nurusyifa'        => 'Mahasiswa',
        'Mukhlis Pratama Mulya'        => 'Mahasiswa',
    ];
    $pengaju = 'Kuwat Santoso , S.T., M.Kom.'; // akun yang login

    $inputClass   = 'h-11 w-full rounded-lg border border-gray-200 bg-white px-3.5 text-[15px] text-gray-900 placeholder:text-gray-400';
    $selectClass  = 'h-11 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-10 pl-3.5 text-[15px] text-gray-900';
    $labelClass   = 'text-[15px] font-medium';
    $hintClass    = 'text-[13px] text-gray-500';
    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
    $radioKartu   = 'flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 text-[15px] has-[:checked]:border-brand has-[:checked]:bg-blue-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand/40';
    $chevron      = '<svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
    $wajib        = '<span class="text-red-500" aria-hidden="true">*</span>';
@endphp

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-4xl font-bold tracking-tight">Tambah Prestasi</h1>
        <p class="text-2xl text-gray-400">Dashboard / Prestasi / Tambah</p>
    </div>

    <form x-data="formPrestasi(@js($kegiatanLama), @js($kandidat), @js($pengaju))" x-on:submit.prevent="simpan()"
          method="POST" action="#" enctype="multipart/form-data"
          class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">
        @csrf

        <p class="text-sm text-gray-500"><span class="text-red-500">*</span> Wajib diisi. Kolom bertanda opsional boleh dikosongkan.</p>

        {{-- 1. SUMBER KEGIATAN --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-sumber">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">1</span>
                <div>
                    <h2 id="h-sumber" class="text-[15px] font-semibold">Sumber kegiatan</h2>
                    <p class="{{ $hintClass }}">Prestasi boleh dicatat mandiri, atau dihubungkan dengan kegiatan yang pernah diajukan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="{{ $radioKartu }}">
                    <input type="radio" name="sumber" value="kegiatan" x-model="sumber" required class="size-4 accent-[#1f66e5]">
                    Dari kegiatan sebelumnya
                </label>
                <label class="{{ $radioKartu }}">
                    <input type="radio" name="sumber" value="mandiri" x-model="sumber" required class="size-4 accent-[#1f66e5]">
                    Tanpa kegiatan sebelumnya
                </label>
            </div>

            {{-- Hanya muncul kalau memilih "Dari kegiatan sebelumnya" --}}
            <div x-show="sumber === 'kegiatan'" x-cloak class="flex flex-col gap-2">
                <label for="kegiatan-lama" class="{{ $labelClass }}">Kegiatan sebelumnya</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
                    <input id="kegiatan-lama" name="kegiatan_sebelumnya" type="text" list="daftar-kegiatan" x-model="pilihKegiatan"
                           placeholder="Cari dan pilih kegiatan" class="{{ $inputClass }} pl-10">
                    <datalist id="daftar-kegiatan">
                        @foreach ($kegiatanLama as $label => $k)
                            <option value="{{ $label }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <p class="{{ $hintClass }}">Nama kegiatan, penyelenggara, dan tanggal akan terisi otomatis dan masih bisa kamu ubah sesuai sertifikat.</p>
            </div>
        </section>

        {{-- 2. INFORMASI PRESTASI --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-info">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">2</span>
                <div>
                    <h2 id="h-info" class="text-[15px] font-semibold">Informasi prestasi</h2>
                    <p class="{{ $hintClass }}">Isi nama lomba dan capaian sesuai dokumen bukti.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4.5 md:grid-cols-2">
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label for="nama" class="{{ $labelClass }}">Nama prestasi {!! $wajib !!}</label>
                    <input id="nama" name="nama_prestasi" type="text" required x-model="nama"
                           placeholder="Juara 1 Lomba Karya Tulis Ilmiah Nasional 2026" class="{{ $inputClass }}">
                </div>

                <div class="flex flex-col gap-2 md:col-span-2">
                    <label for="penyelenggara" class="{{ $labelClass }}">Penyelenggara {!! $wajib !!}</label>
                    <input id="penyelenggara" name="penyelenggara" type="text" required x-model="penyelenggara"
                           placeholder="Nama institusi penyelenggara" class="{{ $inputClass }}">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="tingkat" class="{{ $labelClass }}">Tingkat prestasi {!! $wajib !!}</label>
                    <div class="relative">
                        <select id="tingkat" name="tingkat" required class="{{ $selectClass }}">
                            <option value="">Pilih tingkat prestasi</option>
                            @foreach (['Internal', 'Kabupaten/kota', 'Provinsi', 'Nasional', 'Internasional'] as $t)
                                <option>{{ $t }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="jenis" class="{{ $labelClass }}">Jenis prestasi {!! $wajib !!}</label>
                    <div class="relative">
                        <select id="jenis" name="jenis" required class="{{ $selectClass }}">
                            <option value="">Pilih jenis prestasi</option>
                            <option>Akademik</option>
                            <option>Nonakademik</option>
                        </select>
                        {!! $chevron !!}
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="mulai" class="{{ $labelClass }}">Tanggal mulai {!! $wajib !!}</label>
                    <input id="mulai" name="tanggal_mulai" type="date" required x-model="mulai" class="{{ $inputClass }}">
                </div>
                <div class="flex flex-col gap-2">
                    <label for="selesai" class="{{ $labelClass }}">Tanggal selesai {!! $wajib !!}</label>
                    <input id="selesai" name="tanggal_selesai" type="date" required x-model="selesai" :min="mulai" class="{{ $inputClass }}">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="capaian" class="{{ $labelClass }}">Capaian / peringkat {!! $wajib !!}</label>
                    <input id="capaian" name="capaian" type="text" required
                           placeholder="Contoh: Juara 1" class="{{ $inputClass }}">
                    <p class="{{ $hintClass }}">Contoh: Juara 1, Harapan 2, Finalis, Peserta terbaik.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="kategori" class="{{ $labelClass }}">Kategori / bidang <span class="font-normal text-gray-500">(opsional)</span></label>
                    <input id="kategori" name="kategori" type="text" list="daftar-kategori"
                           placeholder="Pilih atau tulis kategori" class="{{ $inputClass }}">
                    <datalist id="daftar-kategori">
                        @foreach ($daftarKategori as $k)
                            <option value="{{ $k }}"></option>
                        @endforeach
                    </datalist>
                    <p class="{{ $hintClass }}">Contoh: teknologi, IoT, jaringan, olahraga, seni.</p>
                </div>

                <div class="flex flex-col gap-2 md:col-span-2">
                    <span id="l-bentuk" class="{{ $labelClass }}">Bentuk partisipasi {!! $wajib !!}</span>
                    <div role="radiogroup" aria-labelledby="l-bentuk" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label class="{{ $radioKartu }}">
                            <input type="radio" name="bentuk" value="Individu" x-model="bentuk" required class="size-4 accent-[#1f66e5]">
                            Individu
                        </label>
                        <label class="{{ $radioKartu }}">
                            <input type="radio" name="bentuk" value="Tim" x-model="bentuk" required class="size-4 accent-[#1f66e5]">
                            Tim
                        </label>
                    </div>
                </div>

                <div class="flex flex-col gap-2 md:col-span-2">
                    <label for="deskripsi" class="{{ $labelClass }}">Deskripsi <span class="font-normal text-gray-500">(opsional)</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="1000" x-model="deskripsi"
                              placeholder="Jelaskan pencapaian, kontribusi Anda, dan informasi tambahan yang relevan."
                              class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-3 text-[15px] text-gray-900 placeholder:text-gray-400"></textarea>
                    <p class="{{ $hintClass }}">Maksimal 1.000 karakter · <span x-text="deskripsi.length.toLocaleString('id-ID')"></span>/1.000</p>
                </div>
            </div>
        </section>

        {{-- 3. ANGGOTA TIM (hanya kalau bentuk partisipasi = Tim) --}}
        <section x-show="bentuk === 'Tim'" x-cloak class="{{ $sectionClass }}" aria-labelledby="h-anggota">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}">3</span>
                <div>
                    <h2 id="h-anggota" class="text-[15px] font-semibold">Anggota tim {!! $wajib !!}</h2>
                    <p class="{{ $hintClass }}">Kamu otomatis menjadi ketua. Tambahkan anggota lain di bawah ini.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_11rem_11rem_auto]">
                <div class="relative sm:col-span-2 lg:col-span-1">
                    <label for="cari" class="sr-only">Cari dosen atau mahasiswa</label>
                    <svg class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
                    <input id="cari" type="text" list="daftar-kandidat" x-model="cari" x-on:keydown.enter.prevent="tambah()"
                           placeholder="Cari dosen atau mahasiswa..." class="{{ $inputClass }} pl-10">
                    <datalist id="daftar-kandidat">
                        @foreach ($kandidat as $nama => $status)
                            <option value="{{ $nama }}">{{ $status }}</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="relative">
                    <label for="peran" class="sr-only">Peran</label>
                    <select id="peran" x-model="peran" class="{{ $selectClass }}">
                        <option value="">Pilih peran</option>
                        <option>Anggota</option>
                        <option>Pembimbing</option>
                    </select>
                    {!! $chevron !!}
                </div>
                <div class="relative">
                    <label for="status-anggota" class="sr-only">Status</label>
                    <select id="status-anggota" x-model="status" class="{{ $selectClass }}">
                        <option value="">Pilih status</option>
                        <option>Dosen</option>
                        <option>Mahasiswa</option>
                    </select>
                    {!! $chevron !!}
                </div>

                <button type="button" x-on:click="tambah()"
                        class="flex h-11 cursor-pointer items-center justify-center gap-2 rounded-lg bg-brand px-5 text-base font-medium text-white hover:bg-blue-700 sm:col-span-2 lg:col-span-1">
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
                            <th scope="col" class="w-[34%] px-2.5 py-2.5 font-medium">Nama</th>
                            <th scope="col" class="w-1/4 px-2.5 py-2.5 font-medium">Peran</th>
                            <th scope="col" class="w-1/4 px-2.5 py-2.5 font-medium">Status</th>
                            <th scope="col" class="px-2.5 py-2.5 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(a, i) in anggota" :key="a.nama">
                            <tr class="border-t border-gray-300">
                                <td class="px-2.5 py-2.5">
                                    <span x-text="a.nama"></span>
                                    <template x-if="bentuk === 'Tim'">
                                        <span>
                                            <input type="hidden" :name="`anggota[${i}][nama]`" :value="a.nama">
                                            <input type="hidden" :name="`anggota[${i}][peran]`" :value="a.peran">
                                            <input type="hidden" :name="`anggota[${i}][status]`" :value="a.status">
                                        </span>
                                    </template>
                                </td>
                                <td class="px-2.5 py-2.5" x-text="a.peran"></td>
                                <td class="px-2.5 py-2.5" x-text="a.status"></td>
                                <td class="px-2.5 py-1.5">
                                    {{-- ketua (akun yang login) tidak bisa dihapus --}}
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

        {{-- 3/4. UNGGAH DOKUMEN (nomor 4 kalau ada bagian Anggota tim, 3 kalau Individu) --}}
        <section class="{{ $sectionClass }}" aria-labelledby="h-dokumen">
            <div class="flex items-center gap-2.5">
                <span class="{{ $numClass }}" x-text="bentuk === 'Tim' ? 4 : 3"></span>
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
                            <span class="{{ $labelClass }}">File {!! $wajib !!}</span>
                            <label class="relative flex cursor-pointer flex-col items-center gap-2 rounded-md border border-dashed border-brand bg-white px-4 py-5 text-center hover:bg-blue-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand">
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

                        <div class="flex flex-col gap-2">
                            <label :for="`jenis-${d.id}`" class="{{ $labelClass }}">Jenis dokumen {!! $wajib !!}</label>
                            <div class="relative">
                                <select :id="`jenis-${d.id}`" :name="`dokumen[${i}][jenis]`" required x-model="d.jenis" class="{{ $selectClass }}">
                                    <option value="">Pilih jenis dokumen</option>
                                    <option>Sertifikat</option>
                                    <option>Piagam</option>
                                    <option>Surat Tugas</option>
                                    <option>Dokumentasi</option>
                                    <option>Lainnya</option>
                                </select>
                                {!! $chevron !!}
                            </div>
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
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-4">
            <p class="text-sm text-gray-500">Periksa kembali sebelum menyimpan.</p>
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('prestasi.index') }}"
                   class="flex h-11 items-center rounded-md bg-red-500 px-6 text-base text-white hover:bg-red-600">Batal</a>
                <button type="submit"
                        class="h-11 cursor-pointer rounded-md bg-brand px-6 text-base text-white hover:bg-blue-700">Simpan prestasi</button>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formPrestasi', (kegiatanLama, kandidat, pengaju) => ({
            sumber: '',
            bentuk: '',
            nama: '', penyelenggara: '', mulai: '', selesai: '', deskripsi: '',

            // Pilih kegiatan lama => isi nama, penyelenggara, dan tanggal (masih bisa diubah)
            pilihKegiatan: '',
            init() {
                this.$watch('pilihKegiatan', v => {
                    const k = kegiatanLama[v];
                    if (!k) return;
                    this.nama = k.nama; this.penyelenggara = k.penyelenggara;
                    this.mulai = k.mulai; this.selesai = k.selesai;
                });
                // status anggota terisi otomatis kalau nama ada di daftar
                this.$watch('cari', v => {
                    const nama = Object.keys(kandidat).find(n => n.toLowerCase() === v.trim().toLowerCase());
                    if (nama) this.status = kandidat[nama];
                });
            },

            // Anggota. Pengaju (akun yang login) selalu jadi Ketua dan terkunci.
            anggota: [
                { nama: pengaju, peran: 'Ketua', status: 'Dosen', kunci: true },
            ],
            cari: '', peran: '', status: '', errorAnggota: '',
            tambah() {
                const nama = this.cari.trim();
                if (!nama || !this.peran || !this.status) {
                    this.errorAnggota = 'Isi nama, peran, dan status sebelum menambahkan anggota.';
                    return;
                }
                if (this.anggota.some(a => a.nama.toLowerCase() === nama.toLowerCase())) {
                    this.errorAnggota = 'Anggota ini sudah ada di daftar.';
                    return;
                }
                this.anggota.push({ nama, peran: this.peran, status: this.status, kunci: false });
                this.cari = ''; this.peran = ''; this.status = ''; this.errorAnggota = '';
            },
            hapus(i) { this.anggota.splice(i, 1); },

            // Dokumen. Dokumen 1 selalu ada, sisanya bisa ditambah / dihapus. File maksimal 5 MB.
            nextId: 2,
            dokumen: [
                { id: 1, namaFile: '', errorFile: '', jenis: '' },
            ],
            tambahDokumen() {
                this.dokumen.push({ id: this.nextId++, namaFile: '', errorFile: '', jenis: '' });
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

            // Simpan (simulasi). Setiap dokumen wajib punya file.
            tersimpan: false,
            simpan() {
                let lengkap = true;
                this.dokumen.forEach(d => {
                    if (!d.namaFile) { d.errorFile = 'File wajib dipilih.'; lengkap = false; }
                });
                if (!lengkap) return;
                this.tersimpan = true;
                this.$dispatch('tampil-alert', { tipe: 'sukses', judul: 'Data berhasil disimpan', pesan: 'Simulasi: data belum masuk database.' });
            },
        }));
    });
</script>
@endpush