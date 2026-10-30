@extends('layouts.pengajuan-app')
@section('title', 'Pengajuan')
@section('content')
@php
    $styles = [
        'Diajukan' => ['badge' => 'bg-[#DBEAFE] text-[#1D4ED8]', 'note' => 'Pengajuan telah dikirim dan menunggu ditinjau.'],
        'Disetujui' => ['badge' => 'bg-[#ECFDF5] text-[#047857]', 'note' => 'Pengajuan telah disetujui oleh tim administrasi.'],
        'Direview' => ['badge' => 'bg-[#FFE1A0] text-[#B96300]', 'note' => 'Pengajuan sedang direview oleh tim administrasi.'],
        'Selesai' => ['badge' => 'bg-[#F3F4F6] text-[#6B7280]', 'note' => 'Pengajuan telah selesai diproses.'],
        'Ditolak' => ['badge' => 'bg-[#FEE2E2] text-[#B91C1C]', 'note' => 'Pengajuan ditolak oleh reviewer akademik.'],
        'Perlu revisi' => ['badge' => 'bg-[#FFEDD5] text-[#B45309]', 'note' => 'Pengajuan perlu diperbaiki sebelum diproses.'],
    ];
    $cards = [
        ['TOTAL PENGAJUAN', $all->count(), 'bg-[#2277FF]'],
        ['DISETUJUI', $counts['Disetujui'] ?? 0, 'bg-[#8BC34A]'],
        ['DIREVIEW', ($counts['Direview'] ?? 0) + ($counts['Diajukan'] ?? 0), 'bg-[#FF9B29]'],
        ['SELESAI', $counts['Selesai'] ?? 0, 'bg-[#9CA3AF]'],
    ];
    $guide = [
        'Dokumen pendukung bersifat opsional.',
        'Lampirkan tautan yang dapat diakses oleh tim peninjau.',
        'Gunakan nama dokumen dan jenis yang sesuai.',
    ];
    $fmt = fn ($d) => \Carbon\Carbon::parse($d)->locale('id')->translatedFormat('d M Y');
    $rangeLabel = $from && $to ? $fmt($from).' - '.$fmt($to) : ($from ? 'Mulai '.$fmt($from) : ($to ? 'Sampai '.$fmt($to) : 'Semua tanggal'));
    $cur = $rows->currentPage();
    $last = $rows->lastPage();
    $pages = collect(range(1, $last))->filter(fn ($p) => $p === 1 || $p === $last || abs($p - $cur) <= 1);
    $btn = 'flex h-9 items-center justify-center gap-1.5 rounded-lg border border-[#E5E7EB] px-3 text-[13px]';
    $dateInput = 'h-10 w-full rounded-lg border border-[#E5E7EB] bg-white px-3 text-[13px] outline-none focus:border-[#2277FF]';
