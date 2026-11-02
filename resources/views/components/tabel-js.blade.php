{{--
    Skrip bersama untuk tabel daftar (Penelitian, Pengabdian, Pengajaran, Prestasi, Kegiatan).
    Taruh <x-tabel-js /> sekali di halaman daftar. Isinya:
      - quickSort : pengurutan QUICK SORT (versi 3 bagian: lebih kecil / sama / lebih besar dari pivot)
      - tabelData : tambahan untuk komponen Alpine halaman: urut kolom, pagination, jumlah baris per halaman

    Cara pakai di halaman:
        Alpine.data('penelitian', (daftar) => tabelData({
            daftar, cari: '',
            get terfilter() { ... daftar yang sudah dicari / difilter ... },
        }, { perHalaman: 5, kunci: { tahun: (k) => k.tahun } }));
    Tabel lalu memakai: tampil (baris halaman ini), terfilter, urutPer('kolom'), halaman, nomorHalaman, ke(n), dll.
--}}
@once
@push('scripts')
<script>
    // Quick sort. Memilih satu elemen tengah sebagai pivot, lalu membagi sisanya ke tiga kelompok
    // (lebih kecil, sama, lebih besar), kemudian mengurutkan kelompok kecil dan besar secara rekursif.
    // Kelompok "sama" dibiarkan sesuai urutan awal, jadi data bernilai sama tidak saling tertukar.
    window.quickSort = function quickSort(data, banding) {
        if (data.length < 2) return data;
        const pivot = data[Math.floor(data.length / 2)];
        const kecil = [], sama = [], besar = [];
        for (const x of data) {
            const c = banding(x, pivot);
            if (c < 0) kecil.push(x); else if (c > 0) besar.push(x); else sama.push(x);
        }
        return [...quickSort(kecil, banding), ...sama, ...quickSort(besar, banding)];
    };

    // Mengubah "12 September 2026" menjadi angka supaya tanggal bisa diurutkan dengan benar.
    window.tanggalKeAngka = function (teks) {
        const bulan = ['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
        const m = String(teks).trim().toLowerCase().match(/^(\d{1,2})\s+([a-z]+)\s+(\d{4})/);
        if (!m || bulan.indexOf(m[2]) < 0) return 0;
        return Number(m[3]) * 10000 + (bulan.indexOf(m[2]) + 1) * 100 + Number(m[1]);
    };

    // Menempelkan fitur tabel ke komponen halaman. Getter halaman (terfilter) ikut terbawa.
    window.tabelData = function (halaman, opsi = {}) {
        const tambahan = {
            perHalaman: opsi.perHalaman || 5,
            ukuran: [5, 10, 25, 50],
            halaman: 1,
            urutKolom: '',
            urutArah: 'asc',
            kunci: opsi.kunci || {},   // kolom => fungsi yang mengambil nilai untuk diurutkan

            urutPer(kolom) {
                if (this.urutKolom === kolom) {
                    this.urutArah = this.urutArah === 'asc' ? 'desc' : 'asc';
                } else {
                    this.urutKolom = kolom;
                    this.urutArah = 'asc';
                }
                this.halaman = 1;
            },
            ariaUrut(kolom) {
                return this.urutKolom === kolom ? (this.urutArah === 'asc' ? 'ascending' : 'descending') : 'none';
            },
            bandingkan(a, b) {
                if (typeof a === 'number' && typeof b === 'number') return a - b;
                return String(a).localeCompare(String(b), 'id', { numeric: true, sensitivity: 'base' });
            },

            get terurut() {
                if (!this.urutKolom) return this.terfilter;
                const ambil = this.kunci[this.urutKolom] || ((k) => k[this.urutKolom]);
                const arah = this.urutArah === 'asc' ? 1 : -1;
                return quickSort(this.terfilter, (a, b) => arah * this.bandingkan(ambil(a), ambil(b)));
            },
            get totalHalaman() { return Math.max(1, Math.ceil(this.terfilter.length / this.perHalaman)); },
            get halamanAktif() { return Math.min(this.halaman, this.totalHalaman); },
            get tampil() {
                const awal = (this.halamanAktif - 1) * this.perHalaman;
                return this.terurut.slice(awal, awal + this.perHalaman);
            },
            get dari() { return this.terfilter.length ? (this.halamanAktif - 1) * this.perHalaman + 1 : 0; },
            get sampai() { return Math.min(this.halamanAktif * this.perHalaman, this.terfilter.length); },
            get nomorHalaman() {
                const total = this.totalHalaman, now = this.halamanAktif;
                if (total <= 5) return Array.from({ length: total }, (_, i) => i + 1);
                const set = new Set([1, total, now - 1, now, now + 1]);
                const urut = [...set].filter((n) => n >= 1 && n <= total).sort((a, b) => a - b);
                const hasil = [];
                urut.forEach((n, i) => { if (i && n - urut[i - 1] > 1) hasil.push('...'); hasil.push(n); });
                return hasil;
            },
            ke(n) { if (n >= 1 && n <= this.totalHalaman) this.halaman = n; },
        };
        return Object.defineProperties(halaman, Object.getOwnPropertyDescriptors(tambahan));
    };
</script>
@endpush
@endonce