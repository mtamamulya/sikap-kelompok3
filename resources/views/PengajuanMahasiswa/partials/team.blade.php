<div class="flex flex-col gap-3">
    <span class="text-[13px] font-semibold leading-5 text-[#374151]">Bentuk partisipasi <span class="text-[#FF3F3F]">*</span></span>
    <div class="flex h-10 items-center gap-16">
        @foreach (['Individu', 'Tim'] as $p)
            <label class="flex cursor-pointer items-center gap-3 text-sm font-medium tracking-[-0.16px]">
                <input type="radio" name="participation" value="{{ $p }}" x-model="participation" class="size-5 accent-[#2277FF]">
                {{ $p }}
            </label>
        @endforeach
    </div>
    <p x-show="participation === 'Individu'" class="text-xs leading-[18px] text-[#6B7280]">Jika memilih Tim, tambahkan nama tim dan anggota pada deskripsi.</p>
</div>

<div x-show="participation === 'Tim'" class="flex flex-col gap-3">
    <input type="hidden" name="members" :value="participation === 'Tim' ? JSON.stringify(members) : ''">
    <span class="text-[13px] font-semibold leading-5 text-[#374151]">{{ $memberLabel }} <span class="text-[#FF3F3F]">*</span></span>
    <div class="flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
            <i class="ti ti-search absolute left-3 top-3 text-base text-[#9CA3AF]"></i>
            <input x-model="newMember" @keydown.enter.prevent="add()" class="{{ $input }} pl-9" placeholder="Cari mahasiswa...">
        </div>
        <div class="relative sm:w-[220px]">
            <select x-model="newRole" class="{{ $select }}">
                <option value="">Pilih peran</option>
                <option>Ketua</option>
                <option>Anggota</option>
            </select>
            <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
        </div>
        <button type="button" @click="add()" class="flex h-10 items-center justify-center gap-1.5 rounded-lg bg-[#2277FF] px-4 text-sm font-medium text-white">
            <i class="ti ti-plus text-base"></i>Tambah
        </button>
    </div>
    <div class="overflow-x-auto rounded-lg border border-[#E5E7EB]">
        <div class="min-w-[420px]">
            <div class="flex gap-4 bg-[#F3F4F6] px-3.5 py-2.5 text-sm font-semibold tracking-[-0.16px] text-[#374151]">
                <div class="flex-1">Nama</div><div class="w-[180px]">Peran</div><div class="w-[50px]">Aksi</div>
            </div>
            <template x-for="(m, i) in members" :key="i">
                <div class="flex items-center gap-4 border-t border-[#E5E7EB] px-3.5 py-2.5 text-sm tracking-[-0.16px]">
                    <div class="flex-1" x-text="m.name"></div>
                    <div class="w-[180px]" x-text="m.role"></div>
                    <div class="w-[50px]">
                        <button type="button" @click="members.splice(i, 1)" class="flex size-8 items-center justify-center rounded-md bg-[#FEE2E2] text-[#EF4444]">
                            <i class="ti ti-trash text-base"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
    @error('members')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
</div>