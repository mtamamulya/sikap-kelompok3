@php
    // Menu versi DOSEN. 'children' = submenu dropdown.
    // href '#' = halaman belum dibuat. Ganti dengan route('nama') kalau sudah ada.
    $menu = [
        [
            'label'  => 'Dashboard',
            'href'   => route('dashboard'),
            'active' => request()->routeIs('dashboard', 'profil'),
            'icon'   => '<rect x="4" y="4" width="7" height="7" rx="2.5" stroke="currentColor" stroke-width="1.5"/><rect x="4" y="14" width="7" height="7" rx="2.5" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="4" width="7" height="7" rx="2.5" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="14" width="7" height="7" rx="2.5" stroke="currentColor" stroke-width="1.5"/>',
        ],
        [
            'label'  => 'Penelitian',
            'href'   => route('penelitian.index'),
            'active' => request()->routeIs('penelitian.*'),
            'icon'   => '<rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor" stroke-width="1.5"/><path d="M8 17L8 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 17L12 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17L16 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
        ],
        [
            'label'  => 'Pengabdian',
            'href'   => route('pengabdian.index'),
            'active' => request()->routeIs('pengabdian.*'),
            'icon'   => '<path d="M13.1494 14.2267L20.5747 11.9991C21.4207 11.7453 22 10.9667 22 10.0835V8.42871C22 7.32414 21.1046 6.42871 20 6.42871H4C2.89543 6.42871 2 7.32414 2 8.42871V10.0835C2 10.9667 2.57934 11.7453 3.42531 11.9991L10.8506 14.2267C11.6004 14.4516 12.3996 14.4516 13.1494 14.2267Z" stroke="currentColor" stroke-width="1.5"/><path d="M12 9.85742L12 11.5717" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21.1663 11.5713V16.9998C21.1663 19.2089 19.3755 20.9998 17.1663 20.9998H6.83301C4.62387 20.9998 2.83301 19.2089 2.83301 16.9998V11.5713" stroke="currentColor" stroke-width="1.5"/><path d="M15.3337 6.42855V5C15.3337 3.89543 14.4382 3 13.3337 3H10.667C9.56242 3 8.66699 3.89543 8.66699 5V6.42855" stroke="currentColor" stroke-width="1.5"/>',
        ],
        [
            'label'  => 'Pengajaran',
            'href'   => route('pengajaran.index'),
            'active' => request()->routeIs('pengajaran.*'),
            'icon'   => '<path d="M12 4.11745V20.9906M12 4.11745L13.1001 3.70461C15.6036 2.76513 18.3964 2.76513 20.8999 3.70461C21.5643 3.95395 22 4.55769 22 5.22907V19.0797C22 20.0016 21.0075 20.632 20.0951 20.2896C18.1082 19.544 15.8918 19.544 13.9049 20.2896L12.0137 20.9993C12.0071 21.0018 12 20.9972 12 20.9906M12 4.11745L10.8999 3.70461C8.39638 2.76513 5.60362 2.76513 3.10014 3.70461C2.43569 3.95395 2 4.55769 2 5.22907V19.0797C2 20.0016 2.99247 20.632 3.90485 20.2896C5.89175 19.544 8.10825 19.544 10.0951 20.2896L11.9863 20.9993C11.9929 21.0018 12 20.9972 12 20.9906" stroke="currentColor" stroke-width="1.5"/>',
        ],
        [
            'label'  => 'Kegiatan',
            'href'   => route('kegiatan.index'),
            'active' => request()->routeIs('kegiatan.*'),
            'icon'   => '<path d="M18.3042 12.8089L17.7543 12.2989L18.3042 12.8089ZM11.2332 5.73783L11.7432 6.28773L11.2332 5.73783ZM21.6616 4.27232L22.4053 4.36958L21.6616 4.27232ZM19.7698 2.38046L19.867 3.12413V3.12413L19.7698 2.38046ZM6.49508 12.6506L7.1685 12.9808L6.49508 12.6506ZM11.3915 17.547L11.0613 16.8736V16.8736L11.3915 17.547ZM8.22801 10.1572C7.93512 9.86432 7.46024 9.86432 7.16735 10.1572C6.87446 10.4501 6.87446 10.925 7.16735 11.2179L7.69768 10.6875L8.22801 10.1572ZM12.8242 16.8747C13.1171 17.1676 13.592 17.1676 13.8849 16.8747C14.1778 16.5818 14.1778 16.107 13.8849 15.8141L13.3545 16.3444L12.8242 16.8747ZM6.46024 18.6425C6.75314 18.3496 6.75314 17.8747 6.46024 17.5818C6.16735 17.2889 5.69248 17.2889 5.39958 17.5818L5.92991 18.1122L6.46024 18.6425ZM2.57116 20.4103C2.27826 20.7032 2.27826 21.178 2.57116 21.4709C2.86405 21.7638 3.33892 21.7638 3.63182 21.4709L3.10149 20.9406L2.57116 20.4103ZM4.33892 16.5212C4.63182 16.2283 4.63182 15.7534 4.33892 15.4605C4.04603 15.1676 3.57116 15.1676 3.27826 15.4605L3.80859 15.9908L4.33892 16.5212ZM1.86405 16.8747C1.57116 17.1676 1.57116 17.6425 1.86405 17.9354C2.15694 18.2283 2.63182 18.2283 2.92471 17.9354L2.39438 17.4051L1.86405 16.8747ZM8.58156 20.7638C8.87445 20.4709 8.87445 19.996 8.58156 19.7032C8.28867 19.4103 7.81379 19.4103 7.5209 19.7032L8.05123 20.2335L8.58156 20.7638ZM6.10669 21.1174C5.8138 21.4103 5.8138 21.8851 6.10669 22.178C6.39958 22.4709 6.87446 22.4709 7.16735 22.178L6.63702 21.6477L6.10669 21.1174ZM10.5968 21.082L11.3393 20.9759V20.9759L10.5968 21.082ZM15.7587 17.6172L16.5012 17.5111V17.5111L15.7587 17.6172ZM2.96004 13.4453L3.06611 12.7028L3.06611 12.7028L2.96004 13.4453ZM6.42487 8.2834L6.3188 9.02586L6.3188 9.02586L6.42487 8.2834ZM9.22063 17.1603L9.75096 16.63L7.4121 14.2911L6.88177 14.8215L6.35144 15.3518L8.6903 17.6906L9.22063 17.1603ZM18.3042 12.8089L17.7543 12.2989C15.8607 14.3406 12.786 16.028 11.0613 16.8736L11.3915 17.547L11.7216 18.2204C13.4618 17.3673 16.7575 15.5795 18.8541 13.3189L18.3042 12.8089ZM6.49508 12.6506L7.1685 12.9808C8.01404 11.256 9.70145 8.18137 11.7432 6.28773L11.2332 5.73783L10.7232 5.18793C8.46255 7.28458 6.67479 10.5802 5.82165 12.3205L6.49508 12.6506ZM21.6616 4.27232L20.9179 4.17506C20.6141 6.49841 19.8068 10.086 17.7543 12.2989L18.3042 12.8089L18.8541 13.3189C21.2496 10.7361 22.0968 6.72859 22.4053 4.36958L21.6616 4.27232ZM11.2332 5.73783L11.7432 6.28773C13.9561 4.23532 17.5437 3.42799 19.867 3.12413L19.7698 2.38046L19.6725 1.6368C17.3135 1.94532 13.306 2.79243 10.7232 5.18793L11.2332 5.73783ZM21.6616 4.27232L22.4053 4.36958C22.6164 2.75571 21.2864 1.42572 19.6725 1.6368L19.7698 2.38046L19.867 3.12413C20.504 3.04082 21.0013 3.5381 20.9179 4.17506L21.6616 4.27232ZM6.88177 14.8215L7.4121 14.2911C7.04892 13.9279 6.96018 13.4057 7.1685 12.9808L6.49508 12.6506L5.82165 12.3205C5.31346 13.3571 5.56365 14.564 6.35144 15.3518L6.88177 14.8215ZM9.22063 17.1603L8.6903 17.6906C9.47809 18.4784 10.685 18.7286 11.7216 18.2204L11.3915 17.547L11.0613 16.8736C10.6364 17.0819 10.1141 16.9932 9.75096 16.63L9.22063 17.1603ZM7.69768 10.6875L7.16735 11.2179L12.8242 16.8747L13.3545 16.3444L13.8849 15.8141L8.22801 10.1572L7.69768 10.6875ZM5.92991 18.1122L5.39958 17.5818L2.57116 20.4103L3.10149 20.9406L3.63182 21.4709L6.46024 18.6425L5.92991 18.1122ZM3.80859 15.9908L3.27826 15.4605L1.86405 16.8747L2.39438 17.4051L2.92471 17.9354L4.33892 16.5212L3.80859 15.9908ZM8.05123 20.2335L7.5209 19.7032L6.10669 21.1174L6.63702 21.6477L7.16735 22.178L8.58156 20.7638L8.05123 20.2335ZM13.7081 7.50556L13.1778 6.97523C12.1038 8.04917 12.1038 9.79037 13.1778 10.8643L13.7081 10.334L14.2384 9.80366C13.7503 9.3155 13.7503 8.52404 14.2384 8.03589L13.7081 7.50556ZM13.7081 10.334L13.1778 10.8643C14.2517 11.9383 15.9929 11.9383 17.0668 10.8643L16.5365 10.334L16.0062 9.80366C15.518 10.2918 14.7266 10.2918 14.2384 9.80366L13.7081 10.334ZM16.5365 10.334L17.0668 10.8643C18.1408 9.79038 18.1408 8.04917 17.0668 6.97523L16.5365 7.50556L16.0062 8.03589C16.4943 8.52404 16.4943 9.3155 16.0062 9.80366L16.5365 10.334ZM16.5365 7.50556L17.0668 6.97523C15.9929 5.90129 14.2517 5.90129 13.1778 6.97523L13.7081 7.50556L14.2384 8.03589C14.7266 7.54773 15.518 7.54773 16.0062 8.03589L16.5365 7.50556ZM15.4759 18.4657L14.9455 17.9354L11.7635 21.1174L12.2939 21.6477L12.8242 22.178L16.0062 18.996L15.4759 18.4657ZM10.5968 21.082L11.3393 20.9759L10.915 18.0061L10.1726 18.1122L9.43009 18.2182L9.85436 21.1881L10.5968 21.082ZM15.4759 15.6373L14.7334 15.7434L15.0162 17.7233L15.7587 17.6172L16.5012 17.5111L16.2183 15.5312L15.4759 15.6373ZM12.2939 21.6477L11.7635 21.1174C11.6179 21.263 11.3684 21.1799 11.3393 20.9759L10.5968 21.082L9.85436 21.1881C10.0583 22.6157 11.8045 23.1977 12.8242 22.178L12.2939 21.6477ZM15.4759 18.4657L16.0062 18.996C16.3957 18.6066 16.5791 18.0564 16.5012 17.5111L15.7587 17.6172L15.0162 17.7233C15.0274 17.8011 15.0012 17.8797 14.9455 17.9354L15.4759 18.4657ZM5.57634 8.56624L5.04601 8.03591L1.86403 11.2179L2.39436 11.7482L2.92469 12.2786L6.10667 9.09657L5.57634 8.56624ZM2.96004 13.4453L2.85398 14.1877L5.82382 14.612L5.92989 13.8695L6.03596 13.1271L3.06611 12.7028L2.96004 13.4453ZM8.40476 8.56624L8.51083 7.82378L6.53093 7.54094L6.42487 8.2834L6.3188 9.02586L8.2987 9.3087L8.40476 8.56624ZM2.39436 11.7482L1.86403 11.2179C0.844323 12.2376 1.42639 13.9838 2.85398 14.1877L2.96004 13.4453L3.06611 12.7028C2.86217 12.6737 2.77902 12.4242 2.92469 12.2786L2.39436 11.7482ZM5.57634 8.56624L6.10667 9.09657C6.16231 9.04093 6.2409 9.01473 6.3188 9.02586L6.42487 8.2834L6.53093 7.54094C5.98564 7.46304 5.4355 7.64642 5.04601 8.03591L5.57634 8.56624Z" fill="currentColor" stroke="none"/>',
        ],
        [
            'label'  => 'Pengajuan',
            'href'   => '#',
            'active' => request()->routeIs('pengajuan.*'),
            'children' => [
                ['label' => 'Dosen',     'href' => route('pengajuan.dosen'),     'active' => request()->routeIs('pengajuan.dosen')],
                ['label' => 'Mahasiswa', 'href' => route('pengajuan.mahasiswa'), 'active' => request()->routeIs('pengajuan.mahasiswa')],
            ],
            'icon'   => '<path d="M13.0815 18.1953L17.4592 21.0095C18.1248 21.4373 19 20.9595 19 20.1683V7C19 4.79086 17.2091 3 15 3H9C6.79086 3 5 4.79086 5 7V20.1683C5 20.9595 5.87525 21.4373 6.54076 21.0095L10.9185 18.1953C11.5773 17.7717 12.4227 17.7717 13.0815 18.1953Z" stroke="currentColor" stroke-width="1.5"/><path d="M9 8.5H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
        ],
    ];

    $linkBase = 'flex h-10 items-center gap-3 rounded-lg px-3 text-[15px]';
