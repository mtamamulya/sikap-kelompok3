@extends('layouts.pengajuan-app')
@section('title', 'Detail Berita')
@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-5 px-4 pb-10 pt-7 sm:px-6">
    <a href="{{ route('pengajuan.berita.index') }}" class="flex w-fit items-center gap-2 text-sm font-semibold text-[#2563EB]">
        <i class="ti ti-chevron-left"></i>Kembali ke daftar berita
    </a>
    @if (session('success'))
        <div class="rounded-lg bg-[#ECFDF5] px-4 py-3 text-sm text-[#047857]">{{ session('success') }}</div>
    @endif
    <article class="flex flex-col gap-5 rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_4px_12px_rgba(0,0,0,0.05)] sm:p-7">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <span class="rounded-full bg-[#DBEAFE] px-2.5 py-1 text-xs font-medium text-[#1D4ED8]">{{ $item['status'] }}</span>
                <h1 class="mt-3 text-2xl font-bold">{{ $item['title'] ?: 'Tanpa judul' }}</h1>
                <p class="mt-2 text-sm text-[#6B7280]">{{ $item['category'] ?: 'Kategori belum dipilih' }} · {{ \Carbon\Carbon::parse($item['submitted_at'])->locale('id')->translatedFormat('d F Y') }}</p>
            </div>
            @if (in_array($item['status'], ['Draft', 'Direvisi'], true))
                <a href="{{ route('pengajuan.berita.edit', $item['id']) }}" class="flex h-10 items-center gap-2 rounded-lg border border-[#E5E7EB] px-4 text-sm font-semibold text-[#2563EB]">
                    <i class="ti ti-pencil"></i>Edit berita
                </a>
            @endif
        </div>
        @if ($item['cover'])
            <img src="{{ asset('storage/'.$item['cover']) }}" alt="Foto sampul {{ $item['title'] }}" class="max-h-[420px] w-full rounded-xl object-cover">
        @endif
        <div class="grid gap-3 rounded-xl bg-[#F9FAFB] p-4 text-sm sm:grid-cols-2">
            <div><span class="text-[#6B7280]">Sumber kegiatan</span><p class="mt-1 font-medium">{{ $item['source_type'] === 'previous' ? 'Kegiatan sebelumnya' : 'Tanpa kegiatan sebelumnya' }}</p></div>
            <div><span class="text-[#6B7280]">Nama kegiatan</span><p class="mt-1 font-medium">{{ $item['previous_activity'] ?: ($item['activity_name'] ?: '—') }}</p></div>
        </div>
        <div class="whitespace-pre-line text-[15px] leading-7 text-[#374151]">{{ $item['content'] ?: 'Belum ada isi berita.' }}</div>
    </article>
</div>
@endsection
