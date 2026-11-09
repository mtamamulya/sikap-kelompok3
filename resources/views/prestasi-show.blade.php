@extends('layouts.publik')

@section('title', $record->kegiatan?->nama_kegiatan ?? 'Detail Prestasi')

@section('content')
<div class="min-h-screen bg-white text-black" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <x-navbar-publik />

    <main class="mx-auto max-w-[1728px] px-4 py-6 sm:px-8">
        <div class="flex flex-col gap-8 px-0 py-4 sm:px-4">
            <div class="flex flex-col gap-2">
                <p class="text-base font-normal tracking-[-0.18px] text-[#737C8D]">
                    <a href="{{ route('home') }}" class="hover:underline">Beranda</a>
                    / <a href="{{ route('publik.prestasi.index') }}" class="hover:underline">Prestasi</a>
                    / Detail Prestasi
                </p>
                <span class="w-fit rounded-full bg-[#CDDEF9] px-3 py-1 text-sm font-semibold text-[#0B3A8C]">
                    {{ $record->capaian ?: ucfirst($record->jenis_prestasi) }}
                </span>
                <h1 class="max-w-[1000px] text-[24px] font-bold leading-[34px] tracking-[-0.2px] sm:text-[32px] sm:leading-[42px]">
                    {{ $record->kegiatan?->nama_kegiatan ?? 'Prestasi' }}
                </h1>
                <p class="text-sm text-[#64758A]">
                    {{ ucfirst($record->jenis_prestasi) }}
                    @if ($record->bidang_lomba)
                        · {{ $record->bidang_lomba }}
                    @endif
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_400px]">
                <article class="flex min-w-0 flex-col gap-6">
                    <div class="flex min-h-[220px] items-center justify-center rounded-xl bg-[#E4EDFB] text-[#2277FF] sm:min-h-[320px]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="88" height="88" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4Z" />
                            <path d="M7 6H4v2a4 4 0 0 0 4 4M17 6h3v2a4 4 0 0 1-4 4" />
                        </svg>
                    </div>

                    @if ($record->catatan)
                        <div class="flex flex-col gap-4 text-justify text-base leading-7 text-[#27364B]">
                            <p>{{ $record->catatan }}</p>
                        </div>
                    @endif

                    <section>
                        <h2 class="text-xl font-bold text-[#071E38]">Bukti Prestasi</h2>
                        <div class="mt-3 flex flex-col divide-y divide-[#E6E9EE] rounded-xl border border-[#E6E9EE] px-4">
                            @forelse ($record->buktiPrestasi as $bukti)
                                <a href="{{ $bukti->link }}" target="_blank" rel="noopener noreferrer"
                                   class="flex flex-wrap items-center justify-between gap-2 py-4 text-sm text-[#2277FF] hover:underline">
                                    <span class="font-semibold">{{ $bukti->nama_file ?: $bukti->jenisDokumen?->nama_jenis_dokumen ?: 'Dokumen bukti' }}</span>
                                    <span>{{ $bukti->jenisDokumen?->nama_jenis_dokumen }}</span>
                                </a>
                            @empty
                                <p class="py-4 text-sm text-[#7D8999]">Belum ada bukti prestasi yang tersedia.</p>
                            @endforelse
                        </div>
                    </section>
                </article>

                <aside class="flex flex-col gap-8 lg:sticky lg:top-[120px] lg:self-start">
                    <section class="flex flex-col gap-4 rounded-[10px] border border-[#E3E3E3] px-5 py-6">
                        <h2 class="text-2xl font-bold leading-[30px] text-[#071E38]">Informasi Prestasi</h2>
                        @foreach ([
                            'Capaian' => $record->capaian,
                            'Jenis prestasi' => ucfirst($record->jenis_prestasi),
                            'Bidang lomba' => $record->bidang_lomba,
                            'Kegiatan' => $record->kegiatan?->nama_kegiatan,
                            'Lokasi' => $record->kegiatan?->lokasi,
                            'Tanggal mulai' => $record->kegiatan?->tanggal_mulai?->format('d M Y'),
                            'Tanggal selesai' => $record->kegiatan?->tanggal_selesai?->format('d M Y'),
                            'Dibuat' => $record->created_at?->format('d M Y'),
                            'Terakhir diperbarui' => $record->updated_at?->format('d M Y'),
                        ] as $label => $value)
                            @if ($value)
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-[#64758A]">{{ $label }}</span>
                                    <span class="text-base text-[#071E38]">{{ $value }}</span>
                                </div>
                            @endif
                        @endforeach
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold leading-[30px] text-[#071E38]">Prestasi Lainnya</h2>
                        <div class="h-1 bg-gradient-to-r from-[#3979BA] to-[#9CB7D5]"></div>
                        <div class="flex flex-col gap-3 pt-[14px]">
                            @forelse ($terkait as $item)
                                <a href="{{ route('publik.prestasi.show', $item->getKey()) }}" class="flex flex-col gap-1 hover:opacity-80">
                                    <span class="line-clamp-2 text-sm font-bold text-[#071E38]">{{ $item->kegiatan?->nama_kegiatan ?? 'Prestasi' }}</span>
                                    <span class="text-xs text-[#7D8999]">{{ $item->capaian ?: ucfirst($item->jenis_prestasi) }}</span>
                                </a>
                                <div class="h-px bg-[#E6E9EE]"></div>
                            @empty
                                <p class="pt-3 text-sm text-[#7D8999]">Belum ada prestasi lainnya.</p>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </main>

    <x-footer />
</div>
@endsection
