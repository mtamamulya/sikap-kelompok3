<header class="sticky top-0 z-20 flex min-h-[68px] items-center gap-3 border-b border-[#E5E7EB] bg-white px-4 py-3 sm:px-8">
    
    <!-- Tombol Mobile -->
    <button type="button" class="rounded-md border border-[#E5E7EB] bg-white p-2 lg:hidden" x-on:click="sidebar = true" aria-label="Buka menu">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>
    </button>

    <!-- Profil User Pojok Kanan -->
    <div class="relative ml-auto" x-data="{ menu: false }" x-on:click.outside="menu = false">
        <button type="button" x-on:click="menu = !menu" :aria-expanded="menu"
                class="flex items-center gap-3 rounded-lg p-1.5 transition-colors hover:bg-[#F9FAFB]">
            
            <!-- Avatar Profil dengan Auto Sync -->
            <div class="relative flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#DCE7FB] text-sm font-semibold text-[#00235C]">
                <img src="" alt="Avatar" class="global-avatar-sync absolute inset-0 z-10 h-full w-full object-cover" style="display: none;">
                <span>A</span>
            </div>

            <span class="hidden text-left sm:block">
                <span class="block text-sm font-semibold leading-5">Arya Bima</span>
                <span class="block text-xs leading-4 text-[#6B7280]">Mahasiswa</span>
            </span>
            <svg class="size-5 text-[#0A0A0A] transition-transform duration-200" :class="menu ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6"/></svg>
        </button>
        
        <div x-show="menu" x-cloak x-transition 
             class="absolute right-0 mt-2 w-44 overflow-hidden rounded-xl border border-[#E5E7EB] bg-white py-1 shadow-lg">
            <a href="{{ url('/profil') }}" class="block px-4 py-2 text-sm text-[#374151] hover:bg-[#F9FAFB]">Profil saya</a>
            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-[#EF4444] hover:bg-[#FEF2F2]">Keluar</button>
            </form>
        </div>
    </div>
</header>