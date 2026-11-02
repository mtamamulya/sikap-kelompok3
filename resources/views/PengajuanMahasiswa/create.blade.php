@extends('layouts.pengajuan-app')
@section('title', $item ? 'Edit Pengajuan' : 'Pengajuan Kegiatan Baru')
@section('content')
@php
    $d = $item['data'] ?? [];
    $v = fn ($k, $def = '') => old($k, $d[$k] ?? $def);
    $input = 'h-10 w-full rounded-lg border border-[#E5E7EB] bg-white px-3 text-[13px] outline-none placeholder:text-[#9CA3AF] focus:border-[#2277FF]';
    $select = $input.' appearance-none pr-9';
    $defaultMembers = [['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota'], ['name' => 'Anisa Aulia Nurusyifa', 'role' => 'Anggota']];
    $members = old('members') ? json_decode(old('members'), true) : ($d['members'] ?? $defaultMembers);
    $initialDocuments = old('documents', collect($item['documents'] ?? [])->map(fn ($f) => [
        'name' => $f['name'] ?? '',
        'type' => $f['type'] ?? 'Dokumentasi',
        'url' => $f['url'] ?? (isset($f['path']) ? asset('storage/'.$f['path']) : ''),
    ])->all());
    $categories = ['Lomba', 'Volunteer', 'Magang', 'Pengabdian'];
    $documentTypes = ['Dokumentasi', 'Surat Mitra', 'Surat Mahasiswa', 'Surat Pengabdian'];
    $locLabels = [
        'Lomba' => 'Lokasi Lomba',
        'Volunteer' => 'Lokasi Kegiatan',
        'Magang' => 'Lokasi Magang',
        'Pengabdian' => 'Lokasi Pengabdian',
    ];
    $levels = ['Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];
    $cancelUrl = $item ? route('pengajuan.show', $item['id']) : route('pengajuan.index');
@endphp
<div class="flex flex-col gap-6 px-4 pb-10 pt-7 sm:px-6 lg:px-12"
     x-data="{
         category: @js($v('category', '')),
         participation: @js($v('participation', 'Tim')),
         level: @js($v('level', '')),
         members: @js($members),
         newMember: '', newRole: '',
         locLabels: @js($locLabels),
         documents: @js($initialDocuments),
         documentTypes: @js($documentTypes),
         documentNote: '',
         ok: false,
         submitting: false,
         add() {
             if (!this.newMember.trim()) return;
             this.members.push({ name: this.newMember.trim(), role: this.newRole || 'Anggota' });
             this.newMember = ''; this.newRole = '';
         },
         addDocument() {
             this.documents.push({ id: Date.now() + Math.random(), name: '', type: '', url: '' });
             this.documentNote = '';
         },
         removeDocument(document) {
             this.documents = this.documents.filter(item => item !== document);
             this.documentNote = '';
         },
         check() {
             const f = this.$refs.form;
             const v = n => ((f.elements[n] && f.elements[n].value) || '').toString().trim();
             const need = ({ Lomba: ['title', 'organizer', 'level'], Volunteer: ['title', 'organizer', 'role'], Magang: ['role', 'organizer', 'duration_months'], Pengabdian: ['title', 'organizer'] })[this.category] || [];
             const hasTeam = this.category === 'Lomba';
             let ok = Boolean(this.category) && need.every(n => v(n)) && ['location', 'start_date', 'end_date'].every(n => v(n));
             if (ok && v('end_date') < v('start_date')) ok = false;
             if (ok && hasTeam && this.participation === 'Tim' && this.members.length < 1) ok = false;
             if (ok && this.documents.some(doc => !doc.name.trim() || !doc.type || !doc.url.trim())) ok = false;
             this.ok = ok;
         }
     }"
     x-init="$nextTick(() => check()); $watch('category', () => setTimeout(() => check(), 60)); $watch('participation', () => setTimeout(() => check(), 60)); $watch('members', () => check()); $watch('documents', () => check(), { deep: true })">
    <div class="flex flex-col gap-[18px]">
        <div class="flex flex-wrap gap-2.5 text-[13px]">
            <a href="{{ route('pengajuan.index') }}" class="text-[#6B7280]">Kegiatan</a>
            <span class="text-[#9CA3AF]">/</span>
            <span class="text-[#6B7280]">{{ $item ? 'Edit Pengajuan' : 'Pengajuan Baru' }}</span>
            <span class="text-[#9CA3AF]">/</span>
            <span class="font-semibold text-[#2277FF]" x-text="category || 'Umum'"></span>
        </div>
        <div class="flex flex-col gap-2">
            <h1 class="text-[26px] font-bold leading-9">{{ $item ? 'Edit Pengajuan' : 'Pengajuan Kegiatan Baru' }}</h1>
            <p class="text-[13px] leading-5 text-[#6B7280]">{{ $item ? 'Perbarui isian pengajuan Anda, lalu simpan perubahan.' : 'Lengkapi formulir berikut untuk mengajukan kegiatan baru.' }}</p>
        </div>
    </div>

    <form method="POST" action="{{ $item ? route('pengajuan.update', $item['id']) : route('pengajuan.store') }}" x-ref="form"
          @input="check()" @change="check()"
          @submit="if (!ok) { $event.preventDefault(); } else { submitting = true; }"
          class="overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
        @csrf
        @if ($item) @method('PUT') @endif
        <div class="flex flex-col gap-5 p-5 sm:p-7">

            <x-pengajuan.field label="Kategori" hint="Pilih kategori kegiatan yang paling sesuai." name="category">
                <div class="relative">
                    <select name="category" x-model="category" class="{{ $select }}">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                    <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                </div>
                <p class="text-xs leading-[18px] text-[#6B7280]" x-text="category ? 'Kategori terpilih: ' + category + '.' : 'Pilih kategori untuk menampilkan isian pengajuan.'"></p>
            </x-pengajuan.field>

            <div x-show="category !== ''" x-cloak class="flex flex-col gap-5">
            {{-- LOMBA --}}
            <template x-if="category === 'Lomba'">
                <div class="flex flex-col gap-5">
                    <x-pengajuan.field label="Judul Kegiatan" required hint="Judul harus singkat, jelas, dan mudah dipahami." name="title">
                        <input name="title" value="{{ $v('title') }}" class="{{ $input }}" placeholder="Masukkan judul kegiatan...">
                    </x-pengajuan.field>

                    @include('PengajuanMahasiswa.partials.team', ['input' => $input, 'select' => $select, 'memberLabel' => 'Anggota Lomba'])

                    <div class="flex flex-col gap-5 md:flex-row">
                        <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                            <label class="text-[13px] font-semibold leading-5 text-[#374151]">
                                <span x-text="participation === 'Tim' ? 'Tingkat Kejuaraan' : 'Tingkat Lomba'"></span> <span class="text-[#FF3F3F]">*</span>
                            </label>
                            <div class="relative">
                                <select name="level" x-model="level" class="{{ $select }}">
                                    <option value="">Pilih...</option>
                                    @foreach ($levels as $l)<option>{{ $l }}</option>@endforeach
                                </select>
                                <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                            </div>
                            <p class="text-xs leading-[18px] text-[#6B7280]" x-text="level ? 'Tingkat terpilih: ' + level + '.' : 'Pilihan: Kabupaten, Provinsi, Nasional, Internasional.'"></p>
                            @error('level')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
                        </div>
                        <x-pengajuan.field class="flex-1" label="Penyelenggara Lomba" required hint="Masukkan penyelenggara lomba." name="organizer">
                            <input name="organizer" value="{{ $v('organizer') }}" class="{{ $input }}" placeholder="Nama lembaga / instansi">
                        </x-pengajuan.field>
                    </div>

                    <x-pengajuan.field label="Link Visual/Website Lomba" optional hint="Cantumkan tautan poster atau situs resmi lomba." name="info_url">
                        <input name="info_url" value="{{ $v('info_url') }}" class="{{ $input }}" placeholder="https://...">
                    </x-pengajuan.field>

                    <x-pengajuan.field label="Dosen Pembimbing" optional name="advisor">
                        <input name="advisor" value="{{ $v('advisor') }}" class="{{ $input }}" placeholder="Masukkan nama dosen pembimbing">
                    </x-pengajuan.field>
                </div>
            </template>

            {{-- VOLUNTEER --}}
            <template x-if="category === 'Volunteer'">
                <div class="flex flex-col gap-5">
                    <x-pengajuan.field label="Nama Program Relawan" required hint="Tuliskan nama program yang akan Anda ikuti." name="title">
                        <input name="title" value="{{ $v('title') }}" class="{{ $input }}" placeholder="Masukkan nama program relawan...">
                    </x-pengajuan.field>
                    <x-pengajuan.field label="Komunitas / Organisasi Penyelenggara" required hint="Gunakan nama resmi penyelenggara program relawan." name="organizer">
                        <input name="organizer" value="{{ $v('organizer') }}" class="{{ $input }}" placeholder="Masukkan nama komunitas atau organisasi">
                    </x-pengajuan.field>
                    <x-pengajuan.field label="Peran / Divisi Relawan" required hint="Tuliskan peran Anda dalam pelaksanaan program." name="role">
                        <input name="role" value="{{ $v('role') }}" class="{{ $input }}" placeholder="Masukkan peran atau divisi relawan">
                    </x-pengajuan.field>
                </div>
            </template>

            {{-- MAGANG --}}
            <template x-if="category === 'Magang'">
                <div class="flex flex-col gap-5">
                    <x-pengajuan.field label="Posisi / Role Magang" required hint="Tuliskan posisi yang akan dijalani selama magang." name="role">
                        <input name="role" value="{{ $v('role') }}" class="{{ $input }}" placeholder="Masukkan posisi atau peran magang...">
                    </x-pengajuan.field>
                    <x-pengajuan.field label="Nama Perusahaan / Instansi" required hint="Gunakan nama resmi perusahaan atau instansi penerima." name="organizer">
                        <input name="organizer" value="{{ $v('organizer') }}" class="{{ $input }}" placeholder="Masukkan nama perusahaan / instansi">
                    </x-pengajuan.field>
                    <x-pengajuan.field label="Durasi Magang (Bulan)" required hint="Masukkan durasi dalam bulan." name="duration_months">
                        <input type="number" min="1" max="60" name="duration_months" value="{{ $v('duration_months') }}" class="{{ $input }}" placeholder="Contoh: 6">
                    </x-pengajuan.field>
                </div>
            </template>

            {{-- PENGABDIAN --}}
            <template x-if="category === 'Pengabdian'">
                <div class="flex flex-col gap-5">
                    <x-pengajuan.field label="Nama / Judul Kegiatan" required hint="Tuliskan nama atau judul kegiatan secara singkat dan jelas." name="title">
                        <input name="title" value="{{ $v('title') }}" class="{{ $input }}" placeholder="Masukkan nama atau judul kegiatan">
                    </x-pengajuan.field>

                    <x-pengajuan.field label="Penyelenggara / Instansi / Sumber" required hint="Masukkan instansi penyelenggara atau sumber informasi kegiatan." name="organizer">
                        <input name="organizer" value="{{ $v('organizer') }}" class="{{ $input }}" placeholder="Nama lembaga, instansi, atau sumber">
                    </x-pengajuan.field>

                    <x-pengajuan.field label="Tautan Informasi Kegiatan" optional hint="Tautan situs resmi, poster, atau halaman pendaftaran." name="info_url">
                        <input name="info_url" value="{{ $v('info_url') }}" class="{{ $input }}" placeholder="https://...">
                    </x-pengajuan.field>

                    <x-pengajuan.field label="Dosen Pembimbing / Pendamping" optional name="advisor">
                        <input name="advisor" value="{{ $v('advisor') }}" class="{{ $input }}" placeholder="Masukkan nama dosen pendamping">
                    </x-pengajuan.field>
                </div>
            </template>

            {{-- FIELD BERSAMA --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] font-semibold leading-5 text-[#374151]">
                    <span x-text="locLabels[category] || 'Lokasi Kegiatan'"></span> <span class="text-[#FF3F3F]">*</span>
                </label>
                <input name="location" value="{{ $v('location') }}" class="{{ $input }}"
                       x-bind:placeholder="category === 'Magang' ? 'Kota atau alamat penempatan magang' : 'Nama tempat, kota, atau platform daring'">
                <p class="text-xs leading-[18px] text-[#6B7280]">Tuliskan nama tempat, kota, atau platform daring tempat kegiatan berlangsung.</p>
                @error('location')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-5 md:flex-row">
                <x-pengajuan.field class="flex-1" label="Tanggal Mulai" required hint="Pilih tanggal mulai pelaksanaan kegiatan." name="start_date">
                    <input type="date" name="start_date" value="{{ $v('start_date') }}" class="{{ $input }}">
                </x-pengajuan.field>
                <x-pengajuan.field class="flex-1" label="Tanggal Selesai" required hint="Pilih tanggal selesai pelaksanaan kegiatan." name="end_date">
                    <input type="date" name="end_date" value="{{ $v('end_date') }}" class="{{ $input }}">
                </x-pengajuan.field>
            </div>

            {{-- TAUTAN DOKUMEN --}}
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <span class="text-[13px] font-semibold leading-5 text-[#374151]">Dokumen pendukung <span class="font-normal text-[#6B7280]">(opsional)</span></span>
                    <p class="text-xs leading-[18px] text-[#6B7280]">Tambahkan tautan dokumen jika diperlukan. Tidak ada batas jumlah dokumen.</p>
                </div>
                <template x-for="(document, index) in documents" :key="document.id || index">
                    <div class="flex flex-col gap-4 rounded-lg border border-[#E5E7EB] p-4">
                        <div class="flex items-center justify-between gap-3 rounded-md bg-[#082C66] px-3 py-2 text-sm font-semibold text-white">
                            <span x-text="'Dokumen ' + (index + 1)"></span>
                            <button type="button" @click="removeDocument(document)" aria-label="Hapus dokumen" class="flex size-7 items-center justify-center rounded text-white/80 hover:bg-white/10 hover:text-white">
                                <i class="ti ti-x text-base"></i>
                            </button>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">
                                <span>Nama dokumen <span class="text-[#FF3F3F]">*</span></span>
                                <input type="text" x-model="document.name" :name="'documents[' + index + '][name]'" maxlength="255" required
                                       class="{{ $input }}" placeholder="Contoh: Proposal pengabdian">
                                @error('documents.*.name')<span class="text-xs text-[#FF3F3F]">{{ $message }}</span>@enderror
                            </label>
                            <label class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">
                                <span>Jenis dokumen <span class="text-[#FF3F3F]">*</span></span>
                                <div class="relative">
                                    <select x-model="document.type" :name="'documents[' + index + '][type]'" required class="{{ $select }}">
                                        <option value="">Pilih jenis dokumen</option>
                                        <template x-for="type in documentTypes" :key="type">
                                            <option :value="type" x-text="type"></option>
                                        </template>
                                    </select>
                                    <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                                </div>
                                @error('documents.*.type')<span class="text-xs text-[#FF3F3F]">{{ $message }}</span>@enderror
                            </label>
                        </div>
                        <label class="flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]">
                            <span>Tautan dokumen <span class="text-[#FF3F3F]">*</span></span>
                            <input type="url" x-model="document.url" :name="'documents[' + index + '][url]'" maxlength="500" required
                                   class="{{ $input }}" placeholder="https://...">
                            @error('documents.*.url')<span class="text-xs text-[#FF3F3F]">{{ $message }}</span>@enderror
                        </label>
                    </div>
                </template>
                <button type="button" @click="addDocument()" class="flex h-10 w-fit items-center gap-2 rounded-lg border border-[#2277FF] px-3 text-sm font-semibold text-[#2277FF] hover:bg-[#EFF6FF]">
                    <i class="ti ti-plus text-base"></i>Tambah dokumen
                </button>
                <p x-show="documentNote" x-text="documentNote" class="text-xs text-[#DC2626]"></p>
                @error('documents')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
                @error('documents.*')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
            </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-[#E5E7EB] px-5 py-5 sm:px-7">
            <p class="min-w-[200px] flex-1 text-xs leading-[18px]"
               :class="ok ? 'text-[#6B7280]' : 'text-[#B45309]'"
               x-text="ok ? 'Semua isian wajib sudah lengkap. Periksa kembali data sebelum menyimpan.' : 'Lengkapi semua isian wajib (*) sebelum menyimpan.'"></p>
            <a href="{{ $cancelUrl }}" class="flex h-10 items-center rounded-lg border border-[#E5E7EB] bg-white px-5 text-[13px] font-semibold text-[#374151]">Batal</a>
            <button type="submit" :disabled="!ok || submitting"
                    class="flex h-10 items-center gap-2 rounded-lg bg-[#2563EB] px-5 text-[13px] font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-50">
                <span x-text="submitting ? 'Menyimpan...' : '{{ $item ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}'"></span>
                <i class="ti text-xs" :class="submitting ? 'ti-loader-2 animate-spin' : 'ti-arrow-right'"></i>
            </button>
        </div>
    </form>
</div>
@endsection