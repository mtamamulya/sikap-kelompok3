@extends('layouts.app')

@section('title', 'Tambah Pengajaran')

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-3xl font-bold tracking-tight">Tambah Pengajaran</h1>
        <p class="text-2xl text-gray-400">Dashboard / Pengajaran / Tambah</p>
    </div>

    {{-- Panggil form tanpa passing variabel PHP apapun --}}
    @include('dosen.pengajaran._form')

@endsection