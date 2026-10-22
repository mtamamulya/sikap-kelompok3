@extends('layouts.app')
@section('title', request('menu', 'Halaman'))
@section('content')
<div class="flex h-[60vh] items-center justify-center px-6 text-center">
    <div>
        <h1 class="text-[26px] font-bold">{{ request('menu', 'Halaman') }}</h1>
        <p class="mt-2 text-[13px] text-[#6B7280]">Halaman ini segera hadir.</p>
    </div>
</div>
@endsection