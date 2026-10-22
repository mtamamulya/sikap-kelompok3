@extends('layouts.app')
@section('title', 'Detail pengajuan')
@section('content')
@php
    $status = $row['status'];
    $badge = [
        'Disetujui' => 'bg-[#ECFDF5] text-[#047857]',
        'Direview' => 'bg-[#FFE1A0] text-[#B96300]',
        'Selesai' => 'bg-[#E6F3D3] text-[#3F6212]',
        'Ditolak' => 'bg-[#FEE2E2] text-[#B91C1C]',
        'Perlu revisi' => 'bg-[#FFEDD5] text-[#B45309]',
    ];
    $withNote = in_array($status, ['Ditolak', 'Perlu revisi']);
    $submitted = $row['submitted_at']->copy()->locale('id')->translatedFormat('d M Y');
    $footnote = [
        'Ditolak' => 'Ditolak adalah status akhir. Pengajuan ini tidak dilanjutkan ke Diterima atau Selesai.',
        'Perlu revisi' => 'Setelah diperbaiki, pengajuan dapat diajukan ulang dan direview kembali.',
    ][$status] ?? null;
    $resultNote = [
        'Selesai' => 'Pengajuan telah selesai diproses.',
        'Ditolak' => 'Dokumen hasil tidak diterbitkan karena pengajuan ditolak.',
        'Perlu revisi' => 'Dokumen hasil belum tersedia karena pengajuan perlu diperbaiki.',
    ][$status] ?? 'Dokumen hasil akan tersedia setelah pengajuan selesai diproses.';
    $card = 'rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_4px_12px_rgba(0,0,0,0.05)] sm:p-7';
