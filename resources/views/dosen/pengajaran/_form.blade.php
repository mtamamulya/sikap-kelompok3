{{--
    Formulir Pengajaran, dipakai bersama oleh halaman Tambah dan Edit.
    Data dari controller : $opsi (tahun, rombel, matkul), $dosenLogin, $aksi, $metode, $awal.
    Yang dikirim ke backend: id_enrollment_tingkat_kelas (kelas) dan id_enrollment_mk (mata kuliah).
    Tahun akademik dan Program studi hanya penyaring pilihan, tidak ikut disimpan.
    Bagian "Dokumen Pendukung" tampil di form, tetapi belum disimpan backend (menunggu keputusan tim database soal dokumen.id_kegiatan).
    Isinya sudah dikirim dengan nama dokumen[i][nama|jenis|tautan], tinggal dibaca controller nanti.
--}}
@php
    $rombelAwal = (string) old('id_enrollment_tingkat_kelas', $awal['rombel'] ?? '');
    $matkulAwal = (string) old('id_enrollment_mk', $awal['matkul'] ?? '');

    $r = collect($opsi['rombel'])->firstWhere('id', (int) $rombelAwal);
    $m = collect($opsi['matkul'])->firstWhere('id', (int) $matkulAwal);
    $tahunAwal = $r['ta'] ?? $m['ta'] ?? ($opsi['tahun'][0]['id'] ?? '');
    $prodiAwal = $r['prodi'] ?? '';

    $dokumenTersimpan = false; // ganti true kalau backend sudah menyimpan dokumen
    $dokumenAwal = old('dokumen', $dokumen ?? []);
    if (empty($dokumenAwal)) { $dokumenAwal = [['nama' => '', 'jenis' => '', 'keterangan' => '', 'tautan' => '']]; }
    $dokumenAwal = collect($dokumenAwal)->values()->map(fn ($d, $i) => ['id' => $i + 1] + ['nama' => $d['nama'] ?? '', 'jenis' => $d['jenis'] ?? '', 'keterangan' => $d['keterangan'] ?? '', 'tautan' => $d['tautan'] ?? ''])->all();

    $inputClass   = 'h-11 w-full rounded-lg border border-gray-200 bg-white px-3.5 text-[15px] text-gray-900 placeholder:text-gray-400';
    $selectClass  = 'h-11 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-10 pl-3.5 text-[15px] text-gray-900';
    $lockedClass  = 'h-11 w-full rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3.5 text-[15px] text-gray-500';
    $labelClass   = 'text-[15px] font-medium';
    $sectionClass = 'flex flex-col gap-4.5 rounded-lg border border-gray-200 bg-white p-4.5';
    $numClass     = 'flex size-[26px] shrink-0 items-center justify-center rounded-full bg-brand text-[13px] font-semibold text-white';
    $chevron      = '<svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
@endphp

