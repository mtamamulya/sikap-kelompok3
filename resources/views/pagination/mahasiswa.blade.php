@if ($paginator->hasPages())
    @php
        $nav = 'flex size-9 items-center justify-center rounded-md text-base font-medium';
    @endphp
    <nav role="navigation" aria-label="Navigasi halaman" class="flex justify-center">
        <ul class="flex flex-wrap items-center justify-center gap-2">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="{{ $nav }} cursor-not-allowed bg-[#E5E7EB] text-[#A8A8A8]" aria-disabled="true" aria-label="Sebelumnya">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6"/></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya" class="{{ $nav }} bg-[#27F] text-white">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6"/></svg>
                    </a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="{{ $nav }} border border-[#E5E7EB] text-[#0A0A0A]">…</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $nav }} border border-[#27F] text-[#0162DD]">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="Halaman {{ $page }}" class="{{ $nav }} border border-[#E5E7EB] text-[#0A0A0A] hover:border-[#27F]">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya" class="{{ $nav }} border border-[#27F] bg-[#DCE7FB] text-[#0162DD]">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6l-6 6"/></svg>
                    </a>
                @else
                    <span class="{{ $nav }} cursor-not-allowed bg-[#E5E7EB] text-[#A8A8A8]" aria-disabled="true" aria-label="Berikutnya">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6l-6 6"/></svg>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif