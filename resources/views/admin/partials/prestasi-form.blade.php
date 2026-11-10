@php
    $p = $prestasi ?? [];
    $labelCls = 'text-[13px] font-semibold leading-[1.5] text-[#111827]';
    $inputCls = 'h-[36px] w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none placeholder:text-[#B3B3B8] focus:border-[#2277FF]';
    $selectCls = 'h-[36px] w-full appearance-none rounded-lg border border-[#E5E7EB] bg-white px-3 pr-10 text-sm text-[#111827] outline-none focus:border-[#2277FF]';
    $hint = 'text-xs leading-[1.5] text-[#6B7280]';
    $chev = '<svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
    $adaFoto = !empty($p['bukti']);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="rounded-lg bg-white p-4 pb-6 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="flex flex-col gap-6 overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-6">
        <p class="{{ $hint }}">* Wajib diisi</p>
        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Detail Prestasi --}}
        <div class="flex flex-col gap-4">
            <h3 class="text-[18px] font-bold leading-[1.4] text-[#111827]">Detail Prestasi</h3>
            
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="flex flex-col gap-1.5">
                    <label for="capaian" class="{{ $labelCls }}">Nama Prestasi / Pencapaian</label>
                    <input id="capaian" name="capaian" type="text" value="{{ old('capaian', $p['capaian'] ?? '') }}" placeholder="Contoh: Juara 1 Web Design" class="{{ $inputCls }}">
                </div>
                
                <div class="flex flex-col gap-1.5">
                    <label for="jenis_prestasi" class="{{ $labelCls }}">Jenis Prestasi *</label>
                    <div class="relative">
                        <select id="jenis_prestasi" name="jenis_prestasi" required class="{{ $selectCls }}">
                            <option value="">Pilih Jenis Prestasi</option>
                            <option value="akademik" @selected(old('jenis_prestasi', $p['jenis_prestasi'] ?? '') === 'akademik')>Akademik</option>
                            <option value="non-akademik" @selected(old('jenis_prestasi', $p['jenis_prestasi'] ?? '') === 'non-akademik')>Non-akademik</option>
                        </select>
                        {!! $chev !!}
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="id_kegiatan" class="{{ $labelCls }}">Kegiatan Terkait *</label>
                    <div class="relative">
                        <select id="id_kegiatan" name="id_kegiatan" required class="{{ $selectCls }}">
                            <option value="">Pilih Kegiatan</option>
                            @foreach (($kegiatanOptions ?? []) as $kegiatan)
                                @php
                                    $pemilik = $kegiatan->user;

                                    $tipePemilik = match ((int) ($pemilik?->id_role ?? 0)) {
                                        3 => 'Dosen',
                                        4 => 'Mahasiswa',
                                        default => '',
                                    };

                                    $namaPemilik = $pemilik?->dosen?->nama
                                        ?? $pemilik?->mahasiswa?->nama
                                        ?? $pemilik?->email
                                        ?? '';
                                @endphp

                                <option
                                    value="{{ $kegiatan->id_kegiatan }}"
                                    data-tipe="{{ $tipePemilik }}"
                                    data-pemilik="{{ $namaPemilik }}"
                                    @selected((string) old('id_kegiatan', $p['id_kegiatan'] ?? '') === (string) $kegiatan->id_kegiatan)
                                >
                                    {{ $kegiatan->nama_kegiatan }}
                                </option>
                            @endforeach
                        </select>
                        {!! $chev !!}
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="bidang_lomba" class="{{ $labelCls }}">Bidang Lomba</label>
                    <input id="bidang_lomba" name="bidang_lomba" type="text" maxlength="150" value="{{ old('bidang_lomba', $p['bidang_lomba'] ?? '') }}" placeholder="Contoh: Robotika" class="{{ $inputCls }}">
                </div>
            </div>
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- Data Peraih --}}
        <div class="flex flex-col gap-4">
            <h3 class="text-[18px] font-bold leading-[1.4] text-[#111827]">Informasi Peraih Prestasi</h3>
            <p class="{{ $hint }}">Data peraih mengikuti kegiatan terkait. Pratinjau di bawah hanya menampilkan data sebelum perubahan dan tidak diedit di form ini.</p>
            
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="flex flex-col gap-1.5">
                    <label for="tipe" class="{{ $labelCls }}">Tipe Peraih (dari kegiatan)</label>
                    <div class="relative">
                        <select id="tipe" disabled class="{{ $selectCls }}">
                            <option value="">Mengikuti kegiatan</option>
                            <option value="Mahasiswa" @selected(($p['tipe'] ?? '') === 'Mahasiswa')>Mahasiswa</option>
                            <option value="Dosen" @selected(($p['tipe'] ?? '') === 'Dosen')>Dosen</option>
                        </select>
                        {!! $chev !!}
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="pengunggah" class="{{ $labelCls }}">Pemilik/Pengaju Kegiatan (tampilan saja)</label>
                    <input id="pengunggah" type="text" readonly value="{{ $p['pengunggah'] ?? '' }}" placeholder="Mengikuti kegiatan" class="{{ $inputCls }}">
                </div>

                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label for="catatan" class="{{ $labelCls }}">Catatan Prestasi</label>
                    <textarea id="catatan" name="catatan" placeholder="Keterangan tambahan tentang prestasi" class="min-h-[80px] resize-y rounded-lg border border-[#E5E7EB] p-3 text-sm outline-none focus:border-[#2277FF]">{{ old('catatan', $p['catatan'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- File Upload --}}
        <div class="flex flex-col items-center gap-3 overflow-hidden rounded-lg border border-dashed border-[#AAC8F8] bg-[#F9FAFB] p-6 text-center">
            <svg class="text-[#0B3A8C]" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5"/><path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.99"/><path d="M6 9l12 0"/><path d="M6 12l3 0"/></svg>
            <p class="text-sm font-semibold leading-[1.5] text-[#111827]">Unggah sertifikat / foto bukti (opsional)</p>
            @if ($adaFoto)
                <p class="{{ $hint }}">{{ count($p['bukti']) }} bukti tersimpan. Berkas baru akan ditambahkan.</p>
            @endif
            <div class="w-full max-w-sm">
                <label for="id_jenis_dokumen" class="{{ $labelCls }}">
                    Jenis Dokumen (jika mengunggah)
                </label>

                <div class="relative mt-1.5">
                    <select id="id_jenis_dokumen"
                            name="id_jenis_dokumen"
                            class="{{ $selectCls }}">
                        <option value="">Pilih jenis dokumen</option>

                        @foreach (($jenisDokumenOptions ?? []) as $jenis)
                            <option value="{{ $jenis->id_jenis_dokumen }}"
                                @selected((string) old('id_jenis_dokumen') === (string) $jenis->id_jenis_dokumen)>
                                {{ $jenis->nama_jenis_dokumen }}
                            </option>
                        @endforeach
                    </select>

                    {!! $chev !!}
                </div>
            </div>
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-[#E5E7EB] bg-white px-4 py-[11px] text-sm font-semibold text-[#0062FF]">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 9l5 -5l5 5"/><path d="M12 4l0 12"/></svg>
                Pilih berkas
                <input id="foto" name="foto" type="file" accept=".pdf,.jpg,.jpeg,.png" class="sr-only">

            </label>
            <p id="nama-file-terpilih" class="text-sm text-[#6B7280]">
                Belum ada berkas dipilih
            </p>            
            <p class="{{ $hint }}">PDF, JPG, atau PNG • Maksimal 5 MB</p>
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <div class="flex flex-wrap gap-3 text-sm font-semibold">
                <a href="{{ route('admin.prestasi') }}" class="flex h-10 items-center rounded-lg border border-[#E5E7EB] bg-white px-4 text-[#0062FF]">Batal</a>
                <button type="submit" class="h-10 rounded-lg border border-[#2277FF] bg-[#2277FF] px-4 text-white">Simpan Prestasi</button>
            </div>
        </div>
    </div>
</form>

<script>
(() => {
    const kegiatan = document.getElementById('id_kegiatan');
    const tipe = document.getElementById('tipe');
    const pemilik = document.getElementById('pengunggah');

    if (!kegiatan || !tipe || !pemilik) return;

    function perbaruiInfoPemilik() {
        const option = kegiatan.selectedOptions[0];

        tipe.value = option?.dataset.tipe || '';
        pemilik.value = option?.dataset.pemilik || '';
    }

    kegiatan.addEventListener('change', perbaruiInfoPemilik);

    // Mengisi informasi ketika halaman Edit dibuka
    perbaruiInfoPemilik();
})();

document.addEventListener('DOMContentLoaded', function () {
    const inputFile = document.getElementById('foto');
    const namaFile = document.getElementById('nama-file-terpilih');

    if (!inputFile || !namaFile) return;

    inputFile.addEventListener('change', function () {
        const file = this.files[0];

        namaFile.textContent = file
            ? 'Berkas dipilih: ' + file.name
            : 'Belum ada berkas dipilih';
    });
});
</script>