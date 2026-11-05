<template x-for="advisor in selectedAdvisors" :key="advisor">
    <input type="hidden" name="advisors[]" :value="advisor">
</template>
<div class="flex flex-col gap-3">
    <div class="flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
            <i class="ti ti-search absolute left-3 top-3 text-base text-[#9CA3AF]"></i>
            <input x-model="advisorQuery" autocomplete="off" @keydown.enter.prevent="addAdvisor(advisorMatches[0])" class="{{ $input }} pl-9" placeholder="Cari nama dosen...">
            <div x-show="advisorMatches.length" x-cloak class="absolute z-20 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border border-[#E5E7EB] bg-white shadow-lg">
                <template x-for="dosen in advisorMatches" :key="dosen.nama">
                    <button type="button" @click="addAdvisor(dosen)" class="block w-full px-3 py-2.5 text-left text-[13px] hover:bg-[#F3F4F6]">
                        <span class="block font-medium" x-text="dosen.nama"></span>
                        <span class="block text-xs text-[#6B7280]" x-text="dosen.mata_kuliah.join(', ')"></span>
                    </button>
                </template>
            </div>
        </div>
        <button type="button" @click="addAdvisor(advisorMatches[0])" :disabled="!advisorMatches.length" class="flex h-10 items-center justify-center gap-1.5 rounded-lg bg-[#2277FF] px-4 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-50">
            <i class="ti ti-plus text-base"></i>Tambah
        </button>
    </div>
    <p x-show="advisorQuery && !advisorMatches.length" x-cloak class="text-xs text-[#6B7280]">Nama dosen tidak ditemukan atau sudah ditambahkan. Pilih nama dari daftar pengajar mata kuliah.</p>
    <div x-show="selectedAdvisors.length" x-cloak class="overflow-x-auto rounded-lg border border-[#E5E7EB]">
        <div class="min-w-[320px]">
            <div class="flex gap-4 bg-[#F3F4F6] px-3.5 py-2.5 text-sm font-semibold tracking-[-0.16px] text-[#374151]">
                <div class="flex-1">Nama / Mata Kuliah</div><div class="w-[50px]">Aksi</div>
            </div>
            <template x-for="advisor in selectedAdvisors" :key="advisor">
            <div class="flex items-center gap-4 border-t border-[#E5E7EB] px-3.5 py-2.5 text-sm tracking-[-0.16px]">
                <div class="flex-1">
                    <div x-text="advisor"></div>
                    <div class="text-xs text-[#6B7280]" x-text="advisors.find(dosen => dosen.nama === advisor)?.mata_kuliah.join(', ')"></div>
                </div>
                <div class="w-[50px]">
                    <button type="button" @click="removeAdvisor(advisor)" class="flex size-8 items-center justify-center rounded-md bg-[#FEE2E2] text-[#EF4444]">
                        <i class="ti ti-trash text-base"></i>
                    </button>
                </div>
            </div>
            </template>
        </div>
    </div>
    @if ($errors->has('advisors') || collect($errors->keys())->contains(fn ($key) => str_starts_with($key, 'advisors.')))
        <p class="text-xs text-[#FF3F3F]">{{ $errors->first('advisors') ?: $errors->first(collect($errors->keys())->first(fn ($key) => str_starts_with($key, 'advisors.'))) }}</p>
    @endif
</div>
