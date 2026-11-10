@extends('layouts.app')

@section('title', 'Tambah Pengajaran')

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-3xl font-bold tracking-tight">Tambah Pengajaran</h1>
        <p class="text-2xl text-gray-400">Dashboard / Pengajaran / Tambah</p>
    </div>

    {{-- $opsi dan $dosenLogin dikirim oleh DosenPengajaranController@create. Isi form ada di _form.blade.php. --}}
    @include('dosen.pengajaran._form', [
        'aksi' => route('pengajaran.store'),
        'metode' => 'POST',
        'awal' => [],
    ])

@endsection