@endphp

{{-- Layar lebar: menu tetap di kiri. Layar kecil (di bawah 1024px): menu menjadi laci yang
     meluncur dari kiri, dibuka lewat tombol menu di header ($menu / "menu" ada di layouts/app). --}}
<nav id="menu-utama" aria-label="Menu utama"
     :data-open="menu ? 'true' : 'false'"
     class="flex w-60 shrink-0 flex-col justify-between overflow-y-auto bg-white p-3
            max-lg:invisible max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:z-40 max-lg:w-64 max-lg:-translate-x-full max-lg:shadow-xl max-lg:transition-transform
            max-lg:data-[open=true]:visible max-lg:data-[open=true]:translate-x-0">
    <div class="flex flex-col gap-3">
        {{-- Tombol tutup: hanya tampil di layar kecil --}}
        <button type="button" x-on:click="menu = false" aria-label="Tutup menu"
                class="-mb-1 flex size-10 cursor-pointer items-center justify-center self-end rounded-lg hover:bg-gray-100 lg:hidden">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>
        </button>
        @foreach ($menu as $item)
            @if (! empty($item['children']))
                {{-- Menu dropdown (Pengajuan). Terbuka otomatis kalau salah satu anaknya sedang aktif. --}}
                <details @if ($item['active']) open @endif>
                    <summary class="{{ $linkBase }} cursor-pointer list-none select-none hover:bg-gray-100 [&::-webkit-details-marker]:hidden {{ $item['active'] ? 'font-medium' : '' }}">
                        <svg class="size-[22px] shrink-0 {{ $item['active'] ? 'text-brand' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            {!! $item['icon'] !!}
                        </svg>
                        <span class="flex-1">{{ $item['label'] }}</span>
                        <svg class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </summary>
                    <div class="mt-1 flex flex-col gap-1">
                        @foreach ($item['children'] as $child)
                            <a href="{{ $child['href'] }}"
                               @if ($child['active']) aria-current="page" @endif
                               class="flex h-10 items-center rounded-lg px-3.5 text-[15px] {{ $child['active'] ? 'bg-brand text-white' : 'text-gray-900 hover:bg-gray-100' }}">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </details>
            @else
                <a href="{{ $item['href'] }}"
                   @if ($item['active']) aria-current="page" @endif
                   class="{{ $linkBase }} {{ $item['active'] ? 'bg-brand text-white' : 'text-gray-900 hover:bg-gray-100' }}">
                    <svg class="size-[22px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        {!! $item['icon'] !!}
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </div>

    {{-- Keluar: nanti jadi form POST logout. Sekarang cukup tautan. --}}
    <a href="#" class="{{ $linkBase }} text-gray-900 hover:bg-gray-100">
        <svg class="size-[22px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9 4.5H6.5a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2H9M14 8l4 4-4 4M18 12H9"/>
        </svg>
        Keluar
    </a>
</nav>