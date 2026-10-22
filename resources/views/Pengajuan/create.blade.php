@extends('layouts.app')
@section('title', 'Pengajuan Kegiatan Baru')
@section('content')
@php
    $input = 'h-10 w-full rounded-lg border border-[#E5E7EB] bg-white px-3 text-[13px] outline-none placeholder:text-[#9CA3AF] focus:border-[#2277FF]';
    $select = $input.' appearance-none pr-9';
    $defaultMembers = [['name' => 'Ivan Sigit Santoso', 'role' => 'Anggota'], ['name' => 'Anisa Aulia Nurusyifa', 'role' => 'Anggota']];
    $members = old('members') ? json_decode(old('members'), true) : $defaultMembers;
    $categories = ['Lomba', 'Volunteer', 'Magang', 'Lainnya'];
    $docLabels = [
        'Lomba' => 'Surat Tugas / Bukti Keikutsertaan',
        'Volunteer' => 'Surat Keterangan / Sertifikat Relawan',
        'Magang' => 'Surat Penerimaan (LoA / Acceptance Letter)',
        'Lainnya' => 'Proposal, undangan, surat tugas, atau bukti keikutsertaan yang relevan.',
    ];
    $locLabels = [
        'Lomba' => 'Lokasi Lomba',
        'Volunteer' => 'Lokasi Kegiatan',
        'Magang' => 'Lokasi Magang',
        'Lainnya' => 'Lokasi / Platform Kegiatan',
    ];
    $levels = ['Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];
    $scopes = ['Kampus', 'Regional', 'Nasional', 'Internasional'];
    $roles = ['Peserta', 'Panitia', 'Narasumber', 'Lainnya'];
@endphp
<div class="flex flex-col gap-6 px-4 pb-10 pt-7 sm:px-6 lg:px-12"
     x-data="{
         category: @js(old('category', '')),
         participation: @js(old('participation', 'Tim')),
         level: @js(old('level', '')),
         members: @js($members),
         newMember: '', newRole: '',
         fileName: '',
         docLabels: @js($docLabels),
         locLabels: @js($locLabels),
         add() {
             if (!this.newMember.trim()) return;
             this.members.push({ name: this.newMember.trim(), role: this.newRole || 'Anggota' });
             this.newMember = ''; this.newRole = '';
         }
     }">
    <div class="flex flex-col gap-[18px]">
        <div class="flex flex-wrap gap-2.5 text-[13px]">
            <a href="{{ route('pengajuan.index') }}" class="text-[#6B7280]">Kegiatan</a>
            <span class="text-[#9CA3AF]">/</span>
            <span class="text-[#6B7280]">Pengajuan Baru</span>
            <span class="text-[#9CA3AF]">/</span>
            <span class="font-semibold text-[#2277FF]" x-text="category || 'Umum'"></span>
        </div>
        <div class="flex flex-col gap-2">
            <h1 class="text-[26px] font-bold leading-9">Pengajuan Kegiatan Baru</h1>
            <p class="text-[13px] leading-5 text-[#6B7280]">Lengkapi formulir berikut untuk mengajukan kegiatan baru.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data"
          class="overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
        @csrf
        <div class="flex flex-col gap-5 p-5 sm:p-7">

            <x-field label="Kategori" hint="Pilih kategori kegiatan yang paling sesuai, atau lewati untuk pengajuan umum." name="category">
                <div class="relative">
                    <select name="category" x-model="category" class="{{ $select }}">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                    <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                </div>
                <p class="text-xs leading-[18px] text-[#6B7280]" x-text="category ? 'Kategori terpilih: ' + category + '.' : 'Belum ada kategori dipilih. Isian umum ditampilkan di bawah.'"></p>
            </x-field>

            {{-- LOMBA --}}
            <template x-if="category === 'Lomba'">
                <div class="flex flex-col gap-5">
                    <x-field label="Judul Kegiatan" required hint="Judul harus singkat, jelas, dan mudah dipahami." name="title">
                        <input name="title" value="{{ old('title') }}" class="{{ $input }}" placeholder="Masukkan judul kegiatan...">
                    </x-field>

                    @include('pengajuan.partials.team', ['input' => $input, 'select' => $select, 'memberLabel' => 'Anggota Lomba'])

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
                        <x-field class="flex-1" label="Penyelenggara Lomba" required hint="Masukkan penyelenggara lomba." name="organizer">
                            <input name="organizer" value="{{ old('organizer') }}" class="{{ $input }}" placeholder="Nama lembaga / instansi">
                        </x-field>
                    </div>

                    <x-field label="Link Visual/Website Lomba" optional hint="Cantumkan tautan poster atau situs resmi lomba." name="info_url">
                        <input name="info_url" value="{{ old('info_url') }}" class="{{ $input }}" placeholder="https://...">
                    </x-field>

                    <x-field label="Dosen Pembimbing" optional name="advisor">
                        <input name="advisor" value="{{ old('advisor') }}" class="{{ $input }}" placeholder="Masukkan nama dosen pembimbing">
                    </x-field>
                </div>
            </template>

            {{-- VOLUNTEER --}}
            <template x-if="category === 'Volunteer'">
                <div class="flex flex-col gap-5">
                    <x-field label="Nama Program Relawan" required hint="Tuliskan nama program yang akan Anda ikuti." name="title">
                        <input name="title" value="{{ old('title') }}" class="{{ $input }}" placeholder="Masukkan nama program relawan...">
                    </x-field>
                    <x-field label="Komunitas / Organisasi Penyelenggara" required hint="Gunakan nama resmi penyelenggara program relawan." name="organizer">
                        <input name="organizer" value="{{ old('organizer') }}" class="{{ $input }}" placeholder="Masukkan nama komunitas atau organisasi">
                    </x-field>
                    <x-field label="Peran / Divisi Relawan" required hint="Tuliskan peran Anda dalam pelaksanaan program." name="role">
                        <input name="role" value="{{ old('role') }}" class="{{ $input }}" placeholder="Masukkan peran atau divisi relawan">
                    </x-field>
                </div>
            </template>

            {{-- MAGANG --}}
            <template x-if="category === 'Magang'">
                <div class="flex flex-col gap-5">
                    <x-field label="Posisi / Role Magang" required hint="Tuliskan posisi yang akan dijalani selama magang." name="role">
                        <input name="role" value="{{ old('role') }}" class="{{ $input }}" placeholder="Masukkan posisi atau peran magang...">
                    </x-field>
                    <x-field label="Nama Perusahaan / Instansi" required hint="Gunakan nama resmi perusahaan atau instansi penerima." name="organizer">
                        <input name="organizer" value="{{ old('organizer') }}" class="{{ $input }}" placeholder="Masukkan nama perusahaan / instansi">
                    </x-field>
                    <x-field label="Durasi Magang (Bulan)" required hint="Masukkan durasi dalam bulan." name="duration_months">
                        <input type="number" min="1" max="60" name="duration_months" value="{{ old('duration_months') }}" class="{{ $input }}" placeholder="Contoh: 6">
                    </x-field>
                </div>
            </template>

            {{-- UMUM / LAINNYA (default saat kategori belum dipilih) --}}
            <template x-if="category === '' || category === 'Lainnya'">
                <div class="flex flex-col gap-5">
                    <x-field label="Judul Kegiatan" required hint="Tuliskan nama kegiatan secara singkat dan jelas." name="title">
                        <input name="title" value="{{ old('title') }}" class="{{ $input }}" placeholder="Masukkan judul kegiatan">
                    </x-field>

                    <div class="flex flex-col gap-5 md:flex-row">
                        <x-field class="flex-1" label="Penyelenggara / Instansi" required name="organizer">
                            <input name="organizer" value="{{ old('organizer') }}" class="{{ $input }}" placeholder="Nama lembaga, organisasi, atau perusahaan">
                        </x-field>
                        <x-field class="flex-1" label="Cakupan Kegiatan" required hint="Pilihan: Kampus, Regional, Nasional, Internasional." name="scope">
                            <div class="relative">
                                <select name="scope" class="{{ $select }}">
                                    <option value="">Pilih...</option>
                                    @foreach ($scopes as $o)<option @selected(old('scope') === $o)>{{ $o }}</option>@endforeach
                                </select>
                                <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                            </div>
                        </x-field>
                    </div>

                    <x-field label="Peran dalam Kegiatan" required hint="Pilihan: Peserta, Panitia, Narasumber, Lainnya." name="role">
                        <div class="relative">
                            <select name="role" class="{{ $select }}">
                                <option value="">Pilih...</option>
                                @foreach ($roles as $o)<option @selected(old('role') === $o)>{{ $o }}</option>@endforeach
                            </select>
                            <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                        </div>
                    </x-field>

                    @include('pengajuan.partials.team', ['input' => $input, 'select' => $select, 'memberLabel' => 'Anggota Tim'])

                    <x-field label="Tautan Informasi Kegiatan" optional hint="Tautan situs resmi, poster, atau halaman pendaftaran." name="info_url">
                        <input name="info_url" value="{{ old('info_url') }}" class="{{ $input }}" placeholder="https://...">
                    </x-field>

                    <x-field label="Dosen Pembimbing / Pendamping" optional name="advisor">
                        <input name="advisor" value="{{ old('advisor') }}" class="{{ $input }}" placeholder="Masukkan nama dosen pendamping">
                    </x-field>
                </div>
            </template>

            {{-- FIELD BERSAMA --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[13px] font-semibold leading-5 text-[#374151]">
                    <span x-text="locLabels[category || 'Lainnya']"></span> <span class="text-[#FF3F3F]">*</span>
                </label>
                <input name="location" value="{{ old('location') }}" class="{{ $input }}"
                       x-bind:placeholder="category === 'Magang' ? 'Kota atau alamat penempatan magang' : 'Nama tempat, kota, atau platform daring'">
                <p class="text-xs leading-[18px] text-[#6B7280]">Tuliskan nama tempat, kota, atau platform daring tempat kegiatan berlangsung.</p>
                @error('location')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-5 md:flex-row">
                <x-field class="flex-1" label="Tanggal Mulai" required hint="Pilih tanggal mulai pelaksanaan kegiatan." name="start_date">
                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="{{ $input }}">
                </x-field>
                <x-field class="flex-1" label="Tanggal Selesai" required hint="Pilih tanggal selesai pelaksanaan kegiatan." name="end_date">
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="{{ $input }}">
                </x-field>
            </div>

            <x-field label="Deskripsi Singkat" required hint="Jelaskan secara singkat tujuan dan manfaat kegiatan." name="description">
                <textarea name="description" maxlength="1000" placeholder="Tuliskan deskripsi kegiatan" class="h-24 w-full resize-none rounded-lg border border-[#E5E7EB] bg-white p-2 text-sm outline-none placeholder:text-[#9CA3AF] focus:border-[#2277FF]">{{ old('description') }}</textarea>
            </x-field>

            <div class="flex flex-col gap-1.5">
                <span class="text-[13px] font-semibold leading-5 text-[#374151]">Lampiran Dokumen <span class="text-[#FF3F3F]">*</span></span>
                <p class="text-xs leading-[18px] text-[#6B7280]" x-text="docLabels[category || 'Lainnya']"></p>
                <label class="flex h-[60px] cursor-pointer items-center justify-center gap-2 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] px-3 text-center text-[13px] text-[#6B7280]">
                    <i class="ti ti-download text-base"></i>
                    <span class="truncate" x-text="fileName || 'Unggah dokumen pendukung (PDF, maks. 5 MB)'"></span>
                    <input type="file" name="document" accept="application/pdf" class="hidden" @change="fileName = $event.target.files[0]?.name ?? ''">
                </label>
                <p class="text-xs leading-[18px] text-[#6B7280]">Format PDF dengan ukuran maksimal 5 MB.</p>
                @error('document')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-[#E5E7EB] px-5 py-5 sm:px-7">
            <p class="min-w-[200px] flex-1 text-xs leading-[18px] {{ $errors->any() ? 'text-[#FF3F3F]' : 'text-[#6B7280]' }}">
                {{ $errors->any() ? 'Lengkapi semua isian yang wajib diisi (*).' : 'Periksa kembali data sebelum mengirim pengajuan.' }}
            </p>
            <a href="{{ route('pengajuan.index') }}" class="flex h-10 items-center rounded-lg border border-[#E5E7EB] bg-white px-5 text-[13px] font-semibold text-[#374151]">Batal</a>
            <button type="submit" class="flex h-10 items-center gap-2 rounded-lg bg-[#2563EB] px-5 text-[13px] font-semibold text-white">
                Kirim Pengajuan <i class="ti ti-arrow-right text-xs"></i>
            </button>
        </div>
    </form>
</div>
@endsection