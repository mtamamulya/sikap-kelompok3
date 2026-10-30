@extends('layouts.app')

@section('title', 'Ajukan Berita')

@section('content')

    <div class="flex flex-col gap-1.5">
        <h1 class="text-3xl font-bold tracking-tight">Ajukan Berita</h1>
        <p class="text-2xl text-gray-400">Dashboard / Pengajuan / Berita / Pengajuan Berita</p>
    </div>

    @include('berita._form', ['b' => null, 'aksi' => '#'])

@endsection