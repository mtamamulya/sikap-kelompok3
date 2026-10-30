@extends('layouts.pengajuan-app')
@section('title', 'Pengajuan Berita')
@section('content')
@php
    $statuses = [
        'Semua' => null,
        'Draft' => 'Draft',
        'Diajukan' => 'Diajukan',
        'Direvisi' => 'Direvisi',
        'Dipublikasikan' => 'Dipublikasikan',
        'Ditolak' => 'Ditolak',
    ];
    $badges = [
        'Draft' => 'bg-[#F3F4F6] text-[#4B5563]',
        'Diajukan' => 'bg-[#DBEAFE] text-[#1D4ED8]',
        'Direvisi' => 'bg-[#FFEDD5] text-[#B45309]',
        'Dipublikasikan' => 'bg-[#ECFDF5] text-[#047857]',
        'Ditolak' => 'bg-[#FEE2E2] text-[#B91C1C]',
    ];
@endphp
<div class="flex flex-col gap-6 px-4 pb-10 pt-7 sm:px-6 lg:px-12">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-[26px] font-bold leading-9">Pengajuan Berita</h1>
            <p class="mt-1 text-[13px] text-[#6B7280]">Kelola berita kegiatan dan informasi program studi.</p>
        </div>
        <a href="{{ route('pengajuan.berita.create') }}" class="flex h-10 items-center gap-2 rounded-lg bg-[#2563EB] px-4 text-sm font-semibold text-white hover:bg-[#1D4ED8]">
            <i class="ti ti-plus text-base"></i>Ajukan Berita
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-[#ECFDF5] px-4 py-3 text-sm text-[#047857]">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
        <div class="flex flex-wrap gap-5 overflow-x-auto border-b border-[#E5E7EB] px-5 pt-4">
            @foreach ($statuses as $label => $value)
                <a href="{{ route('pengajuan.berita.index', array_filter(['status' => $value, 'q' => $query])) }}"
                   class="flex shrink-0 items-center gap-2 border-b-2 px-1 pb-3 text-[13px] {{ $status === $value ? 'border-[#2563EB] font-semibold text-[#2563EB]' : 'border-transparent text-[#6B7280] hover:text-[#111827]' }}">
                    {{ $label }}
                    <span class="rounded-full bg-[#EFF6FF] px-2 py-0.5 text-xs text-[#2563EB]">{{ $value ? ($counts[$value] ?? 0) : $counts->sum() }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('pengajuan.berita.index') }}" class="p-5">
            @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <label class="flex h-11 items-center gap-2 rounded-lg border border-[#E5E7EB] px-3 text-sm text-[#6B7280] focus-within:border-[#2563EB]">
                <i class="ti ti-search text-lg"></i>
                <input name="q" value="{{ $query }}" placeholder="Cari judul atau kategori berita..." class="min-w-0 flex-1 outline-none">
            </label>
        </form>

        <div class="overflow-x-auto px-5 pb-5">
            <table class="w-full min-w-[720px] border-collapse text-left text-[13px]">
                <thead>
                    <tr class="bg-[#F3F4F6] text-[#374151]">
                        <th class="rounded-l-lg border-y border-[#E5E7EB] px-3 py-2.5 font-semibold">Judul Berita</th>
                        <th class="border-y border-[#E5E7EB] px-3 py-2.5 font-semibold">Kategori</th>
                        <th class="border-y border-[#E5E7EB] px-3 py-2.5 font-semibold">Tanggal Diajukan</th>
                        <th class="border-y border-[#E5E7EB] px-3 py-2.5 font-semibold">Status</th>
                        <th class="rounded-r-lg border-y border-[#E5E7EB] px-3 py-2.5 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="border-b border-[#E5E7EB] last:border-0">
                            <td class="max-w-[260px] truncate px-3 py-3 font-medium text-[#111827]">{{ $item['title'] ?: 'Tanpa judul' }}</td>
                            <td class="px-3 py-3 text-[#4B5563]">{{ $item['category'] ?: '—' }}</td>
                            <td class="px-3 py-3 text-[#4B5563]">{{ \Carbon\Carbon::parse($item['submitted_at'])->locale('id')->translatedFormat('d F Y') }}</td>
                            <td class="px-3 py-3"><span class="rounded-full px-2 py-1 text-xs font-medium {{ $badges[$item['status']] ?? 'bg-[#F3F4F6] text-[#4B5563]' }}">{{ $item['status'] }}</span></td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('pengajuan.berita.show', $item['id']) }}" aria-label="Lihat berita" class="flex size-8 items-center justify-center rounded-md bg-[#DBEAFE] text-[#2563EB] hover:bg-[#BFDBFE]"><i class="ti ti-eye text-base"></i></a>
                                    @if (in_array($item['status'], ['Draft', 'Direvisi'], true))
                                        <a href="{{ route('pengajuan.berita.edit', $item['id']) }}" aria-label="Edit berita" class="flex size-8 items-center justify-center rounded-md bg-[#FFEDD5] text-[#C2410C] hover:bg-[#FED7AA]"><i class="ti ti-pencil text-base"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-12 text-center text-sm text-[#6B7280]">Belum ada pengajuan berita.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