@endphp
<div class="flex flex-col gap-6 px-3 py-5 sm:px-5 sm:py-6 lg:px-8 lg:py-7">
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
        @foreach ($cards as [$label, $value, $accent])
            <div
                x-data="{
                    rotateX: 0,
                    rotateY: 0,
                    hovered: false,
                    tilt(event) {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                        const bounds = this.$el.getBoundingClientRect();
                        this.rotateX = ((event.clientY - bounds.top) / bounds.height - 0.5) * -10;
                        this.rotateY = ((event.clientX - bounds.left) / bounds.width - 0.5) * 10;
                    }
                }"
                @mousemove="tilt($event)"
                @mouseenter="hovered = true"
                @mouseleave="hovered = false; rotateX = 0; rotateY = 0"
                :style="`transform: perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(${hovered ? '-4px' : '0'})`"
                class="relative flex flex-col gap-0 overflow-hidden rounded-xl border border-[#E5E7EB] bg-white p-2 pl-4 shadow-[0_4px_12px_rgba(0,0,0,0.05)] transition-[transform,box-shadow] duration-200 ease-out hover:shadow-[0_14px_28px_rgba(0,0,0,0.14)] motion-reduce:transition-none lg:min-h-[108px] lg:justify-center lg:gap-1 lg:p-5 lg:pl-6"
            >
                <span aria-hidden="true" class="absolute inset-y-0 left-0 w-2 {{ $accent }}"></span>
                <div class="text-xs font-semibold text-[#4B5563] lg:text-base">{{ $label }}</div>
                <div class="text-[28px] font-bold leading-8 text-[#111827] lg:text-4xl lg:leading-10">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="flex flex-col gap-3 rounded-xl border border-[#E5E7EB] bg-white p-3 shadow-[0_4px_12px_rgba(0,0,0,0.05)] lg:p-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold lg:text-xl">Daftar pengajuan</h2>
            <a href="{{ route('pengajuan.create') }}" class="rounded-lg bg-[#EFF6FF] px-3.5 py-2 text-sm font-semibold text-[#2563EB] lg:px-4 lg:py-2.5">Tambah pengajuan</a>
        </div>

        @if (session('success'))
            <div role="status" class="rounded-lg bg-[#ECFDF5] px-4 py-3 text-sm text-[#047857]">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('pengajuan.index') }}" class="flex flex-wrap items-end gap-4 xl:flex-nowrap">
            <label class="flex w-full min-w-0 flex-col gap-2 text-[13px] font-medium text-[#4B5563] md:min-w-[260px] md:flex-1">
                Cari pengajuan
                <div class="flex min-h-[42px] items-center gap-2 rounded-lg border border-[#E5E7EB] px-3">
                    <input name="q" value="{{ $q }}" data-live-search placeholder="Cari jenis atau judul pengajuan" class="min-w-0 flex-1 bg-transparent text-sm font-normal outline-none placeholder:text-[#9CA3AF]">
                    <i aria-hidden="true" class="ti ti-search text-base text-[#9CA3AF]"></i>
                </div>
            </label>

            {{-- STATUS (dropdown kustom) --}}
            <div x-data="{
                     open: false,
                     value: @js($status),
                     options: @js(array_merge([''], array_keys($styles))),
                     pick(v) {
                         this.value = v;
                         this.open = false;
                         this.$nextTick(() => this.$refs.status.form.submit());
                     }
                 }"
                 @click.outside="open = false" @keydown.escape="open = false"
                 class="relative flex w-full flex-col gap-2 text-[13px] font-medium text-[#4B5563] sm:w-[200px] sm:shrink-0">
                Status
                <input type="hidden" name="status" x-ref="status" :value="value">
                <button type="button" @click="open = !open"
                        :class="open ? 'border-[#2277FF]' : 'border-[#E5E7EB]'"
                        class="flex min-h-[42px] w-full items-center gap-2 rounded-lg border bg-white px-3 text-left transition">
                    <span class="min-w-0 flex-1 truncate text-sm font-normal" :class="value ? 'text-[#111827]' : 'text-[#4B5563]'" x-text="value || 'Semua status'"></span>
                    <i class="ti ti-chevron-down text-base text-[#9CA3AF] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute left-0 top-full z-30 mt-2 flex w-full min-w-[200px] flex-col gap-0.5 rounded-lg border border-[#E5E7EB] bg-white p-1.5 shadow-[0_4px_12px_rgba(17,24,39,0.08)]">
                    <template x-for="o in options" :key="o">
                        <button type="button" @click="pick(o)"
                                :class="value === o ? 'bg-[#EFF6FF] text-[#2277FF]' : 'text-[#111827] hover:bg-[#F9FAFB]'"
                                class="h-9 rounded-lg px-3 text-left text-sm font-normal transition" x-text="o || 'Semua status'"></button>
                    </template>
                </div>
            </div>

            {{-- RENTANG TANGGAL --}}
            <div x-data="{ open: false }" @click.outside="open = false" class="relative flex w-full flex-col gap-2 text-[13px] font-medium text-[#4B5563] sm:w-[260px] sm:shrink-0">
                Rentang tanggal
                <button type="button" @click="open = !open" class="flex min-h-[42px] w-full items-center gap-2 rounded-lg border border-[#E5E7EB] px-3 text-left">
                    <span class="min-w-0 flex-1 truncate text-sm font-normal {{ $from || $to ? 'text-[#111827]' : 'text-[#9CA3AF]' }}">{{ $rangeLabel }}</span>
                    <i class="ti ti-calendar text-base text-[#9CA3AF]"></i>
                </button>
                <div x-show="open" x-cloak class="absolute left-0 top-full z-30 mt-2 flex w-full min-w-[260px] flex-col gap-3 rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-[0_10px_25px_rgba(0,0,0,0.08)] sm:w-[320px]">
                    <label class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">Dari
                        <input type="date" name="from" x-ref="from" value="{{ $from }}" class="{{ $dateInput }}">
                    </label>
                    <label class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">Sampai
                        <input type="date" name="to" x-ref="to" value="{{ $to }}" class="{{ $dateInput }}">
                    </label>
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="$refs.from.value = ''; $refs.to.value = ''" class="h-9 rounded-lg border border-[#E5E7EB] px-3 text-[13px] font-semibold text-[#374151]">Hapus</button>
                        <button type="submit" class="h-9 rounded-lg bg-[#2563EB] px-4 text-[13px] font-semibold text-white">Terapkan</button>
                    </div>
                </div>
            </div>

            <a href="{{ route('pengajuan.index') }}" class="flex h-[42px] w-full items-center justify-center rounded-lg border border-[#E5E7EB] px-4 text-sm font-semibold text-[#2563EB] sm:w-auto sm:shrink-0">Reset</a>
        </form>

        <div class="flex flex-col gap-5">
            <div class="flex flex-col gap-3 lg:hidden">
                @forelse ($rows as $r)
                    @php $st = $styles[$r['status']] ?? $styles['Direview']; @endphp
                    <article class="rounded-xl border border-[#E5E7EB] bg-white p-3">
                        <div class="flex items-start gap-3">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-xs fo                            .\vendor\bin\pint --testnt-bold text-[#2563EB]">{{ $rows->firstItem() + $loop->index }}</span>
                            <div class="min-w-0 flex-1">
                                <h3 class="break-words text-[13px] font-semibold">{{ $r['title'] }}</h3>
                                <p class="mt-0.5 text-xs text-[#6B7280]">{{ $r['category'] }} <span aria-hidden="true">·</span> {{ $r['submitted_at']->copy()->locale('id')->translatedFormat('d M Y') }}</p>
                                <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $st['badge'] }}">{{ $r['status'] }}</span>
                                <p class="mt-1 text-xs leading-5 text-[#6B7280]">{{ $st['note'] }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-end gap-3 border-t border-[#E5E7EB] pt-2">
                            @if ($r['editable'])
                                <a href="{{ route('pengajuan.edit', $r['id']) }}" class="flex h-8 items-center gap-1.5 rounded-lg bg-[#EFF6FF] px-2.5 text-xs font-semibold text-[#2563EB]">
                                    <i class="ti ti-pencil"></i>Edit
                                </a>
                            @endif
                            <a href="{{ route('pengajuan.show', $r['id']) }}" class="flex h-8 items-center gap-1.5 rounded-lg px-2.5 text-xs font-semibold text-[#2563EB]">
                                Lihat detail<i class="ti ti-chevron-right"></i>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-[#E5E7EB] bg-white p-8 text-center text-sm text-[#6B7280]">Tidak ada pengajuan.</div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto rounded-xl border border-[#E5E7EB] lg:block">
                <div class="min-w-[860px]">
                    <div class="grid grid-cols-[1fr_115px_235px_156px] gap-4 bg-[#F9FAFB] px-4 py-1 text-sm font-semibold text-[#6B7280]">
                        <div>Nama pengajuan / jenis dokumen</div>
                        <div>TANGGAL</div>
                        <div>STATUS PENGAJUAN</div>
                        <div></div>
                    </div>
                    @forelse ($rows as $r)
                        @php $st = $styles[$r['status']] ?? $styles['Direview']; @endphp
                        <div class="grid min-h-16 grid-cols-[1fr_105px_215px_140px] items-center gap-3 border-b border-[#E5E7EB] px-3 py-1">
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#EFF6FF] text-xs font-bold text-[#2563EB]">{{ $rows->firstItem() + $loop->index }}</div>
                                <div class="flex flex-col gap-[5px]">
                                    <div class="text-[13px] font-semibold">{{ $r['title'] }}</div>
                                    <div class="text-xs text-[#6B7280]">{{ $r['category'] }}</div>
                                </div>
                            </div>
                            <div class="text-xs text-[#6B7280]">{{ $r['submitted_at']->copy()->locale('id')->translatedFormat('d M Y') }}</div>
                            <div class="flex flex-col items-start gap-[5px]">
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $st['badge'] }}">{{ $r['status'] }}</span>
                                <span class="text-xs text-[#6B7280]">{{ $st['note'] }}</span>
                            </div>
                            <div class="flex items-center justify-end gap-3">
                                @if ($r['editable'])
                                    <a href="{{ route('pengajuan.edit', $r['id']) }}" title="Edit pengajuan" aria-label="Edit pengajuan"
                                       class="flex size-7 items-center justify-center rounded-lg bg-[#EFF6FF] text-[#2563EB] transition hover:bg-[#DBEAFE]">
                                        <i class="ti ti-pencil text-base"></i>
                                    </a>
                                @else
                                    <span title="Pengajuan dengan status {{ $r['status'] }} tidak dapat diedit" aria-disabled="true"
                                          class="flex size-7 cursor-not-allowed items-center justify-center rounded-lg bg-[#F3F4F6] text-[#9CA3AF]">
                                        <i class="ti ti-pencil text-base"></i>
                                    </span>
                                @endif
                                <a href="{{ route('pengajuan.show', $r['id']) }}" class="text-xs font-semibold text-[#2563EB]">Lihat detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-[#6B7280]">Tidak ada pengajuan.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-[#E5E7EB] bg-white p-3">
                <div class="mb-3 text-sm font-bold">Panduan Pengajuan</div>
                <div class="flex flex-col gap-3">
                    @foreach ($guide as $g)
                        <div class="flex items-start gap-2 text-[13px] text-[#4B5563]">
                            <span class="mt-px flex size-4 shrink-0 items-center justify-center rounded border-[1.5px] border-[#2B3F6C]">
                                <i class="ti ti-check text-[10px] text-[#2B3F6C]"></i>
                            </span>
                            {{ $g }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-[13px] text-[#6B7280]">{{ $rows->count() }} pengajuan ditampilkan • Total {{ $all->count() }} pengajuan</div>
            <nav class="flex flex-wrap items-center gap-2" aria-label="Halaman">
                @if ($rows->onFirstPage())
                    <span class="{{ $btn }} text-[#9CA3AF]"><i class="ti ti-chevron-left"></i><span class="hidden sm:inline">Sebelumnya</span></span>
                @else
                    <a href="{{ $rows->previousPageUrl() }}" class="{{ $btn }} text-[#4B5563]"><i class="ti ti-chevron-left"></i><span class="hidden sm:inline">Sebelumnya</span></a>
                @endif

                @php $prev = 0; @endphp
                @foreach ($pages as $p)
                    @if ($p - $prev > 1)<span class="px-1 text-[#9CA3AF]">…</span>@endif
                    <a href="{{ $rows->url($p) }}" class="flex size-9 items-center justify-center rounded-lg text-[13px] font-semibold {{ $p === $cur ? 'bg-[#2563EB] text-white' : 'border border-[#E5E7EB] text-[#4B5563]' }}">{{ $p }}</a>
                    @php $prev = $p; @endphp
                @endforeach

                @if ($rows->hasMorePages())
                    <a href="{{ $rows->nextPageUrl() }}" class="{{ $btn }} text-[#4B5563]"><span class="hidden sm:inline">Berikutnya</span><i class="ti ti-chevron-right"></i></a>
                @else
                    <span class="{{ $btn }} text-[#9CA3AF]"><span class="hidden sm:inline">Berikutnya</span><i class="ti ti-chevron-right"></i></span>
                @endif
            </nav>
        </div>
    </div>
</div>
@endsection