{{--
    resources/views/admin/kegiatan-tambah.blade.php
    Form "Tambah Kegiatan": 1 Informasi kegiatan, 2 Anggota kegiatan, 3 Unggah dokumen.
    Anggota & dokumen dinamis (JavaScript biasa). Simpan = SIMULASI (lihat controller, TODO-DB).
--}}
@extends(request()->routeIs('kaprodi.*') ? 'layouts.kaprodi' : 'layouts.admin')

@section('title', 'Tambah Kegiatan')

@php
    $card   = 'flex flex-col gap-4 rounded-lg border border-black/[0.06] bg-white p-4';
    $label  = 'text-base font-medium text-[#0A0A0A]';
    $field  = 'h-10 w-full rounded-lg border border-[#E8E8EC] bg-white px-3 text-base text-[#2E2E2E] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';
    $select = $field.' appearance-none pr-10';
    $error  = 'text-sm text-[#FF3F3F]';
    $chev   = '<svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
@endphp

@section('content')
<main class="flex flex-col gap-8 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Tambah Kegiatan</h1>
        <p class="text-xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8] sm:text-2xl">Dashboard / Kegiatan / Tambah</p>
    </div>

    <form method="POST" action="{{ route(request()->routeIs('kaprodi.*') ? 'kaprodi.kegiatan.store' : 'admin.kegiatan.store') }}" enctype="multipart/form-data" class="flex flex-col gap-6 rounded-lg bg-white p-4 shadow-sm sm:p-6" novalidate>
        @csrf

        {{-- ============ 1. Informasi Kegiatan ============ --}}
        <section class="{{ $card }}">
            <div class="flex items-center gap-[11px]">
                <span class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">1</span>
                <h2 class="text-sm font-semibold text-[#0A0A0A]">Informasi Kegiatan</h2>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex min-w-0 flex-col gap-2">
                    <label for="kategori" class="{{ $label }}">Kategori</label>
                    <div class="relative">
                        <select id="kategori" name="kategori" class="{{ $select }}">
                            <option value="">Pilih kategori</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k }}" @selected(old('kategori') === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                        {!! $chev !!}
                    </div>
                    @error('kategori') <p class="{{ $error }}">{{ $message }}</p> @enderror
                </div>

                <div class="flex min-w-0 flex-col gap-2">
                    <label for="jenis_kegiatan" class="{{ $label }}">Jenis kegiatan</label>
                    <div class="relative">
                        <select id="jenis_kegiatan" name="jenis_kegiatan" class="{{ $select }}">
                            <option value="">Pilih jenis kegiatan</option>
                            @foreach ($jenisKegiatan as $j)
                                <option value="{{ $j }}" @selected(old('jenis_kegiatan') === $j)>{{ $j }}</option>
                            @endforeach
                        </select>
                        {!! $chev !!}
                    </div>
                    @error('jenis_kegiatan') <p class="{{ $error }}">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex min-w-0 flex-col gap-2">
                <label for="nama_kegiatan" class="{{ $label }}">Nama kegiatan</label>
                <input id="nama_kegiatan" name="nama_kegiatan" type="text" value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Seminar Nasional AI 2026" class="{{ $field }}">
                @error('nama_kegiatan') <p class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex min-w-0 flex-col gap-2">
                    <label for="penyelenggara" class="{{ $label }}">Penyelenggara</label>
                    <input id="penyelenggara" name="penyelenggara" type="text" value="{{ old('penyelenggara') }}" placeholder="Nama penyelenggara" class="{{ $field }}">
                    @error('penyelenggara') <p class="{{ $error }}">{{ $message }}</p> @enderror
                </div>

                <div class="flex min-w-0 flex-col gap-2">
                    <label for="tahun" class="{{ $label }}">Tahun penyelenggaraan</label>
                    <div class="relative">
                        <select id="tahun" name="tahun" class="{{ $select }}">
                            @foreach ($tahun as $t)
                                <option value="{{ $t }}" @selected((int) old('tahun', date('Y')) === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        {!! $chev !!}
                    </div>
                    @error('tahun') <p class="{{ $error }}">{{ $message }}</p> @enderror
                </div>

                <div class="flex min-w-0 flex-col gap-2">
                    <label for="tanggal_mulai" class="{{ $label }}">Tanggal mulai</label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai') }}" class="{{ $field }}">
                    @error('tanggal_mulai') <p class="{{ $error }}">{{ $message }}</p> @enderror
                </div>

                <div class="flex min-w-0 flex-col gap-2">
                    <label for="tanggal_selesai" class="{{ $label }}">Tanggal selesai</label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai') }}" class="{{ $field }}">
                    @error('tanggal_selesai') <p class="{{ $error }}">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- ============ 2. Anggota Kegiatan ============ --}}
        <section class="{{ $card }}">
            <div class="flex items-center gap-[11px]">
                <span class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">2</span>
                <h2 class="text-sm font-semibold text-[#0A0A0A]">Anggota Kegiatan</h2>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                <div class="relative min-w-0 flex-1">
                    <div class="flex items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 focus-within:border-[#2277FF]">
                        <svg class="size-5 shrink-0 text-[#A8A8A8]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                        <input id="cariAnggota" type="text" autocomplete="off" placeholder="Cari dosen atau mahasiswa..." class="h-10 min-w-0 flex-1 bg-transparent text-base outline-none placeholder:text-[#A8A8A8]">
                    </div>
                    <ul id="saranAnggota" class="absolute inset-x-0 top-[calc(100%+4px)] z-10 hidden max-h-60 overflow-y-auto rounded-lg border border-[#E8E8EC] bg-white py-1 shadow-lg"></ul>
                </div>
                <button type="button" id="btnTambahAnggota" class="inline-flex h-10 shrink-0 items-center justify-center gap-1 rounded-lg bg-[#2277FF] px-4 text-base font-medium text-white hover:bg-[#1A63D8]">
                    <span class="text-lg leading-none">+</span> Tambah Anggota
                </button>
            </div>
            <p id="pesanAnggota" class="{{ $error }} hidden"></p>
            @error('anggota') <p class="{{ $error }}">{{ $message }}</p> @enderror
            @error('anggota.*') <p class="{{ $error }}">{{ $message }}</p> @enderror

            <div class="overflow-x-auto rounded-md border border-[#00002F]/15 bg-white/80">
                <table class="w-full min-w-[480px] table-fixed border-collapse text-left text-sm text-[#1C2024]">
                    <thead>
                        <tr class="bg-[#000055]/[0.02]">
                            <th class="p-2 font-[510]">Nama Anggota</th>
                            <th class="w-40 p-2 font-[510]">Status</th>
                            <th class="w-24 p-2 font-[510]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyAnggota"></tbody>
                </table>
            </div>
            <div id="hiddenAnggota"></div>
        </section>

        {{-- ============ 3. Unggah Dokumen ============ --}}
        <section class="{{ $card }}">
            <div class="flex items-center gap-[11px]">
                <span class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">3</span>
                <h2 class="text-sm font-semibold text-[#0A0A0A]">Unggah Dokumen</h2>
            </div>

            <div id="daftarDokumen" class="flex flex-col gap-4"></div>

            <div>
                <button type="button" id="btnTambahDokumen" class="inline-flex h-9 items-center gap-1 rounded-lg border border-[#2277FF] bg-white px-3 text-sm font-medium text-[#2277FF] hover:bg-[#F1F6FF]">
                    <span class="text-base leading-none">+</span> Tambah dokumen
                </button>
            </div>
        </section>

        {{-- Aksi --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route(request()->routeIs('kaprodi.*') ? 'kaprodi.kegiatan.index' : 'admin.kegiatan.index') }}" class="inline-flex h-10 items-center rounded-lg bg-[#FF3F3F] px-5 text-base font-medium text-white hover:bg-[#E63535]">Batal</a>
            <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-[#2277FF] px-5 text-base font-medium text-white hover:bg-[#1A63D8]">Simpan</button>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script>
(function () {
    var KANDIDAT = @json($kandidat);
    var JENIS_DOK = @json($jenisDokumen);
    var OLD_ANGGOTA = @json(old('anggota', []));
    var OLD_DOKUMEN = @json(old('dokumen', []));
    var ERR_DOK = @json($errors->getBag('default')->messages());

    var $ = function (id) { return document.getElementById(id); };
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    /* =================== ANGGOTA =================== */
    var anggota = [];          // daftar key "Tipe:id"
    var terpilih = null;       // kandidat yang dipilih dari daftar saran
    var cari = $('cariAnggota'), saran = $('saranAnggota'), pesan = $('pesanAnggota');
    var tbody = $('tbodyAnggota'), hidden = $('hiddenAnggota');

    function infoKandidat(key) {
        for (var i = 0; i < KANDIDAT.length; i++) if (KANDIDAT[i].key === key) return KANDIDAT[i];
        return null;
    }
    function tampilPesan(t) { pesan.textContent = t || ''; pesan.classList.toggle('hidden', !t); }

    function renderAnggota() {
        tbody.innerHTML = anggota.length ? anggota.map(function (key) {
            var k = infoKandidat(key); if (!k) return '';
            return '<tr class="border-t border-[#00002F]/15">' +
                '<td class="truncate p-2">' + esc(k.nama) + '</td>' +
                '<td class="p-2">' + esc(k.tipe) + '</td>' +
                '<td class="p-2"><button type="button" data-hapus="' + esc(key) + '" title="Hapus" aria-label="Hapus anggota" class="rounded-md bg-[#FFD9D9] p-1.5 text-[#E5484D] hover:bg-[#FFC9C9]">' +
                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg></button></td></tr>';
        }).join('') : '<tr class="border-t border-[#00002F]/15"><td colspan="3" class="p-4 text-center text-sm text-[#A8A8A8]">Belum ada anggota.</td></tr>';

        hidden.innerHTML = anggota.map(function (key) {
            return '<input type="hidden" name="anggota[]" value="' + esc(key) + '">';
        }).join('');
    }

    function cocok(q) {
        q = q.toLowerCase();
        return KANDIDAT.filter(function (k) {
            return anggota.indexOf(k.key) === -1 && k.nama.toLowerCase().indexOf(q) !== -1;
        });
    }
    function tutupSaran() { saran.classList.add('hidden'); saran.innerHTML = ''; }

    function tambah(key) {
        if (anggota.indexOf(key) === -1) anggota.push(key);
        terpilih = null; cari.value = ''; tutupSaran(); tampilPesan(''); renderAnggota();
    }

    cari.addEventListener('input', function () {
        terpilih = null; tampilPesan('');
        var q = cari.value.trim();
        if (!q) { tutupSaran(); return; }
        var hasil = cocok(q).slice(0, 8);
        saran.innerHTML = hasil.length ? hasil.map(function (k) {
            return '<li><button type="button" data-pilih="' + esc(k.key) + '" class="flex w-full items-center justify-between gap-3 px-3 py-2 text-left text-sm hover:bg-[#F1F6FF]">' +
                   '<span class="truncate">' + esc(k.nama) + '</span><span class="shrink-0 text-xs text-[#80838D]">' + esc(k.tipe) + '</span></button></li>';
        }).join('') : '<li class="px-3 py-2 text-sm text-[#A8A8A8]">Tidak ditemukan.</li>';
        saran.classList.remove('hidden');
    });
    saran.addEventListener('click', function (e) {
        var b = e.target.closest('[data-pilih]'); if (!b) return;
        terpilih = infoKandidat(b.dataset.pilih);
        cari.value = terpilih.nama; tutupSaran(); cari.focus();
    });
    document.addEventListener('click', function (e) {
        if (!saran.contains(e.target) && e.target !== cari) tutupSaran();
    });
    cari.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('btnTambahAnggota').click(); }
    });
    $('btnTambahAnggota').addEventListener('click', function () {
        var q = cari.value.trim();
        if (terpilih && terpilih.nama === q) { tambah(terpilih.key); return; }
        if (!q) { tampilPesan('Ketik nama dosen atau mahasiswa, lalu pilih dari daftar.'); return; }
        var hasil = cocok(q);
        var persis = hasil.filter(function (k) { return k.nama.toLowerCase() === q.toLowerCase(); });
        if (persis.length === 1) { tambah(persis[0].key); return; }
        if (hasil.length === 1) { tambah(hasil[0].key); return; }
        tampilPesan(hasil.length ? 'Ada beberapa hasil. Pilih salah satu dari daftar.' : 'Nama tidak ditemukan.');
    });
    tbody.addEventListener('click', function (e) {
        var b = e.target.closest('[data-hapus]'); if (!b) return;
        anggota = anggota.filter(function (k) { return k !== b.dataset.hapus; });
        renderAnggota();
    });

    OLD_ANGGOTA.forEach(function (k) { if (infoKandidat(k) && anggota.indexOf(k) === -1) anggota.push(k); });
    renderAnggota();

    /* =================== DOKUMEN =================== */
    var daftar = $('daftarDokumen'), seq = 0;
    var FIELD = 'h-10 w-full rounded-lg border border-[#E8E8EC] bg-white px-3 text-base text-[#2E2E2E] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';
    var LABEL = 'text-base font-medium text-[#0A0A0A]';
    var CHEV = '<svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';

    function errDok(i, f) {
        var m = ERR_DOK['dokumen.' + i + '.' + f];
        return m ? '<p class="text-sm text-[#FF3F3F]">' + esc(m[0]) + '</p>' : '';
    }

    function kartuDokumen(n, v) {
        v = v || {};
        var opsi = '<option value="">Pilih jenis dokumen</option>' + JENIS_DOK.map(function (j) {
            return '<option value="' + esc(j) + '"' + (v.jenis === j ? ' selected' : '') + '>' + esc(j) + '</option>';
        }).join('');
        var p = 'dokumen[' + n + ']';
        return '<div class="dok overflow-hidden rounded-lg border border-black/[0.08] bg-white" data-n="' + n + '">' +
            '<div class="flex items-center justify-between bg-[#00205B] px-4 py-2.5 text-sm font-semibold text-white">' +
                '<span class="dok-judul">Dokumen</span>' +
                '<button type="button" data-hapus-dok class="hidden rounded-md px-2 py-0.5 text-xs font-medium text-white/80 hover:bg-white/10 hover:text-white">Hapus</button>' +
            '</div>' +
            '<div class="flex flex-col gap-4 p-4">' +
                '<div class="flex flex-col gap-2"><span class="' + LABEL + '">File</span>' +
                    '<label for="dok-file-' + n + '" class="flex min-h-[90px] cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border border-dashed border-[#4D8DFF] bg-[#F5F8FF] px-4 py-4 text-center hover:bg-[#EDF3FF]">' +
                        '<span class="flex items-center gap-2 text-base font-medium text-[#2277FF]">' +
                            '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" /><path d="M9 15l3 3l3 -3" /><path d="M12 12l0 9" /></svg>' +
                            '<span class="dok-file-nama">Pilih file dari perangkat</span></span>' +
                        '<span class="text-xs text-[#80838D]">pdf, jpg, jpeg, png, doc, docx, xls, xlsx, txt, maksimal 5 MB</span>' +
                    '</label>' +
                    '<input id="dok-file-' + n + '" name="' + p + '[file]" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt" class="sr-only">' +
                    errDok(n, 'file') +
                '</div>' +
                '<div class="grid gap-4 md:grid-cols-2">' +
                    '<div class="flex min-w-0 flex-col gap-2"><label for="dok-nama-' + n + '" class="' + LABEL + '">Nama dokumen</label>' +
                        '<input id="dok-nama-' + n + '" name="' + p + '[nama]" type="text" value="' + esc(v.nama) + '" placeholder="Contoh: Proposal pengabdian" class="' + FIELD + '">' + errDok(n, 'nama') + '</div>' +
                    '<div class="flex min-w-0 flex-col gap-2"><label for="dok-jenis-' + n + '" class="' + LABEL + '">Jenis dokumen</label>' +
                        '<div class="relative"><select id="dok-jenis-' + n + '" name="' + p + '[jenis]" class="' + FIELD + ' appearance-none pr-10">' + opsi + '</select>' + CHEV + '</div>' + errDok(n, 'jenis') + '</div>' +
                '</div>' +
                '<div class="flex flex-col gap-2"><label for="dok-ket-' + n + '" class="' + LABEL + '">Keterangan</label>' +
                    '<textarea id="dok-ket-' + n + '" name="' + p + '[keterangan]" rows="3" placeholder="Keterangan singkat tentang dokumen ini" class="w-full rounded-lg border border-[#E8E8EC] bg-white px-3 py-2 text-base outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]">' + esc(v.keterangan) + '</textarea>' + errDok(n, 'keterangan') + '</div>' +
                '<div class="flex flex-col gap-2"><label for="dok-url-' + n + '" class="' + LABEL + '">Tautan dokumen <span class="text-[#A8A8A8]">(opsional)</span></label>' +
                    '<input id="dok-url-' + n + '" name="' + p + '[tautan]" type="url" value="' + esc(v.tautan) + '" placeholder="https://" class="' + FIELD + '">' + errDok(n, 'tautan') + '</div>' +
            '</div></div>';
    }

    function nomori() {
        var kartu = daftar.querySelectorAll('.dok');
        kartu.forEach(function (el, i) {
            el.querySelector('.dok-judul').textContent = 'Dokumen ' + (i + 1);
            el.querySelector('[data-hapus-dok]').classList.toggle('hidden', kartu.length === 1);
        });
    }
    function tambahDokumen(v, idx) {
        // idx = indeks asli dari old input, supaya pesan error cocok dengan kartunya
        var n = (idx != null && !isNaN(idx)) ? parseInt(idx, 10) : seq;
        seq = Math.max(seq, n) + 1;
        daftar.insertAdjacentHTML('beforeend', kartuDokumen(n, v));
        nomori();
    }

    $('btnTambahDokumen').addEventListener('click', function () { tambahDokumen(); });
    daftar.addEventListener('click', function (e) {
        var b = e.target.closest('[data-hapus-dok]'); if (!b) return;
        b.closest('.dok').remove(); nomori();
    });
    daftar.addEventListener('change', function (e) {
        if (e.target.type !== 'file') return;
        var kartu = e.target.closest('.dok');
        var f = e.target.files && e.target.files[0];
        var nama = kartu.querySelector('.dok-file-nama');
        if (f && f.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5 MB.'); e.target.value = ''; nama.textContent = 'Pilih file dari perangkat'; return;
        }
        nama.textContent = f ? f.name : 'Pilih file dari perangkat';
    });

    var kunciLama = Object.keys(OLD_DOKUMEN || {});
    if (kunciLama.length) kunciLama.forEach(function (k) { tambahDokumen(OLD_DOKUMEN[k], k); }); else tambahDokumen();
})();
</script>
@endpush