@endphp
<div class="flex flex-col gap-6 px-4 pb-10 pt-7 sm:px-6 lg:px-12">
    <div class="flex flex-col gap-[18px]">
        <a href="{{ route('pengajuan.index') }}" class="flex w-fit items-center gap-2 text-sm font-semibold text-[#2563EB]">
            <span class="flex size-8 items-center justify-center rounded-full bg-[#EFF6FF]"><i class="ti ti-chevron-left text-base"></i></span>
            Kembali ke pengajuan
        </a>
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-4">
                <h1 class="text-[26px] font-bold leading-9">Detail pengajuan</h1>
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge[$status] }} {{ $withNote ? 'ml-auto' : '' }}">{{ $status }}</span>
            </div>
            <p class="text-[13px] leading-5 text-[#6B7280]">Informasi dokumen dan perkembangan proses pengajuan Anda.</p>
        </div>
    </div>

    @if ($withNote)
        <div class="flex gap-3 rounded-xl bg-[#EFF6FF] p-4 text-[#003385]">
            <i class="ti ti-info-circle text-xl"></i>
            <div class="flex flex-col gap-1">
                <div class="text-[15px] font-semibold">Contoh tampilan</div>
                <div class="text-[13px]">Pengajuan dan catatan pada halaman ini adalah contoh untuk memperlihatkan status proses.</div>
            </div>
        </div>
    @endif

    <div class="{{ $card }} flex flex-col gap-4">
        <div class="flex flex-col gap-1.5">
            <h2 class="text-xl font-bold">{{ $row['title'] }}</h2>
            <p class="text-sm text-[#6B7280]">Diajukan {{ $submitted }} • {{ $row['type'] }}</p>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-[#F9FAFB] p-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-sm font-bold text-[#2563EB]">{{ strtoupper(substr($student['name'], 0, 1)) }}</div>
            <div class="flex-1">
                <div class="text-sm font-semibold">{{ $student['name'] }}</div>
                <div class="text-[13px] text-[#6B7280]">NIM {{ $student['nim'] }} • {{ $student['prodi'] }}</div>
            </div>
            <span class="rounded-full border border-[#BFDBFE] bg-[#DBEAFE] px-2.5 py-1 text-xs font-medium text-[#1D4ED8]">Mahasiswa</span>
        </div>
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-[3fr_2fr]">
        <div class="flex flex-col gap-6">
            <div class="{{ $card }} flex flex-col gap-5">
                <h2 class="text-xl font-bold">Riwayat proses</h2>
                <div class="flex flex-col">
                    @foreach ($steps as $s)
                        @php $last = $loop->last; @endphp
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 border-[#2563EB]">
                                    <span class="size-2.5 rounded-full bg-[#2563EB]"></span>
                                </span>
                                @unless ($last)<span class="w-0.5 flex-1 bg-[#BFDBFE]"></span>@endunless
                            </div>
                            <div class="flex flex-1 flex-col gap-1 {{ $last ? '' : 'pb-6' }}">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[15px] font-semibold">{{ $s['title'] }}</span>
                                    @if ($last)<span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $badge[$status] }}">Status terkini</span>@endif
                                </div>
                                <div class="text-[13px] text-[#6B7280]">{{ $s['at']->copy()->locale('id')->translatedFormat('d M Y, H.i') }} WIB • {{ $s['actor'] }}</div>
                                <div class="text-[13px] text-[#4B5563]">{{ $s['note'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($footnote)<p class="text-[13px] text-[#6B7280]">{{ $footnote }}</p>@endif
            </div>

            @if ($status === 'Ditolak')
                <div class="{{ $card }} flex flex-col gap-4">
                    <h2 class="text-xl font-bold">Alasan penolakan</h2>
                    <div class="flex gap-3 rounded-xl bg-[#FEF2F2] p-4 text-[#B91C1C]">
                        <i class="ti ti-circle-x text-xl"></i>
                        <div class="flex flex-col gap-1">
                            <div class="text-[15px] font-semibold">Pengajuan tidak dapat dilanjutkan</div>
                            <div class="text-sm">Pengajuan dikirim di luar periode penerimaan dukungan lomba. Silakan ajukan kembali pada periode berikutnya.</div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($status === 'Perlu revisi')
                <div class="{{ $card }} flex flex-col gap-4">
                    <h2 class="text-xl font-bold">Catatan reviewer</h2>
                    <div class="flex gap-3 rounded-xl bg-[#FEF9E7] p-4 text-[#92400E]">
                        <i class="ti ti-info-circle text-xl"></i>
                        <div class="flex flex-col gap-1">
                            <div class="text-[15px] font-semibold">Perbaikan diperlukan</div>
                            <div class="text-sm">Lengkapi rincian anggaran per komponen dan unggah lembar pengesahan yang telah ditandatangani.</div>
                        </div>
                    </div>
                    <a href="{{ route('pengajuan.create') }}" class="flex h-11 w-fit items-center gap-2 rounded-lg bg-[#2563EB] px-4 text-sm font-semibold text-white">
                        <i class="ti ti-pencil text-base"></i>Perbaiki pengajuan
                    </a>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-6">
            <div class="{{ $card }} flex flex-col gap-4">
                <h2 class="text-xl font-bold">Informasi pengajuan</h2>
                <div>
                    <div class="text-[13px] text-[#6B7280]">Jenis dokumen</div>
                    <div class="text-[15px] font-semibold">{{ $row['type'] }}</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[13px] text-[#6B7280]">Tanggal pengajuan</div>
                        <div class="text-[15px] font-semibold">{{ $submitted }}</div>
                    </div>
                    <div>
                        <div class="text-[13px] text-[#6B7280]">Pengaju / NIM</div>
                        <div class="text-[15px] font-semibold">{{ $student['name'] }} / {{ $student['nim'] }}</div>
                    </div>
                </div>
            </div>

            <div class="{{ $card }} flex flex-col gap-4">
                <h2 class="text-xl font-bold">Dokumen lampiran</h2>
                @foreach ($files as $f)
                    <div class="flex items-center gap-3 rounded-xl bg-[#F9FAFB] p-4">
                        <i class="ti ti-file-text text-xl text-[#2563EB]"></i>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold">{{ $f['name'] }}</div>
                            <div class="text-[13px] text-[#6B7280]">{{ $f['meta'] }}</div>
                        </div>
                        <i class="ti ti-paperclip text-lg text-[#6B7280]"></i>
                    </div>
                @endforeach
            </div>

            <div class="{{ $card }} flex flex-col gap-4">
                <h2 class="text-xl font-bold">Dokumen hasil</h2>
                @if ($status === 'Selesai')
                    <div class="flex items-center gap-3 rounded-xl bg-[#F9FAFB] p-4">
                        <i class="ti ti-file-text text-xl text-[#2563EB]"></i>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold">Dokumen_hasil_pengajuan.pdf</div>
                            <div class="text-[13px] text-[#6B7280]">PDF</div>
                        </div>
                        <i class="ti ti-paperclip text-lg text-[#6B7280]"></i>
                    </div>
                    <a href="#" class="flex h-11 w-fit items-center gap-2 rounded-lg bg-[#2563EB] px-4 text-sm font-semibold text-white">
                        <i class="ti ti-download text-base"></i>Unduh dokumen pengajuan
                    </a>
                @else
                    <button type="button" disabled class="flex h-11 w-fit cursor-not-allowed items-center gap-2 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] px-4 text-sm font-semibold text-[#6B7280]">
                        <i class="ti ti-download text-base"></i>Unduh dokumen pengajuan
                    </button>
                @endif
                <p class="text-[13px] text-[#6B7280]">{{ $resultNote }}</p>
            </div>
        </div>
    </div>
</div>
@endsection