@extends('layouts.app')

@section('title', 'Edit Berita')

@php
    // DATA TIRUAN dari berita/data.php. $id berasal dari alamat /pengajuan/berita/{id}/edit.
    $semua = require resource_path('views/berita/data.php');
    $b     = $semua[max(1, min(count($semua), (int) ($id ?? 1))) - 1];
@endphp

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-3xl font-bold tracking-tight">Edit Berita</h1>
        <p class="text-2xl text-gray-400">Dashboard / Pengajuan / Berita / Edit</p>
    </div>

    {{-- Kalau berita diminta revisi, catatan admin tampil di atas form --}}
    @if ($b['status'] === 'Direvisi' && $b['catatan'])
        <div role="status" class="flex items-start gap-3 rounded-lg border border-orange-200 bg-orange-50 p-4 text-[15px] text-orange-900">
            <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M12 7.5v5.5M12 16.5v.01"/></svg>
            <div>
                <p class="font-semibold">Catatan revisi dari admin</p>
                <p>{{ $b['catatan'] }}</p>
                <p class="mt-1 text-sm text-orange-800">Perbaiki berita di bawah ini, lalu klik &ldquo;Ajukan Berita&rdquo; untuk mengajukan kembali.</p>
            </div>
        </div>
    @endif

    @include('berita._form', ['b' => $b, 'aksi' => '#'])

@endsection