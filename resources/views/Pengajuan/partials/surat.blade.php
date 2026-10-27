@php
    $d = $item['data'];
    $cat = $d['category'] ?? 'Lainnya';
    $tgl = \Carbon\Carbon::parse($item['submitted_at'])->locale('id');
    $nomor = sprintf('%03d/DST/PL4.7.1/HK.07.00/%s', $item['id'], $tgl->year);
    $fmt = fn ($x) => \Carbon\Carbon::parse($x)->locale('id')->translatedFormat('d F Y');
    $ay = $tgl->month >= 8 ? $tgl->year.'/'.($tgl->year + 1) : ($tgl->year - 1).'/'.$tgl->year;
    $title = $d['title'] ?? $item['title'];
    $purpose = match ($cat) {
        'Lomba' => 'mengikuti lomba '.$title,
        'Volunteer' => 'mengikuti program relawan '.$title,
        'Magang' => 'melaksanakan magang sebagai '.($d['role'] ?? '-').' di '.($d['organizer'] ?? '-'),
        default => 'mengikuti kegiatan '.$title,
    };
    $team = (($d['participation'] ?? '') === 'Tim') ? ($d['members'] ?? []) : [];
    $period = $fmt($d['start_date']).' s.d. '.$fmt($d['end_date']);
    $rows = array_filter([
        ['Kegiatan', $title],
        $cat === 'Magang' ? ['Posisi', $d['role'] ?? '-'] : null,
        [$cat === 'Magang' ? 'Perusahaan / Instansi' : 'Penyelenggara', $d['organizer'] ?? '-'],
        isset($d['level']) ? ['Tingkat', $d['level']] : (isset($d['scope']) ? ['Cakupan', $d['scope']] : null),
        $cat === 'Magang' && !empty($d['duration_months']) ? ['Durasi', $d['duration_months'].' bulan'] : null,
        ['Tempat', $d['location']],
        ['Waktu', $period],
    ]);
@endphp
<style>
    @page { size: A4; margin: 12mm; }
    @media print {
        body * { visibility: hidden !important; }
        #surat-print, #surat-print * { visibility: visible !important; }
        #surat-print { position: fixed; left: 0; top: 0; width: 100%; box-shadow: none !important; }
    }
</style>
<div id="surat-print" class="relative mx-auto w-full max-w-[794px] bg-white p-8 text-[13px] leading-[1.5] text-black shadow-[0_2px_12px_rgba(0,0,0,0.12)] sm:p-12" style="font-family: 'Times New Roman', Times, serif;">
    <div class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden">
        <span class="-rotate-[28deg] select-none text-[84px] font-bold tracking-widest text-black/[0.04]">PRATINJAU</span>
    </div>

    <div class="relative">
        <div class="flex items-center gap-4 border-b-2 border-black pb-3">
            <img src="{{ asset('images/logo-polines.png') }}" alt="" class="size-20 shrink-0 object-contain" onerror="this.style.visibility='hidden'">
            <div class="flex-1 text-center">
                <div class="text-[15px] uppercase tracking-wide">Kementerian Pendidikan Tinggi, Sains,<br>dan Teknologi</div>
                <div class="text-[16px] font-bold uppercase">Politeknik Negeri Semarang</div>
                <div class="text-[12px]">Jalan Prof. H. Soedarto, S.H., Tembalang, Semarang 50275</div>
                <div class="text-[12px]">Telepon (024) 7473417, 7499585, 7499586</div>
                <div class="text-[12px]">Laman www.polines.ac.id, Surel sekretariat@polines.ac.id</div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <div class="text-[18px] font-bold uppercase underline">Surat Pengajuan</div>
            <div class="text-[13px]">Nomor : {{ $nomor }}</div>
        </div>

        <p class="mt-6">Pimpinan Politeknik Negeri Semarang menerangkan bahwa :</p>
        <table class="mt-2 w-full">
            <tr><td class="w-[170px] align-top">Nama</td><td class="w-4 align-top">:</td><td class="font-bold">{{ $student['name'] }}</td></tr>
            <tr><td class="align-top">NIM</td><td class="align-top">:</td><td>{{ $student['nim'] }}</td></tr>
            <tr><td class="align-top">Jurusan / Prodi</td><td class="align-top">:</td><td>Teknik Elektro / S.Tr - {{ $student['prodi'] }}</td></tr>
        </table>

        @if (count($team))
            <p class="mt-3">Bersama anggota tim :</p>
            <ol class="ml-6 list-decimal">
                @foreach ($team as $m)
                    <li>{{ $m['name'] }} ({{ $m['role'] }})</li>
                @endforeach
            </ol>
        @endif

        <p class="mt-4 text-justify">Benar-benar sebagai mahasiswa Politeknik Negeri Semarang pada Tahun Akademik {{ $ay }}.</p>
        <p class="mt-2 text-justify">Surat Pengajuan ini dibuat sebagai syarat {{ $purpose }}, dengan keterangan sebagai berikut :</p>

        <table class="mt-2 w-full">
            @foreach ($rows as [$k, $v])
                <tr><td class="w-[170px] align-top">{{ $k }}</td><td class="w-4 align-top">:</td><td>{{ $v }}</td></tr>
            @endforeach
        </table>

        @if (!empty($d['description']))
            <p class="mt-3 text-justify">{{ $d['description'] }}</p>
        @endif

        <p class="mt-4">Demikian Surat Pengajuan ini dibuat dengan sebenarnya.</p>

        <div class="mt-6 flex justify-end">
            <div class="w-[300px]">
                <div>Semarang, {{ $tgl->translatedFormat('d F Y') }}</div>
                <div class="mt-1">POLITEKNIK NEGERI SEMARANG</div>
                <div>Ketua Program Studi</div>
                <div>Jurusan Teknik Elektro,</div>
                <div>Teknologi Rekaysa Komputer,</div>
                <div class="my-3 flex size-24 items-center justify-center border border-dashed border-black/50 text-[11px] text-black/50">QR / TTD</div>
                <div>Kuwat Santoso, S.T., M.Kom.</div>
                <div>NIP198407192019031008</div>
            </div>
        </div>

        <p class="mt-8 text-center text-[10px] text-black/60">Pratinjau surat. Dokumen belum bermakna resmi sebelum ditandatangani pejabat yang berwenang.</p>
    </div>
</div>