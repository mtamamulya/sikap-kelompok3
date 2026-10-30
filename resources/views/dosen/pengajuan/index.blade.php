@extends('layouts.app')

@section('title', 'Pengajuan Dosen')

@section('content')
@php
    $statusStyles = [
        'Diajukan' => 'bg-blue-100 text-blue-700',
        'Direview' => 'bg-amber-100 text-amber-700',
        'Direvisi' => 'bg-orange-100 text-orange-700',
        'Ditolak' => 'bg-red-100 text-red-700',
        'Diterima' => 'bg-green-100 text-green-700',
        'Selesai' => 'bg-gray-100 text-gray-700',
    ];
    $counts = collect($applications)->countBy('status');
    $summary = [
        ['Total Pengajuan', count($applications), 'bg-blue-500'],
        ['Diajukan', $counts->get('Diajukan', 0), 'bg-sky-500'],
        ['Direview', $counts->get('Direview', 0), 'bg-amber-500'],
        ['Selesai', $counts->get('Selesai', 0) + $counts->get('Diterima', 0), 'bg-green-500'],
    ];
@endphp

<div class="flex flex-col gap-6">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Pengajuan Dosen</h1>
        <p class="mt-1 text-sm text-gray-500">Dashboard / Pengajuan / Dosen</p>
    </div>

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Ringkasan pengajuan dosen">
        @foreach ($summary as [$label, $value, $accent])
            <article class="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-4 pl-5 shadow-sm">
                <span aria-hidden="true" class="absolute inset-y-0 left-0 w-1.5 {{ $accent }}"></span>
                <h2 class="text-sm font-medium text-gray-500">{{ $label }}</h2>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm" aria-labelledby="daftar-pengajuan-dosen">
        <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
            <h2 id="daftar-pengajuan-dosen" class="text-lg font-semibold">Daftar Pengajuan Dosen</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">Nama kegiatan</th>
                        <th scope="col" class="px-5 py-3">Diajukan oleh</th>
                        <th scope="col" class="px-5 py-3">Kategori</th>
                        <th scope="col" class="px-5 py-3">Status</th>
                        <th scope="col" class="px-5 py-3"><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($applications as $application)
                        <tr class="text-gray-700">
                            <th scope="row" class="px-5 py-4 font-semibold text-gray-900">{{ $application['title'] }}</th>
                            <td class="px-5 py-4">{{ $application['applicant'] }}</td>
                            <td class="px-5 py-4">{{ $application['category'] }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusStyles[$application['status']] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $application['status'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('pengajuan.dosen.show', $application['id']) }}" class="font-semibold text-brand hover:underline">
                                    Lihat detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-500">Belum ada pengajuan dosen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