<form x-data="formPengajaran(@js($opsi), @js(['tahun' => (string) $tahunAwal, 'prodi' => $prodiAwal, 'rombel' => $rombelAwal, 'matkul' => $matkulAwal]), @js($dokumenAwal))"
      method="POST" action="{{ $aksi }}" class="flex flex-col gap-5 rounded-lg bg-white p-4.5 shadow-sm">
    @csrf
    @if ($metode === 'PUT') @method('PUT') @endif

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
                    <select id="tahunAkademik" required x-model="tahun" class="{{ $selectClass }}">
                        <template x-for="t in opsi.tahun" :key="t.id">
                            <option :value="t.id" :selected="String(t.id) === tahun" x-text="t.label"></option>
                        </template>
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <label for="semester" class="{{ $labelClass }}">Semester</label>
                {{-- terisi otomatis dari Tahun akademik, tidak bisa diketik --}}
                <input id="semester" type="text" readonly :value="semester" placeholder="Terisi otomatis" class="{{ $lockedClass }}">
            </div>

            <div class="flex flex-col gap-2">
                <label for="prodi" class="{{ $labelClass }}">Program studi</label>
                <div class="relative">
                    <select id="prodi" required x-model="prodi" class="{{ $selectClass }}">
                        <option value="">Pilih program studi</option>
                        <template x-for="p in daftarProdi" :key="p">
                            <option :value="p" :selected="p === prodi" x-text="p"></option>
                        </template>
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <label for="kelas" class="{{ $labelClass }}">Kelas</label>
                <div class="relative">
                    <select id="kelas" name="id_enrollment_tingkat_kelas" required x-model="rombel" class="{{ $selectClass }}">
                        <option value="">Pilih kelas</option>
                        <template x-for="r in daftarRombel" :key="r.id">
                            <option :value="r.id" :selected="String(r.id) === rombel" x-text="r.kelas"></option>
                        </template>
                    </select>
                    {!! $chevron !!}
                </div>
                @error('id_enrollment_tingkat_kelas') <p class="text-[13px] text-red-600">{{ $message }}</p> @enderror
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
                    <select id="matkul" name="id_enrollment_mk" required x-model="matkul" class="{{ $selectClass }}">
                        <option value="">Pilih mata kuliah</option>
                        <template x-for="m in daftarMatkul" :key="m.id">
                            <option :value="m.id" :selected="String(m.id) === matkul" x-text="m.label"></option>
                        </template>
                    </select>
                    {!! $chevron !!}
                </div>
                @error('id_enrollment_mk') <p class="text-[13px] text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label for="dosen" class="{{ $labelClass }}">Dosen pengampu</label>
                <div class="relative">
                    <select id="dosen" disabled aria-describedby="dosen-info"
                            class="h-11 w-full appearance-none rounded-lg border border-dashed border-gray-300 bg-gray-50 pr-10 pl-3.5 text-[15px] text-gray-700">
                        <option>{{ $dosenLogin }} (akun Anda)</option>
                    </select>
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

    {{-- 3. DOKUMEN PENDUKUNG --}}
    <section class="{{ $sectionClass }}" aria-labelledby="h-dokumen">
        <div class="flex items-center gap-2.5">
            <span class="{{ $numClass }}">3</span>
            <h2 id="h-dokumen" class="text-[15px] font-semibold">Dokumen Pendukung</h2>
        </div>

        {{-- Hapus catatan ini kalau penyimpanan dokumen di backend sudah jadi (set $dokumenTersimpan = true di atas). --}}
        @unless ($dokumenTersimpan)
            <p role="note" class="rounded-lg bg-amber-50 px-3.5 py-2.5 text-[13px] text-amber-700">Catatan: dokumen belum disimpan ke database, penyimpanannya menyusul.</p>
        @endunless

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

                    <!-- <div class="flex flex-col gap-2">
                        <label :for="`ket-${d.id}`" class="{{ $labelClass }}">Keterangan</label>
                        <textarea :id="`ket-${d.id}`" :name="`dokumen[${i}][keterangan]`" rows="3" x-model="d.keterangan"
                                  placeholder="Keterangan singkat tentang dokumen ini"
                                  class="w-full resize-y rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-[15px] text-gray-900 placeholder:text-gray-400"></textarea>
                    </div> -->

                    <div class="flex flex-col gap-2">
                        <label :for="`tautan-${d.id}`" class="{{ $labelClass }}">Tautan dokumen</label>
                        <input :id="`tautan-${d.id}`" :name="`dokumen[${i}][tautan]`" type="url" required x-model="d.tautan"
                               placeholder="https://drive.google.com/..." class="{{ $inputClass }}">
                        <p class="text-[13px] text-gray-500">Tempel tautan Google Drive, OneDrive, atau cloud lain. Pastikan akses tautan dibuka untuk yang memiliki tautan.</p>
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
        <a href="{{ route('pengajaran.index') }}"
           class="flex h-11 items-center rounded-md bg-red-500 px-6 text-base text-white hover:bg-red-600">Batal</a>
        <button type="submit"
                class="h-11 cursor-pointer rounded-md bg-brand px-6 text-base text-white hover:bg-blue-700">Simpan</button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formPengajaran', (opsi, awal, dokumenAwal) => ({
            opsi,
            tahun: awal.tahun, prodi: awal.prodi, rombel: awal.rombel, matkul: awal.matkul,

            // Pilihan berikutnya hanya yang cocok dengan tahun akademik / prodi yang dipilih.
            get daftarProdi() {
                return [...new Set(this.opsi.rombel.filter(r => String(r.ta) === this.tahun).map(r => r.prodi))];
            },
            get daftarRombel() {
                return this.opsi.rombel.filter(r => String(r.ta) === this.tahun && r.prodi === this.prodi);
            },
            get daftarMatkul() {
                return this.opsi.matkul.filter(m => String(m.ta) === this.tahun);
            },

            // Semester diambil dari Tahun akademik yang dipilih ("Ganjil 2025/2026" => "Ganjil")
            get semester() {
                const t = this.opsi.tahun.find(t => String(t.id) === this.tahun);
                return t ? t.semester : '';
            },

            // Ringkasan: gabungan pilihan yang sudah diisi
            get ringkasan() {
                const r = this.opsi.rombel.find(r => String(r.id) === this.rombel);
                const m = this.opsi.matkul.find(m => String(m.id) === this.matkul);
                const t = this.opsi.tahun.find(t => String(t.id) === this.tahun);
                if (!r && !m) return '-';
                return [m && m.label, r && `Kelas ${r.kelas}`, r && r.prodi, t && t.label].filter(Boolean).join(' · ');
            },

            // Dokumen. Dokumen 1 selalu ada, sisanya bisa ditambah / dihapus.
            dokumen: dokumenAwal,
            nextId: dokumenAwal.length + 1,
            tambahDokumen() {
                this.dokumen.push({ id: this.nextId++, nama: '', jenis: '', keterangan: '', tautan: '' });
            },
            hapusDokumen(i) { this.dokumen.splice(i, 1); },

            init() {
                // Ganti tahun akademik / prodi => pilihan di bawahnya dikosongkan supaya tidak salah pasangan.
                this.$watch('tahun', () => { this.prodi = ''; this.rombel = ''; this.matkul = ''; });
                this.$watch('prodi', () => { this.rombel = ''; });
            },
        }));
    });
</script>
@endpush