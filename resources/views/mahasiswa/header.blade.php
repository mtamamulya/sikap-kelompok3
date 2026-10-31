<header class="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-[#E5E7EB] bg-white px-6 shadow-xs">
    <!-- Sisi Kiri: Brand Logo jika dibuka tanpa Sidebar -->
    <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 lg:hidden">
            <img src="{{ Vite::asset('resources/images/logo-trk.png') }}" alt="Logo TRK" class="h-8 w-auto shrink-0" onerror="this.onerror=null; this.src='https://api.builder.io/api/v1/image/assets/TEMP/89ec6d5eff0263da1bc98643370f3576302d902a?width=92';">
            <span class="text-xs font-bold leading-tight text-[#1F66E5]">TEKNOLOGI<br>REKAYASA KOMPUTER</span>
        </a>
    </div>

    <!-- Sisi Kanan: Profile Dropdown User -->
    <div class="relative flex items-center gap-4 ml-auto"
         x-data="{ 
            dropdownOpen: false, 
            userAvatar: localStorage.getItem('user_avatar') || null 
         }"
         x-init="
            window.addEventListener('avatar-updated', (e) => userAvatar = e.detail);
            window.addEventListener('storage', () => userAvatar = localStorage.getItem('user_avatar'));
         "
         @click.outside="dropdownOpen = false">
        
        <!-- Tombol Profil Topbar -->
        <button type="button" 
                @click="dropdownOpen = !dropdownOpen" 
                class="flex items-center gap-3 rounded-xl p-1.5 hover:bg-gray-50 transition outline-none cursor-pointer">
            <div class="flex size-9 items-center justify-center rounded-full bg-[#E0E7FF] text-xs font-bold text-[#1F66E5] overflow-hidden border border-[#E5E7EB]">
                <template x-if="userAvatar">
                    <img :src="userAvatar" class="size-full object-cover">
                </template>
                <template x-if="!userAvatar">
                    <span>A</span>
                </template>
            </div>
            <div class="hidden flex-col text-left sm:flex">
                <span class="text-xs font-bold text-[#111827]">Arya Bima</span>
                <span class="text-[10px] text-[#6B7280]">Mahasiswa</span>
            </div>
            <svg class="size-4 text-[#6B7280] transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Menu Dropdown Popover -->
        <div x-show="dropdownOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95 translate-y-[-8px]"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-[-8px]"
             class="absolute right-0 top-12 z-50 w-64 rounded-2xl border border-[#E5E7EB] bg-white p-4 shadow-xl">
            
            <!-- Header User Info -->
            <div class="flex flex-col gap-0.5 pb-3">
                <span class="text-base font-bold text-[#111827]">Arya Bima</span>
                <span class="text-xs text-[#6B7280]">Mahasiswa</span>
            </div>

            <div class="border-t border-[#E5E7EB] my-2"></div>

            <div class="flex flex-col gap-2 pt-1">
                <!-- Item 1: Profil Saya (Background Putih, Berubah Saat Hover) -->
                <a href="{{ url('/profil') }}" 
                   @click="dropdownOpen = false"
                   class="flex items-center gap-3 rounded-xl bg-white px-3.5 py-2.5 text-xs font-medium text-[#111827] hover:bg-[#F3F4F6] transition duration-150">
                    <svg class="size-4 text-[#374151]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil Saya</span>
                </a>

                <!-- Item 2: Keluar (Logout) -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="flex w-full items-center gap-3 rounded-xl bg-white px-3.5 py-2 text-xs font-medium text-[#EF4444] hover:bg-red-50 transition duration-150 cursor-pointer">
                        <svg class="size-4 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>