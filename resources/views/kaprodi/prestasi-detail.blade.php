@extends('layouts.kaprodi')

@section('title', 'Detail Prestasi')

@section('content')
<main class="flex flex-col gap-8 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('kaprodi.prestasi') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#2277FF] hover:underline">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                    Kembali ke Daftar Prestasi
                </a>
            </div>
            <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $prestasi['nama'] }}</h1>
            <p class="text-xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8] sm:text-2xl">Dashboard / Prestasi / Detail</p>
        </div>
    </div>

    <div class="flex flex-col gap-8 lg:flex-row lg:items-start">
        <div class="flex flex-1 flex-col gap-6 rounded-lg bg-white p-6 shadow-sm">
            {{-- Gambar Sampul (Opsional, tetap dibiarkan default jika tidak ada gambar spesifik) --}}
            <div class="aspect-[16/9] w-full overflow-hidden rounded-xl bg-[#F3F4F6]">
                <img src="{{ asset('images/slides/PBL.jpeg') }}" alt="{{ $prestasi['nama'] }}" class="h-full w-full object-cover">
            </div>

            <div class="flex flex-col gap-3">
                <h2 class="text-xl font-bold text-[#1C2024]">Deskripsi Pencapaian</h2>
                <p class="text-justify text-base leading-7 text-[#55555C]">
                    {{-- MENGAMBIL DESKRIPSI ASLI DARI DATABASE --}}
                    {{ $modelAsli->catatan ?? ($modelAsli->kegiatan->deskripsi ?? 'Tidak ada catatan atau deskripsi yang dicantumkan untuk prestasi ini.') }}
                </p>
            </div>

            <hr class="border-[#0A0A0A]/[0.06]">

            <div class="flex flex-col gap-3">
                <h2 class="text-xl font-bold text-[#1C2024]">Anggota Tim / Peraih Prestasi</h2>
                <ul class="flex flex-col gap-2">
                    @foreach ($prestasi['anggota'] as $idx => $anggota)
                        <li class="flex items-center gap-3 rounded-lg border border-[#E8E8EC] bg-[#FAFAFA] p-3">
                            <span class="flex size-7 items-center justify-center rounded-full bg-[#2277FF]/10 text-xs font-bold text-[#2277FF]">
                                {{ $idx + 1 }}
                            </span>
                            <span class="text-base font-medium text-[#1C2024]">{{ $anggota }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- FITUR BARU: BUKTI PRESTASI --}}
            @if(isset($modelAsli) && $modelAsli->buktiPrestasi && $modelAsli->buktiPrestasi->count() > 0)
            <hr class="border-[#0A0A0A]/[0.06]">
            <div class="flex flex-col gap-3">
                <h2 class="text-xl font-bold text-[#1C2024]">Bukti Prestasi (Sertifikat / Dokumen)</h2>
                <ul class="flex flex-col gap-2">
                    @foreach ($modelAsli->buktiPrestasi as $bukti)
                        <li class="flex items-center justify-between rounded-lg border border-[#E8E8EC] bg-[#FAFAFA] p-3">
                            <div class="flex items-center gap-3">
                                <span class="flex size-7 items-center justify-center rounded-full bg-[#2277FF]/10 text-xs font-bold text-[#2277FF]">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </span>
                                <span class="text-base font-medium text-[#1C2024]">
                                    {{ $bukti->nama_file ?? 'Dokumen Bukti' }} 
                                    <span class="text-xs text-[#A8A8A8]">({{ $bukti->jenisDokumen->nama_jenis ?? 'Lainnya' }})</span>
                                </span>
                            </div>
                            @if($bukti->link)
                                <a href="{{ $bukti->link }}" target="_blank" class="text-sm font-semibold text-[#2277FF] hover:underline">Lihat Tautan</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>

        {{-- Kolom Kanan: Card Informasi Ringkas (Sticky Scroll) --}}
        <div class="w-full shrink-0 lg:sticky lg:top-8 lg:w-[360px]">
            <div class="flex flex-col gap-4 rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold tracking-[-0.2px] text-[#1C2024]">Informasi Prestasi</h2>
                <hr class="border-[#0A0A0A]/[0.06]">

                <div class="flex flex-col gap-4">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Kategori</span>
                        <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['kategori'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Tipe Peraih</span>
                        <p class="mt-1">
                            <span class="inline-flex rounded-lg border px-2.5 py-1 text-xs font-semibold {{ $prestasi['tipe'] === 'Dosen' ? 'border-[#D8C8F5] bg-[#EADFFB] text-[#6A3FC7]' : 'border-[#B7E0E0] bg-[#D9F1F1] text-[#0E7C7B]' }}">
                                {{ $prestasi['tipe'] }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Diunggah Oleh</span>
                        <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['pengunggah'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Semester</span>
                        <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['semester'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Tanggal Diraih</span>
                        <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['tanggal'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection