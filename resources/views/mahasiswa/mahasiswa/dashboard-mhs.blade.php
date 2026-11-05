@extends('layouts.mahasiswa-tim')

@section('title', 'Dashboard Mahasiswa')

@section('content')
@php
    $jam     = now()->hour;
    $sapaan  = match (true) { $jam < 11 => 'pagi', $jam < 15 => 'siang', $jam < 18 => 'sore', default => 'malam' };
    $depan   = explode(' ', trim($nama))[0];
    $persen  = $ringkasan['sks_total'] > 0 ? round($ringkasan['sks_tempuh'] / $ringkasan['sks_total'] * 100) : 0;
    $selectCls = 'w-full appearance-none rounded-lg border border-[#E5E7EB] bg-white py-2 pl-3.5 pr-9 text-sm text-[#0A0A0A] outline-none focus:border-[#27F]';

    // Gaya kartu statistik dashboard ini, tanpa mengubah komponen yang dipakai halaman lain.
    $kartuCls  = 'group relative flex h-full flex-col gap-3 overflow-hidden rounded-xl border border-[#E5E7EB] bg-white p-5 pl-6 shadow-[0_2px_8px_rgba(0,0,0,0.02)] transition-all duration-300 hover:-translate-y-1 hover:border-[#D1D5DB] hover:shadow-md';
    $judulCls  = 'text-sm font-bold text-[#111827]';
    $angkaCls  = 'text-3xl font-bold leading-none';
    $satuanCls = 'text-xs text-[#6B7280]';
    $ketCls    = 'text-xs text-[#6B7280]';
@endphp

