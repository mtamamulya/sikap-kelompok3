{{--
    resources/views/admin/kegiatan.blade.php
    Kegiatan gabungan (Dosen | Mahasiswa | Prodi) menggunakan DataTables 2.x.
--}}
@extends('layouts.admin')

@section('title', 'Kegiatan')

@php
    $tabs = ['dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa', 'prodi' => 'Prodi'];
    $selectCls = 'h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] outline-none focus:border-[#2277FF] cursor-pointer';
    $chev = '<svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
@endphp

@section('content')
{{-- CSS DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<style>
    /* ---------- Filter: Search dan Dropdown Sejajar ---------- */
    .filter-grid-inline { display: grid; gap: 12px; grid-template-columns: minmax(0, 1fr); align-items: center; }
    @media (min-width: 1024px) { 
        .filter-grid-inline { grid-template-columns: 2fr repeat(2, minmax(0, 1fr)); } 
        .filter-grid-inline.has-kelas { grid-template-columns: 2fr repeat(3, minmax(0, 1fr)); }
    }

    #tabel-wrap .dt-layout-row { margin: 0; }
    #tabel-wrap .dt-layout-row.dt-layout-table {
        border: 1px solid rgb(0 0 47 / .15);
        border-radius: 6px;
        overflow: hidden;
        background: #fff;
    }
    #tabel-wrap .dt-layout-table .dt-layout-cell { width: 100%; padding: 0; overflow-x: auto; }

    #tabel { width: 100%; min-width: 960px; table-layout: fixed; border-collapse: collapse; margin: 0 !important; font-size: 14px; color: #1C2024; }

    #tabel.dataTable > thead > tr > th:nth-child(1) { width: 26%; }
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 17%; }
    #tabel.dataTable > thead > tr > th:nth-child(3) { width: 17%; }
    #tabel.dataTable > thead > tr > th:nth-child(4) { width: 17%; }
    #tabel.dataTable > thead > tr > th:nth-child(5) { width: 12%; }
    #tabel.dataTable > thead > tr > th:nth-child(6) { width: 11%; }

    #tabel.dataTable > thead > tr > th:first-child,
    #tabel.dataTable > tbody > tr > td:first-child:not(.dt-empty) { padding-left: 20px; }

    #tabel.dataTable > thead > tr > th {
        padding: 8px; font-weight: 510; text-align: left;
        background: #F9F9FC;
        border-bottom: 1px solid rgb(0 0 47 / .15);
    }
    #tabel.dataTable > thead > tr > th .dt-column-header {
        display: flex; flex-direction: row !important; justify-content: flex-start !important; align-items: center; gap: 6px;
    }
    #tabel.dataTable > thead > tr > th .dt-column-title { flex-grow: 0 !important; }

    #tabel.dataTable > tbody > tr > td {
        padding: 8px; text-align: left;
        border-top: 1px solid rgb(0 0 47 / .15);
        box-shadow: none;
    }
    #tabel.dataTable > tbody > tr:first-child > td { border-top: 0; }
    #tabel.dataTable > tbody > tr > td.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    #tabel-wrap .dt-layout-row:not(.dt-layout-table) { padding: 24px 0 0; justify-content: center; }
    #tabel-wrap .dt-layout-row:not(.dt-layout-table) .dt-layout-cell { width: 100%; padding: 0; display: flex; justify-content: center; }
    #tabel-wrap .dt-paging nav { display: flex; align-items: center; justify-content: center; gap: 8px; }

    #tabel-wrap .dt-paging .dt-paging-button {
        display: flex !important; align-items: center; justify-content: center;
        width: 32px; height: 32px; min-width: 32px; padding: 0 !important; margin: 0 !important;
        border: 1px solid #DFE3E8; border-radius: 4px !important;
        font-size: 14px; font-weight: 700; letter-spacing: -.16px; line-height: 1;
        box-shadow: none !important; cursor: pointer;
    }
    #tabel-wrap .dt-paging .dt-paging-button.disabled { cursor: not-allowed; opacity: .6; }

    #tabel-wrap .dt-paging .dt-paging-button:not(.previous):not(.next):not(.ellipsis),
    #tabel-wrap .dt-paging .dt-paging-button:not(.previous):not(.next):not(.ellipsis):hover {
        background: #fff !important; color: #0A0A0A !important; border-color: #DFE3E8 !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button:not(.previous):not(.next):not(.ellipsis):hover { border-color: #2277FF !important; }
    
    #tabel-wrap .dt-paging .dt-paging-button.current:not(.previous):not(.next):not(.ellipsis),
    #tabel-wrap .dt-paging .dt-paging-button.current:not(.previous):not(.next):not(.ellipsis):hover,
    #tabel-wrap .dt-paging .dt-paging-button[aria-current="page"]:not(.previous):not(.next):not(.ellipsis),
    #tabel-wrap .dt-paging .dt-paging-button[aria-current="page"]:not(.previous):not(.next):not(.ellipsis):hover {
        color: #2277FF !important; border-color: #2277FF !important; background: #fff !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button.ellipsis { border-color: transparent !important; background: none !important; color: #55555C !important; opacity: 1; cursor: default; }

    #tabel-wrap .dt-paging .dt-paging-button.previous,
    #tabel-wrap .dt-paging .dt-paging-button.next {
        font-size: 0 !important; background-repeat: no-repeat !important; background-position: center !important; background-size: 20px 20px !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button.previous,
    #tabel-wrap .dt-paging .dt-paging-button.previous.disabled:hover {
        background-color: #2277FF !important; border-color: #2277FF !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 6l-6 6l6 6'/%3E%3C/svg%3E") !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button.next,
    #tabel-wrap .dt-paging .dt-paging-button.next.disabled:hover {
        background-color: #CDDEF9 !important; border-color: #2277FF !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232277FF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 6l6 6l-6 6'/%3E%3C/svg%3E") !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button.previous:hover:not(.disabled) { background-color: #1A66E0 !important; border-color: #1A66E0 !important; }
    #tabel-wrap .dt-paging .dt-paging-button.next:hover:not(.disabled) { background-color: #BBD2F6 !important; }

    /* Modal Hapus */
    #modalHapus { position: fixed; inset: 0; margin: auto; height: fit-content; width: 380px; max-width: calc(100vw - 32px); padding: 0; border: 0; background: transparent; overflow: visible; }
    #modalHapus::backdrop { background: rgb(10 10 10 / .45); }
    #modalHapus[open] { animation: modalMasuk .15s ease-out; }
    @keyframes modalMasuk { from { opacity: 0; transform: translateY(8px) scale(.98); } to { opacity: 1; transform: none; } }
    .modal-kartu { padding: 24px; border-radius: 12px; background: #fff; text-align: center; box-shadow: 0 20px 50px rgb(0 0 0 / .2); }
    .modal-ikon { display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; margin: 0 auto 16px; border-radius: 9999px; background: #FFD9D9; color: #E5484D; }
    .modal-judul { font-size: 18px; font-weight: 700; line-height: 24px; color: #0A0A0A; }
    .modal-teks { margin-top: 8px; font-size: 14px; line-height: 20px; color: #55555C; }
    .modal-teks strong { font-weight: 600; color: #0A0A0A; word-break: break-word; }
    .modal-aksi { display: flex; gap: 12px; margin-top: 24px; }
    .modal-btn { flex: 1; height: 40px; border: 1px solid transparent; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .modal-btn-batal { background: #fff; border-color: #E8E8EC; color: #2E2E2E; }
    .modal-btn-batal:hover { background: #F9F9FB; }
    .modal-btn-hapus { background: #E5484D; color: #fff; }
    .modal-btn-hapus:hover { background: #D13A3F; }
</style>

<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Kegiatan</h1>
        <p class="text-xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8] sm:text-2xl">Dashboard / Kegiatan</p>
    </div>

    @if (session('success'))
        <div class="rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-8 rounded-lg bg-white p-4 pb-6 shadow-sm">
        
        {{-- Area Laporan & Ekspor Dinamis --}}
        <form id="formLaporan" method="GET" action="{{ route('admin.kegiatan.dosen.laporan') }}" class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-gray-50/30 p-5">
            <div class="flex flex-col gap-1">
                <h2 id="laporanJudul" class="text-lg font-bold text-gray-800">Laporan Data Kegiatan Dosen</h2>
                <p class="text-sm text-gray-500">Unduh rekap kegiatan berdasarkan kurun waktu (tahun).</p>
            </div>
            
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Dari tahun</label>
                    <div class="relative w-28">
                        <select name="dari" class="h-10 w-full appearance-none rounded-md border border-gray-300 bg-white px-3 pr-8 text-sm outline-none focus:border-blue-500 cursor-pointer">
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6l6 -6"/></svg>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600">Sampai tahun</label>
                    <div class="relative w-28">
                        <select name="sampai" class="h-10 w-full appearance-none rounded-md border border-gray-300 bg-white px-3 pr-8 text-sm outline-none focus:border-blue-500 cursor-pointer">
                            <option value="2026">2026</option>
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6l6 -6"/></svg>
                    </div>
                </div>
                
                <div class="flex gap-2 ml-2">
                    <button type="submit" name="format" value="pdf" class="flex h-10 items-center gap-1.5 rounded-md border border-red-200 px-4 text-sm font-semibold text-red-500 transition-colors hover:bg-red-50 cursor-pointer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 11l5 5l5 -5"/><path d="M12 4l0 12"/></svg>
                        PDF
                    </button>
                    <button type="submit" name="format" value="xlsx" class="flex h-10 items-center gap-1.5 rounded-md border border-green-200 px-4 text-sm font-semibold text-green-600 transition-colors hover:bg-green-50 cursor-pointer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 11l5 5l5 -5"/><path d="M12 4l0 12"/></svg>
                        Excel
                    </button>
                </div>
            </div>
        </form>

        {{-- Baris Tab (Kiri) & Tombol Tambah Kegiatan (Kanan) --}}
        <div class="flex flex-col border-b border-[#0A0A0A]/[0.06] pb-3 lg:flex-row lg:items-end lg:justify-between lg:pb-0">
            <div id="tabs" role="tablist" class="flex gap-2 overflow-x-auto">
                @foreach ($tabs as $key => $nama)
                    <button type="button" role="tab" data-tab="{{ $key }}" aria-selected="{{ $tab === $key ? 'true' : 'false' }}"
                            class="group relative flex h-10 shrink-0 items-center gap-2 rounded-t-md px-4 text-sm text-[#0A0A0A]/50 transition-colors hover:bg-gray-50 hover:text-[#0A0A0A]/80 aria-selected:bg-[#F2F6FC] aria-selected:font-semibold aria-selected:text-[#2277FF]">
                        <span>{{ $nama }}</span>
                        <span data-count="{{ $key }}" class="rounded-lg bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-600 group-aria-selected:bg-[#2277FF] group-aria-selected:text-white">{{ count($data[$key]) }}</span>
                        <span class="absolute inset-x-0 bottom-0 hidden h-[3px] bg-[#2277FF] group-aria-selected:block"></span>
                    </button>
                @endforeach
            </div>

            <div class="mt-3 flex lg:mb-1 lg:mt-0">
                <a href="{{ route('admin.kegiatan.create') }}" class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-[#2277FF] px-4 text-sm font-medium text-white hover:bg-[#1A63D8] shadow-sm">
                    <span class="text-lg leading-none">+</span> Tambah Kegiatan
                </a>
            </div>
        </div>

        {{-- Filter & Pencarian (Sejajar dalam 1 Baris) --}}
        <div id="filterContainer" class="filter-grid-inline">
            <div class="flex h-10 items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 focus-within:border-[#2277FF]">
                <svg class="size-6 shrink-0 text-[#A8A8A8]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                <input id="q" type="text" placeholder="Cari nama kegiatan..." class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] outline-none placeholder:text-[#A8A8A8]">
            </div>

            <div class="relative">
                <select id="fSemester" class="{{ $selectCls }}">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
                {!! $chev !!}
            </div>

            <div class="relative">
                <select id="fKategori" class="{{ $selectCls }}">
                    <option value="">Semua Kategori</option>
                    @foreach (['Penelitian', 'Pengabdian', 'Pelatihan', 'Seminar'] as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
                {!! $chev !!}
            </div>

            <div id="wrapKelas" class="relative hidden">
                <select id="fKelas" class="{{ $selectCls }}">
                    <option value="">Semua Kelas</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
                {!! $chev !!}
            </div>
        </div>

        {{-- Tabel Data --}}
        <div id="tabel-wrap">
            <table id="tabel">
                <thead>
                    <tr>
                        <th>Nama Kegiatan</th>
                        <th>Kategori</th>
                        <th id="thPengaju">Pengaju</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</main>

{{-- Modal Konfirmasi Hapus --}}
<dialog id="modalHapus" aria-labelledby="modalHapusJudul" aria-describedby="modalHapusTeks">
    <div class="modal-kartu">
        <div class="modal-ikon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg>
        </div>
        <h2 id="modalHapusJudul" class="modal-judul">Hapus kegiatan?</h2>
        <p id="modalHapusTeks" class="modal-teks"><strong id="modalHapusNama"></strong> akan dihapus permanen.</p>
        <div class="modal-aksi">
            <button type="button" id="modalHapusBatal" class="modal-btn modal-btn-batal">Batal</button>
            <button type="button" id="modalHapusYa" class="modal-btn modal-btn-hapus">Hapus</button>
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script>
(function () {
    var RAW_DATA = @json($data);
    var tab = @json($tab);
    
    var URL_DETAIL = {
        dosen:     @json(route('admin.kegiatan.dosen.detail.show', ['id' => '__ID__'])),
        mahasiswa: @json(route('admin.kegiatan.mahasiswa.detail.show', ['id' => '__ID__'])),
        prodi:     null
    };
    var URL_DELETE = {
        dosen:     @json(route('admin.kegiatan.dosen.destroy', ['id' => '__ID__'])),
        mahasiswa: @json(route('admin.kegiatan.mahasiswa.destroy', ['id' => '__ID__'])),
        prodi:     null
    };
    var URL_LAPORAN = {
        dosen:     @json(route('admin.kegiatan.dosen.laporan')),
        mahasiswa: @json(route('admin.kegiatan.mahasiswa.laporan')),
        prodi:     null
    };
    
    var CSRF = @json(csrf_token());
    var PENGAJU = { dosen: 'Pengaju', mahasiswa: 'Pengaju', prodi: 'Penanggung Jawab' };
    
    var STATUS = {
        'Disetujui': 'border-[#D6EDB5] bg-[#E6F3D3] text-[#4C7D08]'
    };

    var $ = function (id) { return document.getElementById(id); };
    var filter = { semester: '', kategori: '', kelas: '' };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    
    function svg(paths, w, sw) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    
    var I_EYE   = svg('<circle cx="12" cy="12" r="2"/><path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>', 20, 1.5);
    var I_TRASH = svg('<path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" />', 20, 1.5);

    var TXT = DataTable.render.text();

    var BULAN = { jan: 1, feb: 2, mar: 3, apr: 4, mei: 5, may: 5, jun: 6, jul: 7, agu: 8, aug: 8, ags: 8, sep: 9, okt: 10, oct: 10, nov: 11, des: 12, dec: 12 };
    function tglKeAngka(r) {
        var m = String(r.tanggal || '').toLowerCase().match(/(\d{1,2})\s+([a-z]+)\.?\s+(\d{4})/);
        if (m && BULAN[m[2].slice(0, 3)]) return Date.UTC(+m[3], BULAN[m[2].slice(0, 3)] - 1, +m[1]);
        return 0;
    }

    var table = new DataTable('#tabel', {
        data: (RAW_DATA[tab] || []).filter(function(r) { return r.status === 'Disetujui'; }),
        autoWidth: false,
        pageLength: 10,
        order: [],
        columns: [
            { data: 'nama', render: TXT, className: 'truncate' },
            { data: 'kategori', render: TXT, className: 'truncate' },
            { data: 'pengaju', render: TXT, className: 'truncate' },
            { 
                data: 'tanggal', className: 'truncate',
                render: function (d, type, row) {
                    if (type === 'sort' || type === 'type') return tglKeAngka(row);
                    return esc(d);
                }
            },
            {
                data: 'status',
                render: function (d, type) {
                    if (type !== 'display') return d;
                    return '<span class="inline-flex rounded-lg border px-2 py-0.5 text-xs font-medium ' + (STATUS[d] || '') + '">' + esc(d) + '</span>';
                }
            },
            {
                data: null, orderable: false, searchable: false,
                render: function (d, type, r) {
                    var lihatBtn = URL_DETAIL[tab]
                        ? '<a href="' + URL_DETAIL[tab].replace('__ID__', encodeURIComponent(r.id)) + '" title="Lihat" class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6] cursor-pointer">' + I_EYE + '</a>'
                        : '<button type="button" title="Detail belum tersedia" disabled class="cursor-not-allowed rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] opacity-50">' + I_EYE + '</button>';
                    
                    var hapusBtn = URL_DELETE[tab]
                        ? '<button type="button" title="Hapus" data-act="delete" data-id="' + esc(r.id) + '" class="rounded-md bg-[#FFD9D9] p-1.5 text-[#E5484D] hover:bg-[#FFC9C9] cursor-pointer">' + I_TRASH + '</button>'
                        : '';

                    return '<div class="flex items-center gap-3">' + lihatBTnSafe(lihatBtn, hapusBtn) + '</div>';
                }
            }
        ],
        createdRow: function (tr, r) {
            var td = tr.children;
            td[0].title = r.nama || '';
            td[1].title = r.kategori || '';
            td[2].title = r.pengaju || '';
            td[3].title = r.tanggal || '';
        },
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada kegiatan yang disetujui.',
            zeroRecords: 'Tidak ada kegiatan yang disetujui.',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });

    function lihatBTnSafe(l, h) { return l + h; }

    function togglePager() {
        var p = document.querySelector('#tabel-wrap .dt-paging');
        if (p) p.style.display = table.page.info().pages > 1 ? '' : 'none';
    }
    table.on('draw.dt', togglePager);
    togglePager();

    DataTable.ext.search.push(function (settings, searchData, dataIndex, r) {
        if (settings.nTable.id !== 'tabel') return true;
        return r.status === 'Disetujui' &&
               (!filter.semester || r.semester === filter.semester) &&
               (!filter.kategori || r.kategori === filter.kategori) &&
               (tab !== 'mahasiswa' || !filter.kelas || r.kelas === filter.kelas);
    });

    function switchTab(targetTab) {
        tab = targetTab;
        document.getElementById('thPengaju').textContent = PENGAJU[tab];
        
        // Update Form Laporan Dinamis
        var formLaporan = document.getElementById('formLaporan');
        var laporanJudul = document.getElementById('laporanJudul');
        if (URL_LAPORAN[tab]) {
            formLaporan.action = URL_LAPORAN[tab];
            formLaporan.style.display = 'flex';
            laporanJudul.textContent = 'Laporan Data Kegiatan ' + tab.charAt(0).toUpperCase() + tab.slice(1);
        } else {
            formLaporan.style.display = 'none';
        }

        var filterContainer = document.getElementById('filterContainer');
        var wrapKelas = document.getElementById('wrapKelas');
        
        if (tab === 'mahasiswa') {
            wrapKelas.classList.remove('hidden');
            filterContainer.classList.add('has-kelas');
        } else {
            wrapKelas.classList.add('hidden');
            filterContainer.classList.remove('has-kelas');
            filter.kelas = '';
            document.getElementById('fKelas').value = '';
        }

        table.clear();
        var dataBaru = (RAW_DATA[tab] || []).filter(function(r) { return r.status === 'Disetujui'; });
        table.rows.add(dataBaru);
        table.draw();
        togglePager();

        try { history.replaceState(null, '', '?tab=' + tab); } catch (e) {}
    }

    document.querySelectorAll('[data-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('[data-tab]').forEach(function (b) { b.setAttribute('aria-selected', 'false'); });
            btn.setAttribute('aria-selected', 'true');
            switchTab(btn.dataset.tab);
        });
    });

    document.querySelector('[data-tab="' + tab + '"]').setAttribute('aria-selected', 'true');
    document.getElementById('thPengaju').textContent = PENGAJU[tab];
    if (tab === 'mahasiswa') {
        document.getElementById('wrapKelas').classList.remove('hidden');
        document.getElementById('filterContainer').classList.add('has-kelas');
    }
    if (URL_LAPORAN[tab]) {
        document.getElementById('formLaporan').action = URL_LAPORAN[tab];
        document.getElementById('laporanJudul').textContent = 'Laporan Data Kegiatan ' + tab.charAt(0).toUpperCase() + tab.slice(1);
    } else {
        document.getElementById('formLaporan').style.display = 'none';
    }

    $('q').addEventListener('input', function (e) {
        table.column(0).search(e.target.value.trim()).draw();
    });
    $('fSemester').addEventListener('change', function (e) { filter.semester = e.target.value; table.draw(); togglePager(); });
    $('fKategori').addEventListener('change', function (e) { filter.kategori = e.target.value; table.draw(); togglePager(); });
    $('fKelas').addEventListener('change', function (e) { filter.kelas = e.target.value; table.draw(); togglePager(); });

    var modal = $('modalHapus'), modalNama = $('modalHapusNama'), btnBatal = $('modalHapusBatal'), btnYa = $('modalHapusYa');
    var idHapus = null;

    function kirimHapus(id) {
        if (!URL_DELETE[tab]) return;
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = URL_DELETE[tab].replace('__ID__', encodeURIComponent(id));
        f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(f);
        f.submit();
    }

    $('tabel').addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act="delete"]'); if (!b) return;
        var rowData = table.row(b.closest('tr')).data();
        idHapus = b.dataset.id;
        if (typeof modal.showModal !== 'function') {
            if (confirm('Hapus kegiatan "' + (rowData ? rowData.nama : '') + '"?')) kirimHapus(idHapus);
            return;
        }
        modalNama.textContent = '"' + (rowData ? rowData.nama : '') + '"';
        modal.showModal();
    });

    btnBatal.addEventListener('click', function () { modal.close(); });
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.close(); });
    modal.addEventListener('close', function () { idHapus = null; btnYa.disabled = false; btnYa.textContent = 'Hapus'; });
    btnYa.addEventListener('click', function () {
        if (!idHapus) return;
        btnYa.disabled = true;
        btnYa.textContent = 'Menghapus...';
        kirimHapus(idHapus);
    });
})();
</script>
@endpush