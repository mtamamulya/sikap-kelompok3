@extends('layouts.publik')

@section('title', 'Daftar Prestasi')

@section('content')
<div class="min-h-screen bg-white text-[#071E38]" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <x-navbar-publik />

    <main class="mx-auto max-w-[1728px] px-5 pb-[105px] pt-12 lg:px-12">
        <div class="flex flex-col gap-2">
            <p class="text-base leading-[22px] tracking-[-0.18px] text-[#737C8D]">
                <a href="{{ route('home') }}" class="hover:underline">Beranda</a> / Prestasi
            </p>
            <h1 class="text-4xl font-bold leading-tight tracking-[-0.4px] text-[#071E38] md:text-5xl md:leading-[58px]">
                Prestasi TRK
            </h1>
            <p class="text-sm text-[#7D8999]">Capaian mahasiswa Teknologi Rekayasa Komputer · Politeknik Negeri Semarang</p>
        </div>

        <div class="mt-5 flex flex-col gap-6 lg:flex-row lg:items-start">
            <div class="flex w-full min-w-0 flex-col gap-5 lg:flex-1">
                <div class="flex flex-col">
                    @forelse ($prestasi as $item)
                        <article class="flex flex-col gap-[18px] border-b border-[#E6E9EE] py-5 sm:flex-row sm:items-center">
                            <a href="{{ route('publik.prestasi.show', $item->getKey()) }}"
                               class="flex h-[150px] w-full shrink-0 items-center justify-center rounded-xl bg-[#E4EDFB] text-[#2277FF] sm:h-[170px] sm:w-[240px]"
                               aria-label="Lihat detail {{ $item->kegiatan?->nama_kegiatan ?? 'prestasi' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4Z" />
                                    <path d="M7 6H4v2a4 4 0 0 0 4 4M17 6h3v2a4 4 0 0 1-4 4" />
                                </svg>
                            </a>
                            <div class="flex min-w-0 flex-1 flex-col items-start gap-[5px]">
                                <span class="rounded-full bg-[#CDDEF9] px-3 py-1 text-xs font-semibold text-[#0B3A8C]">
                                    {{ $item->capaian ?: ucfirst($item->jenis_prestasi) }}
                                </span>
                                <h2 class="text-base font-semibold leading-[22px] tracking-[-0.18px]">
                                    <a href="{{ route('publik.prestasi.show', $item->getKey()) }}" class="hover:underline">
                                        {{ $item->kegiatan?->nama_kegiatan ?? 'Prestasi' }}
                                    </a>
                                </h2>
                                <p class="text-xs leading-4 text-[#7D8999]">
                                    {{ ucfirst($item->jenis_prestasi) }}
                                    @if ($item->bidang_lomba)
                                        · {{ $item->bidang_lomba }}
                                    @endif
                                </p>
                                <p class="text-justify text-xs leading-4 text-black">
                                    {{ \Illuminate\Support\Str::limit($item->catatan, 380) ?: 'Lihat detail capaian dan bukti prestasi.' }}
                                </p>
                                @if ($item->created_at)
                                    <p class="text-[11px] leading-4 text-[#7D8999]">{{ $item->created_at->format('d M Y') }}</p>
                                @endif
                                <a href="{{ route('publik.prestasi.show', $item->getKey()) }}"
                                   class="text-xs font-semibold leading-4 text-[#27F] hover:underline">
                                    Lihat detail »
                                </a>
                            </div>
                        </article>
                    @empty
                        <p class="py-10 text-center text-[#7D8999]">Belum ada data prestasi.</p>
                    @endforelse
                </div>
                <x-paginasi :paginator="$prestasi" />
            </div>

            <aside class="flex w-full shrink-0 flex-col gap-6 lg:sticky lg:top-[120px] lg:w-[420px]">
                <section>
                    <h2 class="text-2xl font-bold leading-[30px] tracking-[-0.15px]">Prestasi Terbaru</h2>
                    <div class="h-1 w-full bg-gradient-to-r from-[#3979BA] to-[#9CB7D5]"></div>
                    <div class="flex flex-col gap-3 pt-[14px]">
                        @forelse ($terbaru as $item)
                            <a href="{{ route('publik.prestasi.show', $item->getKey()) }}" class="flex items-center gap-[11px] hover:opacity-80">
                                <div class="flex h-[64px] w-[96px] shrink-0 items-center justify-center rounded-lg bg-[#E4EDFB] text-[#2277FF]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4Z" />
                                        <path d="M7 6H4v2a4 4 0 0 0 4 4M17 6h3v2a4 4 0 0 1-4 4" />
                                    </svg>
                                </div>
                                <div class="flex min-w-0 flex-col gap-[5px]">
                                    <p class="line-clamp-2 text-[15px] font-bold leading-[20px] text-[#071E38]">{{ $item->kegiatan?->nama_kegiatan ?? 'Prestasi' }}</p>
                                    <p class="text-xs leading-4 text-[#7D8999]">
                                        {{ $item->capaian ?: ucfirst($item->jenis_prestasi) }}
                                        @if ($item->bidang_lomba)
                                            · {{ $item->bidang_lomba }}
                                        @endif
                                    </p>
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-[#7D8999]">Belum ada data.</p>
                        @endforelse
                        <div class="h-px w-full bg-[#E6E9EE]"></div>
                    </div>
                </section>
            </aside>
        </div>
    </main>

    <x-footer />
</div>
@endsection
