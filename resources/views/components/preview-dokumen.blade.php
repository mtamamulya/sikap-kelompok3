{{--
    Pratinjau dokumen. Satu kali di layouts/app.blade.php, jadi bisa dipakai semua halaman.

    Cara pakai: tombol mata / "Lihat" pada dokumen mengirim kejadian ke komponen ini:
        <button type="button" x-data
                x-on:click="$dispatch('buka-preview', { file: @js($d['file']), url: @js($d['url']), jenis: @js($d['jenis']) })">
    - file  : nama file (tampil di judul)
    - url   : alamat file. PDF tampil di dalam kotak, gambar tampil sebagai gambar.
              Kalau url kosong atau '#' (belum ada file sungguhan), dipakai file contoh di public/dokumen/contoh.pdf
              (atau contoh.png / contoh.jpg). Kalau file contoh juga tidak ada, tampil lembar contoh.
    - jenis : (opsional) jenis dokumen, tampil kecil di bawah nama file.
--}}
@php
    // Dokumen contoh: taruh satu file bernama contoh.pdf (atau contoh.png / contoh.jpg) di folder public/dokumen/.
    // Selama dokumen di halaman masih data tiruan (alamat '#'), file inilah yang tampil di pratinjau.
    $contohDokumen = null;
    foreach (['pdf', 'png', 'jpg', 'jpeg'] as $ext) {
        if (file_exists(public_path("dokumen/contoh.$ext"))) { $contohDokumen = asset("dokumen/contoh.$ext"); break; }
    }
@endphp
@php
    // CONTOH DOKUMEN. Selama dokumen di data tiruan belum punya file sungguhan (url '#'), pratinjau memakai
    // file contoh dari public/dokumen/ (contoh.pdf, atau contoh.png / contoh.jpg). Kalau tidak ada, tampil lembar contoh.
    $urlContoh = '';
    foreach (['pdf', 'png', 'jpg', 'jpeg'] as $ext) {
        if (file_exists(public_path("dokumen/contoh.$ext"))) { $urlContoh = asset("dokumen/contoh.$ext"); break; }
    }
@endphp
<div x-data="{
        buka: false, file: '', url: '', jenis: '', contoh: @js($urlContoh), contoh: @js($contohDokumen),
        get alamat() { return this.url && this.url !== '#' ? this.url : (this.contoh || ''); },
        get ada() { return this.alamat !== ''; },
        get ekstensi() { return (this.alamat.split('?')[0].split('.').pop() || '').toLowerCase(); },
        get gambar() { return this.ada && ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(this.ekstensi); },
        get pdf() { return this.ada && this.ekstensi === 'pdf'; },
        tampil(e) {
            this.file = e.detail.file || 'Dokumen';
            this.url = (e.detail.url && e.detail.url !== '#') ? e.detail.url : this.contoh;
            this.jenis = e.detail.jenis || '';
            this.buka = true;
            this.$nextTick(() => this.$refs.tutup.focus());
        },
        tutup() { this.buka = false; },
     }"
     x-on:buka-preview.window="tampil($event)"
     x-on:keydown.escape.window="tutup()">

    <div x-show="buka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        {{-- Latar gelap. Klik = tutup. --}}
        <div x-show="buka" x-transition.opacity x-on:click="tutup()" class="absolute inset-0 bg-black/50" aria-hidden="true"></div>

        <div x-show="buka" x-transition role="dialog" aria-modal="true" aria-labelledby="judul-preview"
             class="relative flex h-[88dvh] w-full max-w-4xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">

            {{-- Kepala: nama file + Unduh + Tutup --}}
            <div class="flex shrink-0 items-center justify-between gap-3 bg-linear-to-b from-brand from-0% via-[#1550bd] via-50% to-[#0b2f7a] to-100% px-5 py-3.5 text-white">
                <div class="flex min-w-0 flex-col">
                    <h2 id="judul-preview" class="truncate text-base font-semibold" x-text="file" :title="file"></h2>
                    <p x-show="jenis" class="text-xs text-blue-100" x-text="jenis"></p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <a x-show="ada" :href="alamat" :download="file"
                       class="flex h-9 items-center gap-1.5 rounded-lg bg-white px-3.5 text-sm font-medium text-brand hover:bg-blue-50">
                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11M7.5 10.5L12 15l4.5-4.5M5 19.5h14"/></svg>
                        Unduh
                    </a>
                    <button type="button" x-ref="tutup" x-on:click="tutup()" aria-label="Tutup pratinjau"
                            class="flex size-9 cursor-pointer items-center justify-center rounded-lg text-white hover:bg-white/15">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>
                    </button>
                </div>
            </div>

            {{-- Isi pratinjau --}}
            <div class="min-h-0 flex-1 overflow-y-auto bg-gray-100">
                <template x-if="pdf">
                    <iframe :src="alamat" :title="`Pratinjau ${file}`" class="h-full w-full border-0 bg-white"></iframe>
                </template>

                <template x-if="gambar">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <img :src="alamat" :alt="`Pratinjau ${file}`" class="max-h-full max-w-full rounded-md bg-white object-contain shadow">
                    </div>
                </template>

                {{-- Belum ada file sungguhan: lembar contoh supaya tampilan pratinjau tetap terlihat --}}
                <template x-if="!ada">
                    <div class="flex flex-col items-center gap-4 p-5">
                        <div class="flex w-full max-w-2xl flex-col gap-4 rounded-md bg-white px-10 py-12 shadow" aria-hidden="true">
                            <div class="mx-auto h-3 w-1/2 rounded bg-gray-300"></div>
                            <div class="mx-auto h-2.5 w-1/3 rounded bg-gray-200"></div>
                            <div class="mt-4 flex flex-col gap-2.5">
                                <div class="h-2.5 w-full rounded bg-gray-200"></div><div class="h-2.5 w-11/12 rounded bg-gray-200"></div>
                                <div class="h-2.5 w-full rounded bg-gray-200"></div><div class="h-2.5 w-4/5 rounded bg-gray-200"></div>
                            </div>
                            <div class="mt-3 flex flex-col gap-2.5">
                                <div class="h-2.5 w-full rounded bg-gray-200"></div><div class="h-2.5 w-10/12 rounded bg-gray-200"></div>
                                <div class="h-2.5 w-full rounded bg-gray-200"></div><div class="h-2.5 w-2/3 rounded bg-gray-200"></div>
                            </div>
                            <div class="mt-8 ml-auto flex w-1/3 flex-col items-center gap-2"><div class="h-2.5 w-full rounded bg-gray-200"></div><div class="mt-6 h-2.5 w-3/4 rounded bg-gray-300"></div></div>
                        </div>
                        <p class="max-w-md text-center text-sm text-gray-500">
                            Ini lembar contoh. Isi dokumen yang sebenarnya akan tampil di sini setelah file terhubung ke backend.
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>