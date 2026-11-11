@extends('layouts.admin')

@section('title', $row ? 'Edit Kategori Kegiatan' : 'Tambah Kategori Kegiatan')

@section('content')
<div class="px-6 pb-8 pt-4 md:px-8">
    <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $row ? 'Edit Kategori Kegiatan' : 'Tambah Kategori Kegiatan' }}</h1>

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="mt-0.5 text-2xl leading-8 text-[#A8A8A8]">
        <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.kegiatan.mahasiswa') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Kegiatan</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.kegiatan.kategori-kegiatan') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Kategori Kegiatan</a>
        <span class="mx-1">/</span>
        <span class="text-[#6E6E76]" aria-current="page">{{ $row ? 'Edit' : 'Tambah' }}</span>
    </nav>

    <div class="mt-6 grid gap-4 lg:grid-cols-2">

        {{-- ============ KIRI: FORM (3 tab) ============ --}}
        <section class="flex flex-col rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">

            {{-- Tab --}}
            <div role="tablist" aria-label="Bagian form kategori" class="-mx-1 flex gap-1 border-b border-[#EBEBEF]">
                @foreach ([['data', 'Data Kategori'], ['isian', 'Isian Form'], ['template', 'Template Surat']] as [$id, $nama])
                    <button type="button" role="tab" id="tab-btn-{{ $id }}" data-tab="{{ $id }}" aria-controls="tab-{{ $id }}"
                            class="kk-tab -mb-px flex items-center gap-2 border-b-2 border-transparent px-3 pb-2.5 pt-1 text-sm font-semibold text-[#8A8A90] transition-colors hover:text-[#111] aria-selected:border-[#2277FF] aria-selected:text-[#2277FF]">
                        {{ $nama }}
                        @if ($id === 'isian')
                            <span id="isian-jumlah" class="hidden min-w-5 rounded-full bg-[#E3EDFF] px-1.5 text-center text-[11px] font-semibold leading-5 text-[#2277FF]">0</span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- ===== Tab 1: Data Kategori ===== --}}
            <div id="tab-data" role="tabpanel" aria-labelledby="tab-btn-data" class="pt-5">
                <h2 class="text-lg font-medium text-[#111]">Data Kategori</h2>
                <p class="text-sm text-[#8A8A90]">
                    {{ $row ? 'Ubah data kategori kegiatan program studi' : 'Isi data kategori kegiatan baru program studi' }}
                </p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="kk-nama" class="mb-1.5 block text-sm font-semibold text-[#111]">Nama Kategori</label>
                        <input id="kk-nama" type="text" maxlength="100" placeholder="Contoh: Kunjungan Industri"
                               class="h-10 w-full rounded-md border border-[#DEDEE3] px-3 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]">
                    </div>

                    <div>
                        <label for="kk-jenis" class="mb-1.5 block text-sm font-semibold text-[#111]">Jenis Kegiatan</label>
                        <div class="relative">
                            <select id="kk-jenis"
                                    class="h-10 w-full appearance-none rounded-md border border-[#DEDEE3] bg-white px-3 pr-9 text-sm outline-none focus:border-[#2277FF]">
                                @foreach ($jenis as $j)
                                    <option value="{{ $j }}">{{ $j }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#55555C]" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6" /></svg>
                        </div>
                    </div>

                    <fieldset>
                        <legend class="mb-1.5 text-sm font-semibold text-[#111]">Kebutuhan</legend>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-md border border-[#DEDEE3] p-3 hover:bg-[#FAFAFB] has-[:checked]:border-[#2277FF] has-[:checked]:bg-[#F2F7FF]">
                                <input type="radio" name="kk-kebutuhan" value="surat" class="mt-0.5 size-4 accent-[#2277FF]">
                                <span>
                                    <span class="block text-sm font-semibold text-[#111]">Butuh surat</span>
                                    <span class="block text-xs text-[#6E6E76]">Ditinjau dan disetujui admin dulu, lalu pengaju menerima surat persetujuan.</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-md border border-[#DEDEE3] p-3 hover:bg-[#FAFAFB] has-[:checked]:border-[#2277FF] has-[:checked]:bg-[#F2F7FF]">
                                <input type="radio" name="kk-kebutuhan" value="kegiatan" class="mt-0.5 size-4 accent-[#2277FF]">
                                <span>
                                    <span class="block text-sm font-semibold text-[#111]">Langsung kegiatan</span>
                                    <span class="block text-xs text-[#6E6E76]">Tanpa review dan tanpa surat. Statusnya langsung disetujui.</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <div>
                        <label for="kk-ket" class="mb-1.5 block text-sm font-semibold text-[#111]">Keterangan</label>
                        <textarea id="kk-ket" rows="3" maxlength="255" placeholder="Tuliskan keterangan singkat mengenai kegiatan ini"
                                  class="block w-full resize-none rounded-md border border-[#DEDEE3] px-3 py-2 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]"></textarea>
                    </div>
                </div>
            </div>

            {{-- ===== Tab 2: Isian Form ===== --}}
            <div id="tab-isian" role="tabpanel" aria-labelledby="tab-btn-isian" class="hidden pt-5">
                <h2 class="text-lg font-medium text-[#111]">Isian Form</h2>
                <p class="text-sm text-[#8A8A90]">Isian yang harus diisi pengaju saat mengajukan kegiatan kategori ini.</p>

                <ul id="ik-daftar" class="mt-4 space-y-2 empty:mt-0"></ul>

                <button type="button" id="ik-tambah"
                        class="mt-4 flex h-9 w-full items-center justify-center gap-1.5 rounded-md border border-dashed border-[#2277FF] text-sm font-semibold text-[#2277FF] hover:bg-[#F2F7FF] disabled:cursor-not-allowed disabled:opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
                    Tambah isian
                </button>
            </div>

            {{-- ===== Tab 3: Template Surat ===== --}}
            <div id="tab-template" role="tabpanel" aria-labelledby="tab-btn-template" class="hidden pt-5">
                <h2 class="text-lg font-medium text-[#111]">Template Surat Balasan <span class="text-sm font-normal text-[#8A8A90]">(Opsional)</span></h2>
                <p class="text-sm text-[#8A8A90]">Surat yang diterima pengaju setelah kegiatannya disetujui.</p>

                <div class="mt-4">
                    <label for="kk-template" class="sr-only">Template Surat Balasan</label>
                    <textarea id="kk-template" rows="12"
                              placeholder="Yth. Bapak/Ibu [NAMA_PENGAJU]&#10;di Tempat&#10;&#10;Yang bertanda tangan di bawah ini menerangkan bahwa:&#10;Nama: [NAMA_PENGAJU]&#10;Identitas: [IDENTITAS]&#10;Telah disetujui mengikuti [NAMA_KEGIATAN] pada [TANGGAL] di [LOKASI]."
                              class="block w-full resize-y rounded-md border border-[#DEDEE3] px-3 py-2 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]"></textarea>

                    <div id="kk-chips-wrap">
                        <p class="mt-2 text-xs text-[#8A8A90]">Klik variabel untuk menyisipkannya di posisi kursor:</p>
                        <div id="kk-chips" class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach (['NAMA_PENGAJU', 'IDENTITAS', 'NAMA_KEGIATAN', 'TANGGAL', 'LOKASI', 'NOMOR_SURAT', 'TANGGAL_SURAT'] as $v)
                                <button type="button" data-var="[{{ $v }}]"
                                        class="rounded-md bg-[#E3EDFF] px-2 py-1 text-[11px] font-semibold text-[#2277FF] hover:brightness-95">[{{ $v }}]</button>
                            @endforeach
                        </div>

                        <div id="kk-chips-isian-wrap" class="hidden">
                            <p class="mt-3 text-xs text-[#8A8A90]">Dari isian form kategori ini:</p>
                            <div id="kk-chips-isian" class="mt-1.5 flex flex-wrap gap-1.5"></div>
                        </div>
                    </div>
                </div>
            </div>

            <p id="kk-error" class="mt-4 hidden text-[13px] text-[#E5484D]"></p>

            <div class="mt-auto flex justify-end gap-2 pt-6">
                <a href="{{ route('admin.kegiatan.kategori-kegiatan') }}"
                   class="flex h-9 items-center rounded-md bg-[#FF4B4B] px-5 text-sm font-semibold text-white hover:bg-[#e63e3e]">Batal</a>
                <button type="button" id="kk-simpan"
                        class="h-9 rounded-md bg-[#2277FF] px-5 text-sm font-semibold text-white hover:bg-[#1b66e0] disabled:opacity-60">Simpan</button>
            </div>
        </section>

        {{-- ============ KANAN: PRATINJAU SURAT ============ --}}
        <section class="relative rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)] lg:min-h-[480px]">
          {{-- Di layar lebar, isi panel kanan mengikuti tinggi panel kiri dan area pratinjau digulir di dalamnya --}}
          <div class="flex flex-col lg:absolute lg:inset-6">
          {{-- Pratinjau form pengaju (tab Isian Form) --}}
          <div id="pv-form-wrap" hidden class="flex min-h-0 flex-col lg:flex-1">
            <h2 class="text-lg font-medium text-[#111]">Pratinjau Form Pengaju</h2>
            <p class="text-sm text-[#8A8A90]">Begini isian kategori ini akan tampil bagi pengaju.</p>

            <div class="mt-4 max-h-[75vh] overflow-y-auto rounded-md bg-gray-100 p-4 lg:max-h-none lg:min-h-0 lg:flex-1">
                <div class="rounded-lg bg-white p-5">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-[#2563EB] text-xs font-semibold text-white">+</span>
                        <div>
                            <div class="text-[15px] font-semibold text-[#111]">Data tambahan</div>
                            <div class="text-[13px] text-[#6E6E76]">Lengkapi isian berikut untuk kategori <span id="pv-form-kategori" class="font-medium">ini</span>.</div>
                        </div>
                    </div>
                    <div id="pv-form" class="mt-4 space-y-4"></div>
                    <p id="pv-form-kosong" class="mt-4 text-sm italic text-[#A8A8A8]">Belum ada isian. Bagian ini tidak akan tampil di form pengaju.</p>
                </div>
            </div>
            <p class="mt-2 text-xs text-[#8A8A90]">Bagian ini tampil setelah bagian bawaan form (misalnya anggota tim dan dokumen).</p>
          </div>

          {{-- Kategori tanpa surat --}}
          <div id="pv-nosurat" hidden class="flex min-h-0 flex-col lg:flex-1">
            <h2 class="text-lg font-medium text-[#111]">Pratinjau Surat</h2>
            <div class="mt-4 rounded-md bg-gray-100 px-6 py-16 text-center lg:flex lg:flex-1 lg:flex-col lg:justify-center">
                <p class="text-sm font-medium text-[#111]">Kategori ini tidak memakai surat</p>
                <p class="mx-auto mt-1 max-w-xs text-xs text-[#6E6E76]">Pengajuan langsung berstatus disetujui. Ubah Kebutuhan menjadi "Butuh surat" untuk membuat template surat.</p>
            </div>
          </div>

          {{-- Pratinjau surat (tab lain) --}}
          <div id="pv-surat-wrap" class="flex min-h-0 flex-col lg:flex-1">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-medium text-[#111]">Pratinjau Surat</h2>
                    <p class="text-sm text-[#8A8A90]">Diperbarui otomatis saat template diketik</p>
                </div>
                <label class="flex cursor-pointer items-center gap-2 text-xs text-[#55555C]">
                    <input id="kk-raw" type="checkbox" class="size-4 accent-[#2277FF]">
                    Tampilkan variabel asli
                </label>
            </div>

            <div class="mt-4 max-h-[75vh] overflow-y-auto rounded-md bg-gray-100 p-4 lg:max-h-none lg:min-h-0 lg:flex-1">
                <article class="mx-auto min-h-[520px] w-full bg-white px-8 py-8 font-serif text-[12px] leading-[1.6] text-[#0A0A0A] shadow-sm">
                    <header class="relative border-b border-gray-400 pb-2" style="font-family:'Times New Roman',Times,serif">
                        <img src="{{ asset('images/logo.png') }}" alt="" class="absolute left-1 top-1/2 size-10 -translate-y-[56%] object-contain" onerror="this.style.visibility='hidden'">
                        <div class="px-[50px] text-center leading-snug">
                            <div class="text-[7.5px] uppercase">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</div>
                            <div class="text-[7.5px] uppercase">Politeknik Negeri Semarang</div>
                            <div class="mt-0.5 text-[6.5px]">Jalan Prof. H. Soedarto, S.H., Tembalang, Semarang 50275</div>
                            <div class="text-[6.5px]">Telepon (024) 7473417, 7499585, 7499586</div>
                            <div class="text-[6.5px]">Laman www.polines.ac.id, Surel sekretariat@polines.ac.id</div>
                        </div>
                    </header>

                    <div class="mt-5 text-right">Semarang, <span id="pv-tgl-surat"></span></div>

                    <table class="mt-2 text-[12px]">
                        <tbody>
                            <tr><td class="pr-3 align-top">Nomor</td><td class="pr-2 align-top">:</td><td id="pv-nomor" class="align-top"></td></tr>
                            <tr><td class="pr-3 align-top">Lampiran</td><td class="pr-2 align-top">:</td><td class="align-top">2 berkas</td></tr>
                            <tr><td class="pr-3 align-top">Hal</td><td class="pr-2 align-top">:</td><td class="align-top font-bold">Persetujuan Kegiatan</td></tr>
                        </tbody>
                    </table>

                    <div id="pv-isi" class="mt-5 whitespace-pre-line text-justify"></div>

                    <div class="mt-10 flex justify-end">
                        <div class="w-fit" style="text-align:left">
                            <div>Ketua Program Studi,</div>
                            <div class="h-14"></div>
                            <div><span class="font-bold underline">Nama Kaprodi</span></div>
                            <div>NIP. 198001012010101001</div>
                        </div>
                    </div>
                </article>
            </div>
            <p class="mt-2 text-xs text-[#8A8A90]">Data pada pratinjau hanya contoh. Surat asli memakai data pengajuan.</p>
          </div>
          </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const URL_INDEX  = @json(route('admin.kegiatan.kategori-kegiatan'));
    const URL_STORE  = @json(route('admin.kegiatan.kategori-kegiatan.store'));
    const URL_UPDATE = @json(route('admin.kegiatan.kategori-kegiatan.update', ['id' => '__ID__']));
    const CSRF = @json(csrf_token());
    const ROW  = @json($row);

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    // ---------- Tab ----------
    const TABS = ['data', 'isian', 'template'];
    const kebutuhan = () => document.querySelector('input[name="kk-kebutuhan"]:checked')?.value ?? 'surat';
    let tabAktif = 'data';

    function bukaTab(nama) {
        // Kategori tanpa surat tidak punya tab Template Surat
        if (nama === 'template' && kebutuhan() === 'kegiatan') nama = 'data';
        tabAktif = nama;
        TABS.forEach((t) => {
            const aktif = t === nama;
            $('tab-' + t).classList.toggle('hidden', !aktif);
            $('tab-btn-' + t).setAttribute('aria-selected', aktif ? 'true' : 'false');
            $('tab-btn-' + t).tabIndex = aktif ? 0 : -1;
        });
        // Panel kanan: pratinjau form untuk tab Isian, pratinjau surat untuk tab lain
        const tanpaSurat = kebutuhan() === 'kegiatan';
        $('pv-form-wrap').hidden = nama !== 'isian';
        $('pv-surat-wrap').hidden = nama === 'isian' || tanpaSurat;
        $('pv-nosurat').hidden = nama === 'isian' || !tanpaSurat;
        $('tab-btn-template').classList.toggle('hidden', tanpaSurat);
    }
    document.querySelectorAll('input[name="kk-kebutuhan"]').forEach((r) => r.addEventListener('change', () => bukaTab(tabAktif)));
    document.querySelector('[role="tablist"]').addEventListener('click', (e) => {
        const b = e.target.closest('[data-tab]');
        if (b) bukaTab(b.dataset.tab);
    });

    // ---------- Data contoh untuk pratinjau surat ----------
    const tglSurat = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    const SAMPLE = {
        'NAMA_PENGAJU':  'Kuwat Santoso',
        'IDENTITAS':     '3.34.23.0001',
        'NAMA_KEGIATAN': 'Seminar Nasional Kecerdasan Buatan',
        'TANGGAL':       '4 Februari 2026',
        'LOKASI':        'Politeknik Negeri Semarang',
        'NOMOR_SURAT':   '123/PL4.5/KP/2026',
        'TANGGAL_SURAT': tglSurat,
    };

    $('pv-tgl-surat').textContent = tglSurat;
    $('pv-nomor').textContent = SAMPLE.NOMOR_SURAT;

    // ======================================================================
    // ISIAN FORM (dibuat admin per kategori)
    // ======================================================================
    const TIPE = {
        teks: 'Teks pendek', paragraf: 'Teks panjang', angka: 'Angka',
        tanggal: 'Tanggal', pilihan: 'Pilihan', berkas: 'Unggah berkas',
    };
    const RESERVED = Object.keys(SAMPLE);
    const MAKS_ISIAN = 20;
    const IKON = {
        edit:  '<path d="M4 20h4l10.5-10.5a2.83 2.83 0 0 0-4-4L4 16v4M13.5 6.5l4 4"/>',
        hapus: '<path d="M4 7h16M10 11v6M14 11v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/>',
        naik:  '<path d="M6 15l6-6l6 6"/>',
        turun: '<path d="M6 9l6 6l6-6"/>',
    };
    const ikon = (n, ukuran = 15) =>
        `<svg xmlns="http://www.w3.org/2000/svg" width="${ukuran}" height="${ukuran}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${IKON[n]}</svg>`;

    // Status di memori. `terkunci` = kunci variabel sudah tetap (tidak ikut berubah saat label diedit)
    let isian = (Array.isArray(ROW?.isian) ? ROW.isian : []).map((f) => ({
        key: f.kunci, label: f.label ?? '', tipe: f.tipe in TIPE ? f.tipe : 'teks', wajib: !!f.wajib,
        jumlah: (f.jumlah === 1 || f.jumlah === '1' || f.jumlah === 'banyak') ? 1 : 0,
        bantuan: f.bantuan ?? '', opsiText: (f.opsi ?? []).join('\n'), terkunci: true, buka: false,
    }));

    const opsiDari = (f) => [...new Set(f.opsiText.split('\n').map((x) => x.trim()).filter(Boolean))];

    function buatKunci(label, selfIdx) {
        let dasar = label.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase()
            .replace(/[^A-Z0-9]+/g, '_').replace(/^_+|_+$/g, '').slice(0, 40) || 'ISIAN';
        if (/^\d/.test(dasar)) dasar = 'ISIAN_' + dasar;
        const dipakai = new Set([...RESERVED, ...isian.filter((f, i) => i !== selfIdx && f.key).map((f) => f.key)]);
        let k = dasar, n = 2;
        while (dipakai.has(k)) k = dasar + '_' + n++;
        return k;
    }

    const meta = (f) => `${TIPE[f.tipe]}${f.jumlah === 1 ? ' · Lebih dari satu' : ''}${f.wajib ? ' · Wajib' : ''}${f.key ? ' · [' + f.key + ']' : ''}`;

    function renderDaftar() {
        const ul = $('ik-daftar');
        ul.innerHTML = isian.map((f, i) => {
            const editor = !f.buka ? '' : `
                <div class="space-y-3 border-t border-[#EBEBEF] px-3 pb-3 pt-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#111]">Nama isian</label>
                        <input data-f="label" type="text" maxlength="100" value="${esc(f.label)}" placeholder="Contoh: Nama tim"
                               class="h-9 w-full rounded-md border border-[#DEDEE3] px-3 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]">
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#111]">Tipe</label>
                            <select data-f="tipe" class="h-9 w-full rounded-md border border-[#DEDEE3] bg-white px-2 text-sm outline-none focus:border-[#2277FF]">
                                ${Object.entries(TIPE).map(([v, n]) => `<option value="${v}" ${v === f.tipe ? 'selected' : ''}>${n}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#111]">Jumlah isian</label>
                            <select data-f="jumlah" class="h-9 w-full rounded-md border border-[#DEDEE3] bg-white px-2 text-sm outline-none focus:border-[#2277FF]">
                                <option value="0" ${f.jumlah === 0 ? 'selected' : ''}>Satu isian</option>
                                <option value="1" ${f.jumlah === 1 ? 'selected' : ''}>Lebih dari satu</option>
                            </select>
                        </div>
                    </div>
                    <p class="-mt-1 text-xs text-[#8A8A90]">${f.jumlah === 1
                        ? (f.tipe === 'berkas' ? 'Pengaju boleh mengunggah lebih dari satu berkas.' : 'Pengaju boleh menambah isian ini lebih dari satu kali.')
                        : 'Pengaju mengisi isian ini satu kali.'}</p>
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-[#111]">
                        <input data-f="wajib" type="checkbox" class="size-4 accent-[#2277FF]" ${f.wajib ? 'checked' : ''}> Wajib diisi
                    </label>
                    ${f.tipe === 'pilihan' ? `
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#111]">Daftar pilihan <span class="font-normal text-[#8A8A90]">(satu per baris, minimal 2)</span></label>
                        <textarea data-f="opsi" rows="4" placeholder="Provinsi&#10;Nasional&#10;Internasional"
                                  class="block w-full resize-y rounded-md border border-[#DEDEE3] px-3 py-2 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]">${esc(f.opsiText)}</textarea>
                    </div>` : ''}
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#111]">Petunjuk <span class="font-normal text-[#8A8A90]">(opsional)</span></label>
                        <input data-f="bantuan" type="text" maxlength="150" value="${esc(f.bantuan)}" placeholder="Teks kecil di bawah nama isian"
                               class="h-9 w-full rounded-md border border-[#DEDEE3] px-3 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" data-aksi="buka" class="text-xs font-semibold text-[#2277FF] hover:underline">Selesai</button>
                    </div>
                </div>`;

            const tombol = 'flex size-7 items-center justify-center rounded text-[#55555C] hover:bg-[#F1F1F4] disabled:opacity-30 disabled:hover:bg-transparent';
            return `
            <li data-i="${i}" class="rounded-md border ${f.buka ? 'border-[#2277FF]' : 'border-[#DEDEE3]'} bg-white">
                <div class="flex items-center gap-1 px-2 py-2">
                    <div class="flex flex-col">
                        <button type="button" data-aksi="naik" aria-label="Naikkan" class="${tombol} !h-4" ${i === 0 ? 'disabled' : ''}>${ikon('naik', 14)}</button>
                        <button type="button" data-aksi="turun" aria-label="Turunkan" class="${tombol} !h-4" ${i === isian.length - 1 ? 'disabled' : ''}>${ikon('turun', 14)}</button>
                    </div>
                    <button type="button" data-aksi="buka" class="min-w-0 flex-1 px-1 text-left">
                        <div data-ref="judul" class="truncate text-sm font-semibold text-[#111]">${f.label.trim() ? esc(f.label) : '<span class="font-normal italic text-[#A8A8A8]">Isian baru</span>'}</div>
                        <div data-ref="meta" class="truncate text-xs text-[#8A8A90]">${esc(meta(f))}</div>
                    </button>
                    <button type="button" data-aksi="buka" aria-label="Ubah isian" class="${tombol}">${ikon('edit')}</button>
                    <button type="button" data-aksi="hapus" aria-label="Hapus isian" class="${tombol} hover:!bg-[#FFE5E5] hover:!text-[#E5484D]">${ikon('hapus')}</button>
                </div>
                ${editor}
            </li>`;
        }).join('');

        $('ik-tambah').disabled = isian.length >= MAKS_ISIAN;
        $('ik-tambah').lastChild.textContent = isian.length >= MAKS_ISIAN ? ` Maksimal ${MAKS_ISIAN} isian` : ' Tambah isian';

        const jml = $('isian-jumlah');
        jml.textContent = isian.length;
        jml.classList.toggle('hidden', isian.length === 0);

        renderChipIsian();
        renderForm();
    }

    function renderChipIsian() {
        const ada = isian.filter((f) => f.key && f.label.trim());
        $('kk-chips-isian-wrap').classList.toggle('hidden', ada.length === 0);
        $('kk-chips-isian').innerHTML = ada.map((f) => `
            <button type="button" data-var="[${esc(f.key)}]"
                    class="rounded-md bg-[#E6F6EC] px-2 py-1 text-[11px] font-semibold text-[#1E8E4A] hover:brightness-95">[${esc(f.key)}]</button>`).join('');
    }

    // Pratinjau form pengaju
    function renderForm() {
        const ada = isian.filter((f) => f.label.trim());
        $('pv-form-kosong').classList.toggle('hidden', ada.length > 0);
        $('pv-form-kategori').textContent = $('kk-nama').value.trim() || 'ini';

        const dasar = 'h-10 w-full rounded-lg border border-[#DEDEE3] bg-white px-3 text-sm text-[#111] placeholder:text-[#A8A8A8]';
        $('pv-form').innerHTML = ada.map((f) => {
            let kontrol;
            switch (f.tipe) {
                case 'paragraf': kontrol = `<textarea rows="3" disabled class="block w-full resize-none rounded-lg border border-[#DEDEE3] bg-white px-3 py-2 text-sm"></textarea>`; break;
                case 'angka':    kontrol = `<input type="number" disabled placeholder="0" class="${dasar}">`; break;
                case 'tanggal':  kontrol = `<input type="date" disabled class="${dasar}">`; break;
                case 'pilihan':  kontrol = `<select disabled class="${dasar}"><option>Pilih ${esc(f.label.toLowerCase())}</option>${opsiDari(f).map((o) => `<option>${esc(o)}</option>`).join('')}</select>`; break;
                case 'berkas':   kontrol = `<div class="flex h-16 items-center justify-center rounded-lg border border-dashed border-[#C9C9D1] text-sm text-[#6E6E76]">${f.jumlah === 1 ? 'Pilih beberapa berkas atau seret ke sini' : 'Pilih berkas atau seret ke sini'}</div>`; break;
                default:         kontrol = `<input type="text" disabled class="${dasar}">`;
            }
            // Isian "lebih dari satu" (selain berkas): kontrol + tombol tambah, seperti Anggota tim
            if (f.jumlah === 1 && f.tipe !== 'berkas') {
                kontrol += `<div class="mt-2"><span class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-[#2563EB] px-3 text-[13px] font-semibold text-[#2563EB]">+ Tambah ${esc(f.label.trim().toLowerCase())}</span></div>`;
            }
            return `
            <div>
                <div class="text-sm font-semibold text-[#111]">${esc(f.label)}${f.wajib ? ' <span class="text-[#E5484D]">*</span>' : ''}</div>
                ${f.bantuan.trim() ? `<div class="text-[13px] text-[#6E6E76]">${esc(f.bantuan)}</div>` : ''}
                <div class="mt-1.5 pointer-events-none">${kontrol}</div>
            </div>`;
        }).join('');
    }

    function indeksDari(el) {
        const li = el.closest('li[data-i]');
        return li ? Number(li.dataset.i) : -1;
    }

    function tambahIsian(awal = {}) {
        if (isian.length >= MAKS_ISIAN) return;
        isian.forEach((f) => { f.buka = false; });
        const f = {
            key: '', label: awal.label ?? '', tipe: awal.tipe ?? 'teks', wajib: false, jumlah: awal.jumlah ?? 0,
            bantuan: '', opsiText: (awal.opsi ?? []).join('\n'), terkunci: false, buka: !awal.label,
        };
        if (f.label) { f.key = buatKunci(f.label, -1); f.terkunci = true; }
        isian.push(f);
        renderDaftar();
        if (f.buka) $('ik-daftar').querySelector('li:last-child [data-f="label"]')?.focus();
    }

    $('ik-tambah').addEventListener('click', () => tambahIsian());

    $('ik-daftar').addEventListener('click', (e) => {
        const b = e.target.closest('[data-aksi]');
        const i = indeksDari(e.target);
        if (!b || i < 0) return;

        switch (b.dataset.aksi) {
            case 'buka': {
                const baru = !isian[i].buka;
                isian.forEach((f) => { f.buka = false; });
                isian[i].buka = baru;
                renderDaftar();
                if (baru) $('ik-daftar').querySelector(`li[data-i="${i}"] [data-f="label"]`)?.focus();
                break;
            }
            case 'hapus':
                isian.splice(i, 1);
                renderDaftar();
                break;
            case 'naik':
            case 'turun': {
                const j = b.dataset.aksi === 'naik' ? i - 1 : i + 1;
                if (j < 0 || j >= isian.length) break;
                [isian[i], isian[j]] = [isian[j], isian[i]];
                renderDaftar();
                break;
            }
        }
    });

    // Ketikan di editor isian: perbarui status + bagian yang terlihat tanpa menggambar ulang (fokus tetap)
    function segarkanBaris(i) {
        const li = $('ik-daftar').querySelector(`li[data-i="${i}"]`);
        if (!li) return;
        const f = isian[i];
        li.querySelector('[data-ref="judul"]').innerHTML = f.label.trim()
            ? esc(f.label) : '<span class="font-normal italic text-[#A8A8A8]">Isian baru</span>';
        li.querySelector('[data-ref="meta"]').textContent = meta(f);
    }

    $('ik-daftar').addEventListener('input', (e) => {
        const i = indeksDari(e.target), n = e.target.dataset.f;
        if (i < 0 || !n) return;
        const f = isian[i];

        if (n === 'label') {
            f.label = e.target.value;
            if (!f.terkunci) f.key = f.label.trim() ? buatKunci(f.label, i) : '';
        } else if (n === 'bantuan') {
            f.bantuan = e.target.value;
        } else if (n === 'opsi') {
            f.opsiText = e.target.value;
        } else {
            return;
        }
        segarkanBaris(i);
        renderChipIsian();
        renderForm();
    });

    $('ik-daftar').addEventListener('change', (e) => {
        const i = indeksDari(e.target), n = e.target.dataset.f;
        if (i < 0 || !n) return;
        const f = isian[i];

        if (n === 'label' && f.label.trim()) {
            f.terkunci = true; // kunci variabel tetap setelah nama isian selesai diketik
        } else if (n === 'wajib') {
            f.wajib = e.target.checked;
            segarkanBaris(i);
            renderForm();
        } else if (n === 'tipe') {
            f.tipe = e.target.value;
            renderDaftar(); // daftar pilihan muncul / hilang
            $('ik-daftar').querySelector(`li[data-i="${i}"] [data-f="tipe"]`)?.focus();
        } else if (n === 'jumlah') {
            f.jumlah = e.target.value === '1' ? 1 : 0;
            renderDaftar(); // teks penjelasan ikut berganti
            $('ik-daftar').querySelector(`li[data-i="${i}"] [data-f="jumlah"]`)?.focus();
        }
    });

    $('kk-nama').addEventListener('input', renderForm);

    // ---------- Pratinjau surat ----------
    function nilaiContoh(nama) {
        if (nama in SAMPLE) return SAMPLE[nama];
        const f = isian.find((x) => x.key === nama && x.label.trim());
        if (!f) return null;
        const c = 'Contoh ' + f.label.trim().toLowerCase();
        return f.jumlah === 1 ? `${c} 1, ${c} 2` : c;
    }

    function renderPratinjau() {
        const raw = $('kk-raw').checked;
        const teks = $('kk-template').value;

        if (!teks.trim()) {
            $('pv-isi').innerHTML = '<span class="italic text-[#A8A8A8]">Belum ada template. Surat akan memakai teks bawaan sistem.</span>';
            return;
        }

        // Escape dulu (aman XSS), baru ganti variabel dengan span
        $('pv-isi').innerHTML = esc(teks).replace(/\[([A-Z][A-Z0-9_]*)\]/g, (m, nama) => {
            const nilai = nilaiContoh(nama);
            if (nilai === null) return m;
            return raw
                ? `<span class="rounded bg-[#E3EDFF] px-1 font-sans text-[11px] font-semibold text-[#2277FF]">${m}</span>`
                : `<span class="rounded bg-[#FFF3C4] px-0.5">${esc(nilai)}</span>`;
        });
    }

    $('kk-template').addEventListener('input', renderPratinjau);
    $('kk-raw').addEventListener('change', renderPratinjau);

    // ---------- Sisipkan variabel di posisi kursor ----------
    $('kk-chips-wrap').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-var]');
        if (!b) return;
        const ta = $('kk-template');
        const awal = ta.selectionStart ?? ta.value.length;
        const akhir = ta.selectionEnd ?? ta.value.length;
        ta.value = ta.value.slice(0, awal) + b.dataset.var + ta.value.slice(akhir);
        const pos = awal + b.dataset.var.length;
        ta.focus();
        ta.setSelectionRange(pos, pos);
        renderPratinjau();
    });

    // ---------- Isi form saat edit ----------
    if (ROW) {
        $('kk-nama').value = ROW.name ?? '';
        $('kk-jenis').value = ROW.jenis;
        $('kk-ket').value = ROW.description ?? '';
        $('kk-template').value = ROW.template_surat ?? '';
    }
    // Default "Butuh surat" (perilaku sekarang); data lama tanpa kebutuhan ikut default
    const kebAwal = ROW?.kebutuhan === 'kegiatan' ? 'kegiatan' : 'surat';
    document.querySelector(`input[name="kk-kebutuhan"][value="${kebAwal}"]`).checked = true;
    renderDaftar();
    renderPratinjau();
    bukaTab('data');

    // ---------- Simpan ----------
    function tampilError(pesan) {
        const el = $('kk-error');
        el.textContent = pesan;
        el.classList.toggle('hidden', !pesan);
    }

    async function kirim(url, method, body) {
        const res = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body),
        });
        const json = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw new Error(json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Terjadi kesalahan.'));
        }
        return json;
    }

    async function simpan() {
        const body = {
            name: $('kk-nama').value.trim(),
            jenis: $('kk-jenis').value,
            kebutuhan: kebutuhan(), // 'surat' | 'kegiatan' (KEBUTUHAN)
            description: $('kk-ket').value.trim(),
            template_surat: $('kk-template').value.trim(),
            isian: isian.filter((f) => f.label.trim()).map((f) => ({
                kunci: f.key, label: f.label.trim(), tipe: f.tipe, wajib: f.wajib,
                jumlah: f.jumlah, // 0 = satu isian, 1 = lebih dari satu (JUMLAH_ISIAN)
                bantuan: f.bantuan.trim(), opsi: f.tipe === 'pilihan' ? opsiDari(f) : [],
            })),
        };
        if (!body.name) { bukaTab('data'); tampilError('Nama kategori wajib diisi.'); $('kk-nama').focus(); return; }

        // Isian dengan tipe Pilihan butuh minimal 2 pilihan
        const bermasalah = isian.findIndex((f) => f.label.trim() && f.tipe === 'pilihan' && opsiDari(f).length < 2);
        if (bermasalah >= 0) {
            isian.forEach((f, i) => { f.buka = i === bermasalah; });
            renderDaftar();
            bukaTab('isian');
            tampilError(`Isian "${isian[bermasalah].label.trim()}" bertipe Pilihan, isi minimal 2 pilihan.`);
            return;
        }

        const tombol = $('kk-simpan');
        tombol.disabled = true;
        tampilError('');
        try {
            if (ROW) {
                await kirim(URL_UPDATE.replace('__ID__', ROW.id), 'PUT', body);
            } else {
                await kirim(URL_STORE, 'POST', body);
            }
            window.location.href = URL_INDEX + '?status=' + (ROW ? 'diperbarui' : 'ditambahkan');
        } catch (e) {
            tampilError(e.message);
            tombol.disabled = false;
        }
    }

    $('kk-simpan').addEventListener('click', simpan);
    $('kk-nama').addEventListener('keydown', (e) => { if (e.key === 'Enter') simpan(); });
})();
</script>
@endpush