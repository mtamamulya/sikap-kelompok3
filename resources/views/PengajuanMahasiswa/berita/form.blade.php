@extends('layouts.pengajuan-app')
@section('title', $item ? 'Edit Berita' : 'Ajukan Berita')
@section('content')
@php
    $value = fn ($key, $default = '') => old($key, $item[$key] ?? $default);
    $input = 'h-10 w-full rounded-lg border border-[#E5E7EB] bg-white px-3 text-[13px] outline-none placeholder:text-[#9CA3AF] focus:border-[#2277FF]';
    $select = $input.' appearance-none pr-9';
@endphp
<div class="mx-auto flex w-full max-w-5xl flex-col gap-5 px-4 pb-10 pt-7 sm:px-6">
    <div>
        <h1 class="text-[26px] font-bold leading-9">{{ $item ? 'Edit Berita' : 'Ajukan Berita' }}</h1>
        <div class="mt-1 flex flex-wrap gap-1 text-[13px] text-[#9CA3AF]">
            <a href="{{ route('pengajuan.index') }}" class="hover:text-[#2563EB]">Dashboard</a>
            <span>/</span><a href="{{ route('pengajuan.berita.index') }}" class="hover:text-[#2563EB]">Pengajuan</a>
            <span>/</span><span>Berita</span>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" x-data="{ sourceType: @js($value('source_type', 'previous')) }"
          action="{{ $item ? route('pengajuan.berita.update', $item['id']) : route('pengajuan.berita.store') }}"
          class="flex flex-col gap-5 rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[0_4px_12px_rgba(0,0,0,0.05)] sm:p-7">
        @csrf
        @if ($item) @method('PUT') @endif
        @if ($errors->any())
            <div class="rounded-lg bg-[#FEF2F2] px-4 py-3 text-sm text-[#B91C1C]">Periksa kembali isian yang ditandai sebelum mengajukan berita.</div>
        @endif

        <section class="flex flex-col gap-4 rounded-xl border border-[#E5E7EB] p-4">
            <div>
                <h2 class="text-sm font-bold text-[#111827]">Sumber kegiatan</h2>
                <p class="mt-1 text-xs text-[#6B7280]">Pilih apakah berita ini terkait dengan kegiatan sebelumnya.</p>
            </div>
            <div class="flex flex-wrap gap-x-8 gap-y-3">
                <label class="flex items-center gap-2 text-[13px] text-[#374151]">
                    <input type="radio" name="source_type" value="previous" x-model="sourceType" @checked($value('source_type') === 'previous')>
                    Dari kegiatan sebelumnya
                </label>
                <label class="flex items-center gap-2 text-[13px] text-[#374151]">
                    <input type="radio" name="source_type" value="new" x-model="sourceType" @checked($value('source_type') === 'new')>
                    Tanpa kegiatan sebelumnya
                </label>
            </div>
            @error('source_type')<p class="text-xs text-[#DC2626]">{{ $message }}</p>@enderror

            <label x-show="sourceType === 'previous'" class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">
                Kegiatan sebelumnya
                <div class="relative">
                    <select name="previous_activity" :disabled="sourceType !== 'previous'" class="{{ $select }}">
                        <option value="">Pilih kegiatan</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity }}" @selected($value('previous_activity') === $activity)>{{ $activity }}</option>
                        @endforeach
                    </select>
                    <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                </div>
                @error('previous_activity')<span class="text-xs text-[#DC2626]">{{ $message }}</span>@enderror
            </label>

            <label x-show="sourceType === 'new'" x-cloak class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">
                Nama kegiatan baru
                <input name="activity_name" value="{{ $value('activity_name') }}" :disabled="sourceType !== 'new'" maxlength="255" class="{{ $input }}" placeholder="Masukkan nama kegiatan">
                @error('activity_name')<span class="text-xs text-[#DC2626]">{{ $message }}</span>@enderror
            </label>
        </section>

        <section class="flex flex-col gap-4">
            <div>
                <h2 class="text-sm font-bold text-[#111827]">Informasi berita</h2>
                <p class="mt-1 text-xs text-[#6B7280]">Isi berita untuk dibagikan melalui halaman program studi.</p>
            </div>

            <label class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">
                Kategori berita <span class="text-[#FF3F3F]">*</span>
                <div class="relative">
                    <select name="category" class="{{ $select }}">
                        <option value="">Pilih kategori berita</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected($value('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                </div>
                @error('category')<span class="text-xs text-[#DC2626]">{{ $message }}</span>@enderror
            </label>

            <label class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">
                Judul berita <span class="text-[#FF3F3F]">*</span>
                <input name="title" value="{{ $value('title') }}" maxlength="255" class="{{ $input }}" placeholder="Contoh: Mahasiswa TRK Juara 2 Lomba Nasional">
                @error('title')<span class="text-xs text-[#DC2626]">{{ $message }}</span>@enderror
            </label>

            <label class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">
                Isi berita <span class="text-[#FF3F3F]">*</span>
                <textarea name="content" rows="7" maxlength="5000"
                          class="w-full resize-y rounded-lg border border-[#E5E7EB] bg-white p-3 text-[13px] outline-none placeholder:text-[#9CA3AF] focus:border-[#2277FF]"
                          placeholder="Tuliskan isi berita">{{ $value('content') }}</textarea>
                <span class="text-xs text-[#6B7280]">Maksimal 5.000 karakter.</span>
                @error('content')<span class="text-xs text-[#DC2626]">{{ $message }}</span>@enderror
            </label>
        </section>

        <section class="flex flex-col gap-2">
            <div>
                <h2 class="text-sm font-bold text-[#111827]">Foto sampul berita</h2>
                <p class="mt-1 text-xs text-[#6B7280]">Unggah foto yang mendukung berita. Format JPG, PNG, atau WEBP, maksimal 5 MB.</p>
            </div>
            @if ($item && $item['cover'])
                <img src="{{ asset('storage/'.$item['cover']) }}" alt="Foto sampul berita saat ini" class="max-h-64 w-fit rounded-lg object-cover">
            @endif
            <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-[#93C5FD] bg-[#F8FBFF] px-4 py-8 text-center transition hover:bg-[#EFF6FF]">
                <i class="ti ti-cloud-upload text-2xl text-[#2277FF]"></i>
                <span class="text-[13px] text-[#6B7280]"><span class="font-semibold text-[#2277FF]">Pilih foto</span> dari perangkat</span>
                <span class="text-xs text-[#9CA3AF]">JPG, JPEG, PNG, WEBP · Maksimal 5 MB</span>
                <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="sr-only">
            </label>
            @error('cover')<p class="text-xs text-[#DC2626]">{{ $message }}</p>@enderror
        </section>

        <div class="flex flex-wrap justify-end gap-2 border-t border-[#E5E7EB] pt-5">
            <a href="{{ route('pengajuan.berita.index') }}" class="flex h-10 items-center rounded-lg border border-[#E5E7EB] px-4 text-[13px] font-semibold text-[#374151]">Batal</a>
            <button type="submit" name="intent" value="draft" class="flex h-10 items-center rounded-lg border border-[#93C5FD] px-4 text-[13px] font-semibold text-[#2563EB] hover:bg-[#EFF6FF]">Simpan Draft</button>
            <button type="submit" name="intent" value="submit" class="flex h-10 items-center rounded-lg bg-[#2563EB] px-4 text-[13px] font-semibold text-white hover:bg-[#1D4ED8]">Ajukan Berita</button>
        </div>
    </form>
</div>
@endsection
