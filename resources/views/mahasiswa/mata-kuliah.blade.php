@extends('layouts.mahasiswa')

@section('title', 'Mata Kuliah')

@section('content')
@php
    // Tab "riwayat" yang lama sudah dihapus. Tab "semua" sekarang tampil sebagai "Riwayat mata kuliah".
    $subjudul = [
        'semua'    => 'Telusuri catatan mata kuliah Anda dari semester sebelumnya.',
        'berjalan' => 'Pantau mata kuliah dan beban studi Anda pada semester aktif.',
    ][$tab];

    $judulKartu = [
        'semua'    => 'Riwayat mata kuliah',
        'berjalan' => 'Paket mata kuliah semester berjalan',
    ][$tab];

    $ketKartu = [
        'semua'    => 'Temukan mata kuliah berdasarkan tingkat, periode, atau nama dosen.',
        'berjalan' => 'Daftar mata kuliah yang ditetapkan untuk semester aktif Anda.',
    ][$tab];

    // Urutan tombol tab: Semester berjalan di depan, Riwayat mata kuliah di belakang.
    $tabs = [
        ['berjalan', 'Semester berjalan',   'mahasiswa.mata-kuliah.berjalan'],
        ['semua',    'Riwayat mata kuliah', 'mahasiswa.mata-kuliah'],
    ];

    $n        = $rows->count();
    $cardCls  = 'rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)] sm:p-6';
    $badgeCls = 'shrink-0 rounded-full border border-[#B8D0FA] bg-[#E8F0FE] px-3 py-1 text-xs font-medium text-[#0162DD]';
    $selCls   = 'h-10 w-full appearance-none rounded-lg border border-[#E5E7EB] bg-white pl-3.5 pr-9 text-sm text-[#0A0A0A] outline-none transition-colors hover:border-[#C7D2E5] focus:border-[#27F]';
    $infoIcon = '<svg class="mt-0.5 size-5 shrink-0 text-[#003385]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0-18 0"/><path d="M12 9h.01"/><path d="M11 12h1v4h1"/></svg>';
@endphp

