{{--
    Alert / callout (toast) global. Dipasang SEKALI di layouts/app.blade.php, jadi bisa dipakai dari halaman mana saja.

    Cara memunculkannya dari Alpine (tombol, form, dll.):
        $dispatch('tampil-alert', { tipe: 'sukses', judul: 'Berhasil disimpan', pesan: 'Data prestasi sudah tersimpan.' })

    tipe  : 'sukses' (hijau) | 'galat' (merah) | 'peringatan' (kuning) | 'info' (biru). Kosong = 'info'.
    judul : teks tebal (wajib).
    pesan : keterangan singkat (boleh dikosongkan).

    Dari JavaScript biasa:
        window.dispatchEvent(new CustomEvent('tampil-alert', { detail: { tipe: 'sukses', judul: 'Selesai' } }))

    Untuk backend (Laravel): setelah proses selesai dan redirect, kirim pesan lewat session,
    alert akan tampil sendiri saat halaman terbuka:
        return redirect()->route('prestasi.index')->with('sukses', 'Prestasi berhasil disimpan.');
    Kunci yang dibaca: sukses, galat, peringatan, info.
--}}
@php
    $dariSession = [];
    foreach (['sukses' => 'Berhasil', 'galat' => 'Terjadi kesalahan', 'peringatan' => 'Perhatian', 'info' => 'Informasi'] as $tipe => $judul) {
        if (session()->has($tipe)) {
            $dariSession[] = ['tipe' => $tipe, 'judul' => $judul, 'pesan' => session($tipe)];
        }
    }
@endphp

<div x-data="{
        daftar: [], nomor: 0,
        // warna kotak + teks, warna judul (lebih gelap), dan isi ikon di dalam lingkaran (i atau !)
        gaya: {
            sukses:     { kotak: 'bg-[#edf4ec] text-[#3a7a4b]', judul: 'text-[#25593a]', jalur: 'M12 11v5M12 7.5v.01' },
            galat:      { kotak: 'bg-[#fbe9e8] text-[#b5413d]', judul: 'text-[#8f2a27]', jalur: 'M12 7.5v5M12 16v.01' },
            peringatan: { kotak: 'bg-[#fdf5e0] text-[#946a12]', judul: 'text-[#6f4e0a]', jalur: 'M12 7.5v5M12 16v.01' },
            info:       { kotak: 'bg-[#eaf1fe] text-[#2f66c8]', judul: 'text-[#1f4fa8]', jalur: 'M12 11v5M12 7.5v.01' },
        },
        tambah(d) {
            const id = ++this.nomor;
            const tipe = this.gaya[d.tipe] ? d.tipe : 'info';
            this.daftar.push({ id, tipe, judul: d.judul || '', pesan: d.pesan || '' });
            // hilang sendiri setelah 5 detik (galat 8 detik)
            setTimeout(() => this.tutup(id), tipe === 'galat' ? 8000 : 5000);
        },
        tutup(id) { this.daftar = this.daftar.filter(a => a.id !== id); },
        init() { @js($dariSession).forEach(d => this.tambah(d)); },
     }"
     x-on:tampil-alert.window="tambah($event.detail)"
     class="pointer-events-none fixed top-20 left-1/2 z-[60] flex w-[calc(100%-2rem)] max-w-xl -translate-x-1/2 flex-col gap-3">

    {{-- Efek masuk "pop": jatuh dari atas, membesar sedikit, memantul 2 kali, lalu diam.
         Gerakannya ada di @keyframes alert-pop (paling bawah file ini). Mau lebih kuat / lembut? Ubah angka di sana. --}}
    <template x-for="a in daftar" :key="a.id">
        <div style="animation: alert-pop 0.75s ease-out both"
             x-transition:leave="transition duration-300 ease-in" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-6 opacity-0"
             :role="a.tipe === 'galat' || a.tipe === 'peringatan' ? 'alert' : 'status'"
             :class="gaya[a.tipe].kotak"
             class="pointer-events-auto flex items-center gap-3 rounded-lg px-4 py-3.5 text-[15px] shadow-sm">
            <svg class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/><path :d="gaya[a.tipe].jalur"/>
            </svg>
            <p class="min-w-0 flex-1 break-words">
                <span :class="gaya[a.tipe].judul" class="font-semibold" x-text="a.judul"></span>
                <span x-show="a.pesan" x-text="a.pesan"></span>
            </p>
            <button type="button" x-on:click="tutup(a.id)" aria-label="Tutup pemberitahuan"
                    class="-m-1 flex size-7 shrink-0 cursor-pointer items-center justify-center rounded-md opacity-60 hover:bg-black/5 hover:opacity-100">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    </template>
</div>

<style>
    @keyframes alert-pop {
        0%   { transform: translateY(-180%) scale(0.85); opacity: 0; }
        40%  { transform: translateY(18%)   scale(1.07); opacity: 1; }
        58%  { transform: translateY(-9%)   scale(0.98); }
        74%  { transform: translateY(4%)    scale(1.02); }
        88%  { transform: translateY(-1%)   scale(0.995); }
        100% { transform: translateY(0)     scale(1); opacity: 1; }
    }
    /* Pengguna yang mematikan animasi di perangkatnya: alert langsung tampil tanpa gerak */
    @media (prefers-reduced-motion: reduce) {
        [style*="alert-pop"] { animation: none !important; }
    }
</style>