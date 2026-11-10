@extends('layouts.app')

@section('title', 'Edit Pengajaran')

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-3xl font-bold tracking-tight">Edit Pengajaran</h1>
        <p class="text-2xl text-gray-400">Dashboard / Pengajaran / Edit Pengajaran</p>
    </div>

    {{-- $item, $opsi dan $dosenLogin dikirim oleh DosenPengajaranController@edit. Isi form ada di _form.blade.php. --}}
    @include('dosen.pengajaran._form', [
        'aksi' => route('pengajaran.update', $item->getKey()),
        'metode' => 'PUT',
        'awal' => ['rombel' => $item->id_enrollment_tingkat_kelas, 'matkul' => $item->id_enrollment_mk],
    ])

@endsection