<div class="flex flex-col gap-6">

    {{-- Kartu statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="{{ $kartuCls }}">
            <span class="absolute inset-y-0 left-0 w-1.5 transition-all duration-300 group-hover:w-2" style="background-color: #2277FF"></span>
            <div class="flex items-start justify-between gap-3">
                <span class="{{ $judulCls }}">Tahun Akademik</span>
                <span class="flex size-10 items-center justify-center rounded-xl bg-[#EFF6FF] text-[#2563EB] transition-transform duration-300 group-hover:scale-110">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/></svg>
                </span>
            </div>
            <div class="flex flex-col gap-2.5">
                <div class="flex flex-wrap items-end gap-x-2 gap-y-1">
                    <span class="{{ $angkaCls }}">{{ $ringkasan['tahun_akademik'] }}</span>
                    <span class="{{ $satuanCls }}">{{ $ringkasan['jenis_semester'] }}</span>
                </div>
                <span class="{{ $ketCls }}">Semester berjalan</span>
            </div>
        </div>

        <div class="{{ $kartuCls }}">
            <span class="absolute inset-y-0 left-0 w-1.5 transition-all duration-300 group-hover:w-2" style="background-color: #06B6D4"></span>
            <div class="flex items-start justify-between gap-3">
                <span class="{{ $judulCls }}">SKS Ditempuh</span>
                <span class="flex size-10 items-center justify-center rounded-xl bg-[#ECFEFF] text-[#0891B2] transition-transform duration-300 group-hover:scale-110">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/></svg>
                </span>
            </div>
            <div class="flex flex-col gap-2.5">
                <div class="flex flex-wrap items-end gap-x-2 gap-y-1">
                    <span class="{{ $angkaCls }}">{{ $ringkasan['sks_tempuh'] }}</span>
                    <span class="{{ $satuanCls }}">Dari {{ $ringkasan['sks_total'] }} SKS kelulusan</span>
                </div>
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-[#E5E7EB]" role="progressbar" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="h-full rounded-full bg-[#27F]" style="width: {{ $persen }}%"></div>
                </div>
                <span class="{{ $ketCls }}">{{ $persen }}% perjalanan studi</span>
            </div>
        </div>

        <div class="{{ $kartuCls }}">
            <span class="absolute inset-y-0 left-0 w-1.5 transition-all duration-300 group-hover:w-2" style="background-color: #8B5CF6"></span>
            <div class="flex items-start justify-between gap-3">
                <span class="{{ $judulCls }}">Semester Berjalan</span>
                <span class="flex size-10 items-center justify-center rounded-xl bg-[#F5F3FF] text-[#7C3AED] transition-transform duration-300 group-hover:scale-110">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2v-12"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/></svg>
                </span>
            </div>
            <div class="flex flex-col gap-2.5">
                <div class="flex flex-wrap items-end gap-x-2 gap-y-1">
                    <span class="{{ $angkaCls }}">{{ $ringkasan['semester_ke'] }}</span>
                    <span class="{{ $satuanCls }}">{{ $ringkasan['jenis_semester'] }} • {{ $ringkasan['tahun_akademik'] }}</span>
                </div>
                <span class="{{ $ketCls }}">{{ $ringkasan['jumlah_mk'] }} mata kuliah • {{ $ringkasan['sks_semester'] }} SKS</span>
            </div>
        </div>

        <div class="{{ $kartuCls }}">
            <span class="absolute inset-y-0 left-0 w-1.5 transition-all duration-300 group-hover:w-2" style="background-color: #10B981"></span>
            <div class="flex items-start justify-between gap-3">
                <span class="{{ $judulCls }}">Pengajuan Aktif</span>
                <span class="flex size-10 items-center justify-center rounded-xl bg-[#ECFDF5] text-[#059669] transition-transform duration-300 group-hover:scale-110">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1-2-2v-14a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2"/><path d="M9 15l2 2l4-4"/></svg>
                </span>
            </div>
            <div class="flex flex-col gap-2.5">
                <div class="flex flex-wrap items-end gap-x-2 gap-y-1">
                    <span class="{{ $angkaCls }}">{{ $ringkasan['pengajuan_aktif'] }}</span>
                    <span class="{{ $satuanCls }}">Sedang diverifikasi</span>
                </div>
                <span class="{{ $ketCls }}">{{ $ringkasan['dokumen_siap'] }} dokumen siap diunduh</span>
            </div>
        </div>
    </div>


    {{-- Filter dan tabel mata kuliah --}}
    <section class="flex flex-col gap-4 rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
        <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-[1fr_180px_180px_180px_auto]">
            <div class="flex items-center gap-2 rounded-lg border border-[#E5E7EB] bg-white px-3.5 py-2 focus-within:border-[#27F] sm:col-span-2 xl:col-span-1">
                <svg class="size-4 shrink-0 text-[#6B7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0-14 0"/><path d="M21 21l-6-6"/></svg>
                <input type="search" name="q" value="{{ $filter['q'] }}" data-live-search placeholder="Cari mata kuliah atau dosen..."
                       class="min-w-0 flex-1 bg-transparent text-sm outline-none placeholder:text-[#A8A8A8]">
            </div>

            @foreach ([
                ['semester', 'Semua Semester', ['Ganjil' => 'Ganjil', 'Genap' => 'Genap']],
                ['tingkat',  'Semua Tingkat',  [1 => 'Tingkat 1', 2 => 'Tingkat 2', 3 => 'Tingkat 3', 4 => 'Tingkat 4']],
                ['tahun',    'Tahun Akademik', $tahunList],
            ] as [$field, $placeholder, $options])
                <div class="relative">
                    <select name="{{ $field }}" class="{{ $selectCls }}" onchange="this.form.submit()">
                        <option value="">{{ $placeholder }}</option>
                        @foreach ($options as $val => $text)
                            <option value="{{ $val }}" @selected((string) $filter[$field] === (string) $val)>{{ $text }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-[#6B7280]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6"/></svg>
                </div>
            @endforeach

            {{-- Reset: kembali ke halaman tanpa filter apa pun --}}
            <a href="{{ url()->current() }}"
               class="inline-flex h-[38px] items-center justify-center rounded-lg border border-[#E5E7EB] bg-white px-4 text-sm font-semibold text-[#0062FF] transition hover:bg-[#EFF6FF] active:scale-95 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#27F]">
                Reset
            </a>
            <button type="submit" class="sr-only">Terapkan</button>
        </form>

        <div class="hidden overflow-x-auto rounded-lg border border-[#E5E7EB] md:block">
            {{-- table-fixed + colgroup: lebar kolom dikunci, tidak lagi mengikuti isi data --}}
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
                        <th scope="col" class="px-4 py-2.5 font-semibold">Tahun Akademik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse ($mataKuliah as $mk)
                        <tr class="hover:bg-[#F9FAFB]">
                            <td class="truncate px-4 py-2.5" title="{{ $mk['mk'] }}">{{ $mk['mk'] }}</td>
                            <td class="truncate px-4 py-2.5" title="{{ $mk['dosen'] }}">{{ $mk['dosen'] }}</td>
                            <td class="px-4 py-2.5">Tingkat {{ $mk['tingkat'] }}</td>
                            <td class="px-4 py-2.5">{{ $mk['semester'] }}</td>
                            <td class="px-4 py-2.5">{{ $mk['tahun'] ?? ($mk['tahun_akademik'] ?? '—') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-[#6B7280]">Tidak ada mata kuliah yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 md:hidden">
            @forelse ($mataKuliah as $mk)
                <article class="flex min-w-0 flex-col gap-3 rounded-xl border border-[#E5E7EB] bg-white p-3.5">
                    <div class="min-w-0">
                        <h2 class="break-words text-sm font-semibold text-[#111827]">{{ $mk['mk'] }}</h2>
                        <p class="mt-1 break-words text-xs text-[#6B7280]">{{ $mk['dosen'] }}</p>
                    </div>
                    <dl class="grid grid-cols-2 gap-x-3 gap-y-2 border-t border-[#EEF0F3] pt-3 text-xs">
                        <div>
                            <dt class="text-[#6B7280]">Tingkat</dt>
                            <dd class="mt-0.5 font-medium text-[#111827]">Tingkat {{ $mk['tingkat'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-[#6B7280]">Semester</dt>
                            <dd class="mt-0.5 font-medium text-[#111827]">{{ $mk['semester'] }}</dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-[#6B7280]">Tahun Akademik</dt>
                            <dd class="mt-0.5 font-medium text-[#111827]">{{ $mk['tahun'] ?? ($mk['tahun_akademik'] ?? '—') }}</dd>
                        </div>
                    </dl>
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-[#D1D5DB] px-4 py-8 text-center text-sm text-[#6B7280]">Tidak ada mata kuliah yang cocok dengan filter.</p>
            @endforelse
        </div>

        {{ $mataKuliah->links('mahasiswa.mahasiswa.pagination.mahasiswa') }}
    </section>
</div>
@endsection