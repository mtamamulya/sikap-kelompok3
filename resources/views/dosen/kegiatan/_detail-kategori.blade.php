{{--
    Isian tambahan yang berubah menurut kategori. Dipakai create.blade.php dan edit.blade.php.
    Diletakkan DI DALAM grid kartu "Informasi Kegiatan", jadi isian ini tampil menyatu dengan isian lainnya
    (memakai kategori, medan, dan nilai dari komponen Alpine form).
    Daftar isian ada di _medan-kategori.php.
    Memakai x-if (bukan x-show) supaya isian kategori lain tidak ikut divalidasi atau dikirim.
--}}
@php
    $inputClass  = 'h-11 w-full rounded-lg border border-gray-200 bg-white px-3.5 text-[15px] text-gray-900 placeholder:text-gray-400';
    $selectClass = 'h-11 w-full appearance-none rounded-lg border border-gray-200 bg-white pr-10 pl-3.5 text-[15px] text-gray-900';
    $labelClass  = 'text-[15px] font-medium';
    $radioKartu  = 'flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-[15px] has-[:checked]:border-brand has-[:checked]:bg-blue-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand/40';
    $chevron     = '<svg class="pointer-events-none absolute top-1/2 right-3.5 size-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
@endphp

<template x-for="f in medan" :key="kategori + '-' + f.nama">
    <div class="flex flex-col gap-2">
        <label :for="`d-${f.nama}`" class="{{ $labelClass }}" x-text="f.label"></label>

        {{-- Dropdown --}}
        <template x-if="f.tipe === 'pilihan'">
            <div class="relative">
                <select :id="`d-${f.nama}`" :name="`detail[${f.nama}]`" :required="f.wajib"
                        x-on:change="nilai[f.nama] = $event.target.value" class="{{ $selectClass }}">
                    <option value="" :selected="!nilai[f.nama]" x-text="`Pilih ${f.label.toLowerCase()}`"></option>
                    <template x-for="o in f.opsi" :key="o">
                        <option :value="o" :selected="nilai[f.nama] === o" x-text="o"></option>
                    </template>
                </select>
                {!! $chevron !!}
            </div>
        </template>

        {{-- Pilihan kartu (radio) --}}
        <template x-if="f.tipe === 'radio'">
            <div role="radiogroup" :aria-label="f.label" class="flex flex-wrap gap-3">
                <template x-for="o in f.opsi" :key="o">
                    <label class="{{ $radioKartu }}">
                        <input type="radio" :name="`detail[${f.nama}]`" :value="o" :required="f.wajib"
                               :checked="nilai[f.nama] === o" x-on:change="nilai[f.nama] = o"
                               class="size-4 accent-[#1f66e5]">
                        <span x-text="o"></span>
                    </label>
                </template>
            </div>
        </template>

        {{-- Teks --}}
        <template x-if="f.tipe === 'teks'">
            <input :id="`d-${f.nama}`" :name="`detail[${f.nama}]`" type="text" :required="f.wajib"
                   :placeholder="f.placeholder || ''" :value="nilai[f.nama] || ''"
                   x-on:input="nilai[f.nama] = $event.target.value" class="{{ $inputClass }}">
        </template>

        {{-- Angka --}}
        <template x-if="f.tipe === 'angka'">
            <input :id="`d-${f.nama}`" :name="`detail[${f.nama}]`" type="number" min="1" inputmode="numeric" :required="f.wajib"
                   :placeholder="f.placeholder || ''" :value="nilai[f.nama] || ''"
                   x-on:input="nilai[f.nama] = $event.target.value" class="{{ $inputClass }}">
        </template>

        <p x-show="f.catatan" x-cloak x-text="f.catatan" class="text-[13px] text-gray-500"></p>
    </div>
</template>