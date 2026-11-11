@extends('layouts.admin')

@section('title', 'Detail Prestasi')

@section('content')
@php
    // Controller mengirim daftar bukti berbentuk ['id' => ..., 'link' => ...].
    // Tampilkan hanya URL HTTP(S) atau berkas publik yang dikelola controller.
    $tautanBukti = collect($prestasi['bukti'] ?? [])->map(function ($bukti) {
        $path = $bukti['link'] ?? null;
        if (!is_string($path) || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'bukti-prestasi/') && !str_contains($path, '..')) {
            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
        } elseif (preg_match('~^https?://~i', $path)) {
            $url = $path;
        } else {
            return null;
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
        return [
            'url' => $url,
            'is_image' => in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true),
        ];
    })->filter()->values();

    $gambarBukti = $tautanBukti->first(fn ($bukti) => $bukti['is_image']);
    $pdfBukti = $tautanBukti->first(
        fn ($bukti) => strtolower(
            pathinfo(parse_url($bukti['url'], PHP_URL_PATH) ?: '', PATHINFO_EXTENSION)
        ) === 'pdf'
    );
@endphp

<main class="flex flex-col gap-8 px-4 py-8 sm:px-6">
    {{-- Header & Kembali --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.prestasi') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#2277FF] hover:underline">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                    Kembali ke Daftar Prestasi
                </a>
            </div>
            <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $prestasi['nama'] }}</h1>
            <p class="text-xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8] sm:text-2xl">Dashboard / Prestasi / Detail</p>
        </div>
    </div>

    {{-- Konten Utama 2 Kolom --}}
    <div class="flex flex-col gap-8 lg:flex-row lg:items-start">
        {{-- Kolom Kiri: Sertifikat/Foto & Anggota Tim --}}
        <div class="flex flex-1 flex-col gap-6 rounded-lg bg-white p-6 shadow-sm">
            <div class="aspect-[16/9] w-full overflow-hidden rounded-xl bg-[#F3F4F6]">
                @if ($gambarBukti)
                    <img
                        src="{{ $gambarBukti['url'] }}"
                        alt="Bukti prestasi: {{ $prestasi['nama'] }}"
                        class="h-full w-full object-contain"
                    >
                @elseif ($pdfBukti)
                    <iframe
                        src="{{ $pdfBukti['url'] }}"
                        title="Pratinjau PDF Bukti Prestasi"
                        class="h-full w-full border-0"
                    ></iframe>
                @else
                    <div class="flex h-full items-center justify-center p-6 text-center text-sm text-[#6B7280]">
                        Belum ada pratinjau bukti yang tersedia.
                    </div>
                @endif
            </div>
            @if ($tautanBukti->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach ($tautanBukti as $bukti)
                        <a href="{{ $bukti['url'] }}" target="_blank" rel="noopener noreferrer"
                           class="rounded-lg border border-[#AAC8F8] bg-[#F9FAFB] px-3 py-2 text-sm font-medium text-[#2277FF] hover:bg-[#EEF5FF]">
                            Lihat Bukti {{ $loop->iteration }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="flex flex-col gap-3">
                <h2 class="text-xl font-bold text-[#1C2024]">Deskripsi Pencapaian</h2>
                <p class="text-justify text-base leading-7 text-[#55555C]">
                    {{ $prestasi['catatan'] ?: 'Belum ada deskripsi tambahan untuk prestasi ini.' }}
                </p>
            </div>

            <hr class="border-[#0A0A0A]/[0.06]">

            <div class="flex flex-col gap-3">
                <h2 class="text-xl font-bold text-[#1C2024]">Anggota Kegiatan Terkait</h2>
                <ul class="flex flex-col gap-2">
                    @forelse ($prestasi['anggota'] as $idx => $anggota)
                        <li class="flex items-center gap-3 rounded-lg border border-[#E8E8EC] bg-[#FAFAFA] p-3">
                            <span class="flex size-7 items-center justify-center rounded-full bg-[#2277FF]/10 text-xs font-bold text-[#2277FF]">
                                {{ $idx + 1 }}
                            </span>
                            <span class="text-base font-medium text-[#1C2024]">{{ $anggota }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-[#6B7280]">Belum ada anggota kegiatan yang tercatat.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Kolom Kanan: Card Informasi Ringkas (Sticky Scroll) --}}
        <div class="w-full shrink-0 lg:sticky lg:top-8 lg:w-[360px]">
            <div class="flex flex-col gap-4 rounded-lg bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold tracking-[-0.2px] text-[#1C2024]">Informasi Prestasi</h2>
                <hr class="border-[#0A0A0A]/[0.06]">

                <div class="flex flex-col gap-4">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Kategori Kegiatan</span>
                        <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['kategori'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Tipe Pemilik Kegiatan</span>
                        <p class="mt-1">
                            <span class="inline-flex rounded-lg border px-2.5 py-1 text-xs font-semibold {{ $prestasi['tipe'] === 'Dosen' ? 'border-[#D8C8F5] bg-[#EADFFB] text-[#6A3FC7]' : ($prestasi['tipe'] === 'Mahasiswa' ? 'border-[#B7E0E0] bg-[#D9F1F1] text-[#0E7C7B]' : 'border-[#E5E7EB] bg-[#F9FAFB] text-[#6B7280]') }}">
                                {{ $prestasi['tipe'] }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Pemilik/Pengaju Kegiatan</span>
                        <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['pengunggah'] }}</p>
                    </div>
                    @if (($prestasi['semester'] ?? '-') !== '-')
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Semester</span>
                            <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['semester'] }}</p>
                        </div>
                    @endif
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Tanggal Selesai Kegiatan</span>
                        <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['tanggal'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Jenis Prestasi</span>
                        <p class="text-base font-medium text-[#1C2024]">{{ ucfirst($prestasi['jenis_prestasi'] ?? '-') }}</p>
                    </div>
                    @if (!empty($prestasi['bidang_lomba']))
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#A8A8A8]">Bidang Lomba</span>
                            <p class="text-base font-medium text-[#1C2024]">{{ $prestasi['bidang_lomba'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection