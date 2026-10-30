{{--
    Modal konfirmasi hapus. Dipakai bersama oleh halaman daftar (Penelitian, Pengabdian, Prestasi, dst.).

    Cara pakai:
    1. Taruh <x-modal-hapus /> sekali di halaman daftar (di dalam @section('content')).
    2. Tombol hapus di tabel mengirim kejadian ke modal ini:
         x-on:click="$dispatch('minta-hapus', { jenis: 'prestasi', nama: @js($p['nama']), action: '#' })"
       - jenis  : kata benda untuk judul, contoh 'prestasi' -> "Hapus prestasi?"
       - nama   : nama data yang akan dihapus (tampil tebal di isi modal)
       - action : alamat hapus. Nanti diisi route('prestasi.destroy', $id) oleh tim backend.
--}}
<div x-data="{
        buka: false, jenis: '', nama: '', action: '#',
        tampil(e) {
            this.jenis = e.detail.jenis; this.nama = e.detail.nama; this.action = e.detail.action || '#';
            this.buka = true;
            this.$nextTick(() => this.$refs.batal.focus());
        },
        tutup() { this.buka = false; },
        // Simulasi: datanya belum benar-benar terhapus. Kalau backend sudah ada, hapus .prevent di form
        // supaya form dikirim ke server.
        hapus() {
            this.buka = false;
            this.$dispatch('tampil-alert', {
                tipe: 'sukses',
                judul: `${this.jenis.charAt(0).toUpperCase() + this.jenis.slice(1)} berhasil dihapus`,
                pesan: `${this.nama} sudah dihapus. (Simulasi: data belum dihapus dari database.)`,
            });
        },
     }"
     x-on:minta-hapus.window="tampil($event)"
     x-on:keydown.escape.window="tutup()">

    {{-- Modal --}}
    <div x-show="buka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        {{-- Latar gelap. Klik = tutup. --}}
        <div x-show="buka" x-transition.opacity x-on:click="tutup()" class="absolute inset-0 bg-black/40" aria-hidden="true"></div>

        <form x-show="buka" x-transition method="POST" :action="action" x-on:submit.prevent="hapus()"
              role="alertdialog" aria-modal="true" aria-labelledby="judul-hapus" aria-describedby="isi-hapus"
              class="relative flex w-full max-w-md flex-col gap-5 rounded-xl bg-white p-6 shadow-xl">
            @csrf
            @method('DELETE')

            <div class="flex items-start gap-4">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-500">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 7h14M10 7V5h4v2M7 7l1 12h8l1-12M10 11v5M14 11v5"/></svg>
                </span>
                <div class="flex min-w-0 flex-col gap-2">
                    <h2 id="judul-hapus" class="text-lg font-semibold" x-text="`Hapus ${jenis}?`"></h2>
                    <p id="isi-hapus" class="text-[15px] text-gray-600">
                        Kamu akan menghapus <span class="font-semibold break-words text-gray-900" x-text="nama"></span>.
                        Data yang sudah dihapus tidak dapat dikembalikan.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-ref="batal" x-on:click="tutup()"
                        class="h-11 cursor-pointer rounded-md border border-gray-300 bg-white px-5 text-base text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit"
                        class="h-11 cursor-pointer rounded-md bg-red-500 px-5 text-base font-medium text-white hover:bg-red-600">Ya, hapus</button>
            </div>
        </form>
    </div>
</div>