<div class="flex flex-col gap-6">
    {{-- Judul halaman --}}
    <div class="flex flex-col gap-1.5">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Mata Kuliah</h1>
        <p class="text-base text-[#6B7280]">{{ $subjudul }}</p>
    </div>

    {{-- Tab: segmented control --}}
    <nav aria-label="Tampilan mata kuliah" class="max-w-full overflow-x-auto">
        <div class="inline-flex gap-1 rounded-xl border border-[#E5E7EB] bg-white p-1">
            @foreach ($tabs as [$key, $label, $rute])
                <a href="{{ Route::has($rute) ? route($rute) : '#' }}" @if ($tab === $key) aria-current="page" @endif
                   class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition-colors {{ $tab === $key ? 'bg-[#27F] text-white shadow-sm' : 'text-[#0162DD] hover:bg-[#F2F6FD]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Ringkasan paket (hanya tab Semester berjalan) --}}
    @if ($tab === 'berjalan')
        <section class="{{ $cardCls }} flex flex-col gap-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-semibold">{{ $ringkasan['tahun_akademik'] }} • Semester {{ $ringkasan['semester_ke'] }}</h2>
                    <p class="text-sm text-[#6B7280]">{{ $ringkasan['jumlah_berjalan'] }} mata kuliah dipaketkan untuk semester aktif Anda.</p>
                </div>
                <span class="{{ $badgeCls }}">Paket semester</span>
            </div>

            <div class="flex gap-3 rounded-xl bg-[#EEF4FF] px-4 py-3.5 text-[#003385]">
                {!! $infoIcon !!}
                <div class="flex flex-col gap-0.5">
                    <div class="text-sm font-semibold">Paket semester berjalan sudah ditetapkan</div>
                    <p class="text-sm leading-5">Daftar berikut bersumber dari penetapan akademik, bukan pilihan mandiri atau daftar referensi periode.</p>
                </div>
            </div>

            <dl class="grid grid-cols-1 divide-y divide-[#E5E7EB] overflow-hidden rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                <div class="px-5 py-4">
                    <dt class="text-xs text-[#6B7280]">Jumlah mata kuliah</dt>
                    <dd class="mt-1 text-lg font-semibold">{{ $ringkasan['jumlah_berjalan'] }}</dd>
                </div>
                <div class="px-5 py-4">
                    <dt class="text-xs text-[#6B7280]">Total beban studi</dt>
                    <dd class="mt-1 text-lg font-semibold">{{ $ringkasan['sks_diambil'] }} SKS</dd>
                </div>
                <div class="px-5 py-4">
                    <dt class="text-xs text-[#6B7280]">Status paket</dt>
                    <dd class="mt-1 text-lg font-semibold text-[#4C7D08]">Sudah ditetapkan</dd>
                </div>
            </dl>
        </section>
    @endif

    {{-- Kartu daftar mata kuliah --}}
    <section id="daftar-mata-kuliah" class="{{ $cardCls }} flex scroll-mt-24 flex-col gap-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex flex-col gap-1">
                <h2 class="text-xl font-semibold">{{ $judulKartu }}</h2>
                <p class="text-sm text-[#6B7280]">{{ $ketKartu }}</p>
            </div>
            <span class="{{ $badgeCls }}">{{ $n }} {{ $tab === 'berjalan' ? 'mata kuliah' : 'catatan' }}</span>
        </div>

        @if ($tab !== 'berjalan')
            {{-- Panel filter: semua kontrol setinggi h-10, kolom dikunci --}}
            <form method="GET" action="{{ url()->current() }}#daftar-mata-kuliah"
                  class="grid grid-cols-1 gap-3 rounded-xl border border-[#EEF0F3] bg-[#F9FAFB] p-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_180px_180px_180px_88px]">
                <div class="flex h-10 items-center gap-2 rounded-lg border border-[#E5E7EB] bg-white px-3.5 transition-colors focus-within:border-[#27F] sm:col-span-2 xl:col-span-1">
                    <svg class="size-4 shrink-0 text-[#6B7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0-14 0"/><path d="M21 21l-6-6"/></svg>
                    <input type="search" name="q" value="{{ $filter['q'] }}" placeholder="Cari mata kuliah..."
                           class="min-w-0 flex-1 bg-transparent text-sm outline-none placeholder:text-[#A8A8A8]">
                </div>

                @foreach ([
                    ['semester', 'Semua Semester', ['Ganjil' => 'Ganjil', 'Genap' => 'Genap']],
                    ['tingkat',  'Semua Tingkat',  [1 => 'Tingkat 1', 2 => 'Tingkat 2', 3 => 'Tingkat 3']],
                    ['tahun',    'Tahun Akademik', array_combine($tahunList, $tahunList)],
                ] as [$field, $placeholder, $options])
                    <div class="relative">
                        <select name="{{ $field }}" class="{{ $selCls }}" onchange="this.form.submit()" aria-label="{{ $placeholder }}">
                            <option value="">{{ $placeholder }}</option>
                            @foreach ($options as $val => $text)
                                <option value="{{ $val }}" @selected((string) $filter[$field] === (string) $val)>{{ $text }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-[#6B7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6"/></svg>
                    </div>
                @endforeach

                <a href="{{ url()->current() }}#daftar-mata-kuliah"
                   class="inline-flex h-10 w-full items-center justify-center rounded-lg border border-[#E5E7EB] bg-white text-sm font-semibold text-[#0062FF] transition hover:bg-[#EFF6FF] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#27F]">
                    Reset
                </a>
                <button type="submit" class="sr-only">Terapkan</button>
            </form>

            <div class="flex gap-3 rounded-xl bg-[#EEF4FF] px-4 py-3.5 text-[#003385]">
                {!! $infoIcon !!}
                <div class="flex flex-col gap-0.5">
                    <div class="text-sm font-semibold">Informasi mata kuliah belum lengkap</div>
                    <p class="text-sm leading-5">
                        {{ $n }} catatan sumber ditampilkan apa adanya, termasuk nama mata kuliah yang berulang. Tingkat dan periode Ganjil/Genap bukan nomor semester. Nilai, SKS, dan status tiap mata kuliah belum tersedia.
                    </p>
                </div>
            </div>
        @endif

        {{-- Tabel: gaya sama dengan dashboard mahasiswa. Lebar kolom dikunci, tinggi minimum setara 10 baris --}}
        <div class="{{ $tab === 'berjalan' ? '' : 'min-h-[452px]' }} overflow-x-auto rounded-lg border border-[#E5E7EB]">
            <table class="w-full min-w-[760px] table-fixed border-collapse text-left text-sm">
                <colgroup>
                    <col class="w-[30%]">
                    <col class="w-[26%]">
                    <col class="w-[14%]">
                    <col class="w-[14%]">
                    <col class="w-[16%]">
                </colgroup>
                <thead class="bg-[#F3F4F6]">
                    <tr class="text-[#111827]">
                        <th scope="col" class="px-4 py-2.5 font-semibold">Mata Kuliah</th>
                        <th scope="col" class="px-4 py-2.5 font-semibold">Dosen Pengampu</th>
                        <th scope="col" class="px-4 py-2.5 font-semibold">Tingkat</th>
                        <th scope="col" class="px-4 py-2.5 font-semibold">Semester</th>
                        <th scope="col" class="px-4 py-2.5 font-semibold">{{ $tab === 'berjalan' ? 'SKS' : 'Tahun Akademik' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse ($rows as $r)
                        <tr class="hover:bg-[#F9FAFB]">
                            <td class="truncate px-4 py-2.5" title="{{ $r['mk'] }}">{{ $r['mk'] }}</td>
                            <td class="truncate px-4 py-2.5" title="{{ $r['dosen'] }}">{{ $r['dosen'] }}</td>
                            <td class="px-4 py-2.5">Tingkat {{ $r['tingkat'] }}</td>
                            <td class="px-4 py-2.5">{{ $r['periode'] }}</td>
                            <td class="px-4 py-2.5">{{ $tab === 'berjalan' ? ($r['sks'] ?? '—') : ($r['tahun'] ?? '—') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-[#6B7280]">Tidak ada mata kuliah yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-[#6B7280]">
            <span>
                @if ($tab === 'berjalan') {{ $n }} mata kuliah dipaketkan untuk semester aktif
                @else {{ $n }} catatan sumber ditampilkan • Nama mata kuliah berulang dipertahankan @endif
            </span>
            <span>Halaman 1 dari 1</span>
        </div>
    </section>
</div>
@endsection