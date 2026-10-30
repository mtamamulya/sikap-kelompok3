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
    $initialUploads = collect($item['documents'] ?? [])->map(fn ($f, $i) => [
        'id' => 'old'.$i, 'name' => $f['name'], 'size' => $f['size'], 'progress' => 100,
        'status' => 'done', 'path' => $f['path'], 'error' => '',
    ])->all();
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
    $exts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'zip'];
    $cancelUrl = $item ? route('pengajuan.show', $item['id']) : route('pengajuan.index');
@endphp
<div class="flex flex-col gap-6 px-4 pb-10 pt-7 sm:px-6 lg:px-12"
     x-data="{
         category: @js($v('category', '')),
         participation: @js($v('participation', 'Tim')),
         level: @js($v('level', '')),
         members: @js($members),
         newMember: '', newRole: '',
         docLabels: @js($docLabels),
         locLabels: @js($locLabels),
         uploads: @js($initialUploads),
                  legacy: @js($item && $item['documents'] === null),
         uploadNote: '',
         maxFiles: 5,
         maxSize: 10 * 1024 * 1024,
         allowed: @js($exts),
         token: @js(csrf_token()),
         ok: false,
         submitting: false,
         add() {
             if (!this.newMember.trim()) return;
             this.members.push({ name: this.newMember.trim(), role: this.newRole || 'Anggota' });
             this.newMember = ''; this.newRole = '';
         },
         fmt(b) {
             if (b < 1024) return b + ' B';
             if (b < 1048576) return (b / 1024).toFixed(0) + ' KB';
             return (b / 1048576).toFixed(1).replace('.', ',') + ' MB';
         },
         addFiles(list) {
             this.uploadNote = '';
             Array.from(list).forEach(file => {
                 if (this.uploads.length >= this.maxFiles) { this.uploadNote = 'Maksimal ' + this.maxFiles + ' berkas.'; return; }
                 const ext = file.name.split('.').pop().toLowerCase();
                 let status = 'uploading', error = '';
                 if (!this.allowed.includes(ext)) { status = 'error'; error = 'Format tidak didukung.'; }
                 else if (file.size > this.maxSize) { status = 'error'; error = 'Ukuran berkas maksimal 10 MB.'; }
                 this.uploads.push({ id: Date.now() + Math.random(), name: file.name, size: file.size, progress: 0, status: status, path: '', error: error });
                 const item = this.uploads[this.uploads.length - 1];
                 if (status === 'uploading') this.send(item, file);
             });
         },
         send(item, file) {
             const xhr = new XMLHttpRequest();
             const fd = new FormData();
             fd.append('file', file);
             xhr.open('POST', @js(route('pengajuan.upload')));
             xhr.setRequestHeader('X-CSRF-TOKEN', this.token);
             xhr.setRequestHeader('Accept', 'application/json');
             xhr.upload.onprogress = e => { if (e.lengthComputable) item.progress = Math.round(e.loaded / e.total * 100); };
             xhr.onload = () => {
                 let res = {};
                 try { res = JSON.parse(xhr.responseText); } catch (e) {}
                 if (xhr.status === 200 && res.path) { item.path = res.path; item.progress = 100; item.status = 'done'; }
                 else { item.status = 'error'; item.error = (res.errors && res.errors.file && res.errors.file[0]) || res.message || 'Gagal mengunggah berkas.'; }
             };
             xhr.onerror = () => { item.status = 'error'; item.error = 'Koneksi terputus. Coba lagi.'; };
             xhr.send(fd);
         },
         remove(item) {
             if (item.path) {
                 fetch(@js(route('pengajuan.upload.remove')), {
                     method: 'DELETE',
                     headers: { 'X-CSRF-TOKEN': this.token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                     body: JSON.stringify({ path: item.path })
                 });
             }
             this.uploads = this.uploads.filter(u => u.id !== item.id);
             this.uploadNote = '';
         },
         check() {
             const f = this.$refs.form;
             const v = n => ((f.elements[n] && f.elements[n].value) || '').toString().trim();
             const need = ({ Lomba: ['title', 'organizer', 'level'], Volunteer: ['title', 'organizer', 'role'], Magang: ['role', 'organizer', 'duration_months'], Lainnya: ['title', 'organizer', 'scope', 'role'] })[this.category] || [];
             const hasTeam = this.category === 'Lomba' || this.category === '' || this.category === 'Lainnya';
             let ok = Boolean(this.category) && need.every(n => v(n)) && ['location', 'start_date', 'end_date', 'description'].every(n => v(n));
             if (ok && v('end_date') < v('start_date')) ok = false;
             if (ok && hasTeam && this.participation === 'Tim' && this.members.length < 1) ok = false;
             if (ok && !this.legacy && !this.uploads.some(u => u.status === 'done')) ok = false;             if (ok && this.uploads.some(u => u.status === 'uploading')) ok = false;
             this.ok = ok;
         }
     }"
     x-init="$nextTick(() => check()); $watch('category', () => setTimeout(() => check(), 60)); $watch('participation', () => setTimeout(() => check(), 60)); $watch('members', () => check()); $watch('uploads', () => check())">
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

            <x-pengajuan.field label="Kategori" hint="Pilih kategori kegiatan yang paling sesuai. Pilih Lainnya untuk kegiatan umum." name="category">
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

                    @include('mahasiswa.PengajuanMahasiswa.partials.team', ['input' => $input, 'select' => $select, 'memberLabel' => 'Anggota Lomba'])

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

            {{-- UMUM / LAINNYA --}}
            <template x-if="category === 'Lainnya'">
                <div class="flex flex-col gap-5">
                    <x-pengajuan.field label="Judul Kegiatan" required hint="Tuliskan nama kegiatan secara singkat dan jelas." name="title">
                        <input name="title" value="{{ $v('title') }}" class="{{ $input }}" placeholder="Masukkan judul kegiatan">
                    </x-pengajuan.field>

                    <div class="flex flex-col gap-5 md:flex-row">
                        <x-pengajuan.field class="flex-1" label="Penyelenggara / Instansi" required name="organizer">
                            <input name="organizer" value="{{ $v('organizer') }}" class="{{ $input }}" placeholder="Nama lembaga, organisasi, atau perusahaan">
                        </x-pengajuan.field>
                        <x-pengajuan.field class="flex-1" label="Cakupan Kegiatan" required hint="Pilihan: Kampus, Regional, Nasional, Internasional." name="scope">
                            <div class="relative">
                                <select name="scope" class="{{ $select }}">
                                    <option value="">Pilih...</option>
                                    @foreach ($scopes as $o)<option @selected($v('scope') === $o)>{{ $o }}</option>@endforeach
                                </select>
                                <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                            </div>
                        </x-pengajuan.field>
                    </div>

                    <x-pengajuan.field label="Peran dalam Kegiatan" required hint="Pilihan: Peserta, Panitia, Narasumber, Lainnya." name="role">
                        <div class="relative">
                            <select name="role" class="{{ $select }}">
                                <option value="">Pilih...</option>
                                @foreach ($roles as $o)<option @selected($v('role') === $o)>{{ $o }}</option>@endforeach
                            </select>
                            <i class="ti ti-chevron-down pointer-events-none absolute right-3 top-3 text-base"></i>
                        </div>
                    </x-pengajuan.field>

                    @include('mahasiswa.PengajuanMahasiswa.partials.team', ['input' => $input, 'select' => $select, 'memberLabel' => 'Anggota Tim'])

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
                    <span x-text="locLabels[category || 'Lainnya']"></span> <span class="text-[#FF3F3F]">*</span>
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

            <x-pengajuan.field label="Deskripsi Singkat" required hint="Jelaskan secara singkat tujuan dan manfaat kegiatan." name="description">
                <textarea name="description" maxlength="1000" placeholder="Tuliskan deskripsi kegiatan" class="h-24 w-full resize-none rounded-lg border border-[#E5E7EB] bg-white p-2 text-sm outline-none placeholder:text-[#9CA3AF] focus:border-[#2277FF]">{{ $v('description') }}</textarea>
            </x-pengajuan.field>

            {{-- LAMPIRAN --}}
            <div class="flex flex-col gap-2">
                <span class="text-[13px] font-semibold leading-5 text-[#374151]">Lampiran Dokumen <span class="text-[#FF3F3F]">*</span></span>
                <p class="text-xs leading-[18px] text-[#6B7280]" x-text="docLabels[category || 'Lainnya']"></p>

                <label @dragover.prevent @drop.prevent="addFiles($event.dataTransfer.files)"
                       class="flex cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border border-dashed border-[#CBD5E1] bg-[#F9FAFB] px-3 py-5 text-center transition hover:border-[#2277FF] hover:bg-[#EFF6FF]">
                    <i class="ti ti-cloud-upload text-2xl text-[#2277FF]"></i>
                    <span class="text-[13px] text-[#6B7280]"><span class="font-semibold text-[#2277FF]">Pilih berkas</span> atau tarik dan letakkan di sini</span>
                    <span class="text-xs text-[#6B7280]">PDF, Word, Excel, PowerPoint, JPG, PNG, ZIP • Maks. 10 MB per berkas • Hingga 5 berkas</span>
                    <input type="file" multiple accept=".{{ implode(',.', $exts) }}" class="hidden"
                           @change="addFiles($event.target.files); $event.target.value = ''">
                </label>

                <template x-for="f in uploads.filter(u => u.status === 'done')" :key="f.id">
                    <span class="hidden">
                        <input type="hidden" name="documents[]" :value="f.path">
                        <input type="hidden" name="document_names[]" :value="f.name">
                    </span>
                </template>

                <div class="flex flex-col gap-2">
                    <template x-for="f in uploads" :key="f.id">
                        <div class="flex flex-col gap-2 rounded-lg border p-3"
                             :class="f.status === 'error' ? 'border-[#FCA5A5] bg-[#FEF2F2]' : 'border-[#E5E7EB] bg-white'">
                            <div class="flex items-center gap-3">
                                <i class="ti ti-file-text text-xl text-[#2563EB]"></i>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold" x-text="f.name"></div>
                                    <div class="text-xs"
                                         :class="f.status === 'error' ? 'text-[#B91C1C]' : (f.status === 'done' ? 'text-[#15803D]' : 'text-[#6B7280]')"
                                         x-text="f.status === 'uploading' ? 'Mengunggah... ' + f.progress + '%' : (f.status === 'done' ? 'Berhasil diunggah • ' + fmt(f.size) : f.error)"></div>
                                </div>
                                <i x-show="f.status === 'uploading'" class="ti ti-loader-2 animate-spin text-xl text-[#2563EB]"></i>
                                <i x-show="f.status === 'done'" class="ti ti-circle-check text-xl text-[#16A34A]"></i>
                                <i x-show="f.status === 'error'" class="ti ti-alert-circle text-xl text-[#DC2626]"></i>
                                <button type="button" @click="remove(f)" aria-label="Hapus berkas" class="flex size-8 items-center justify-center rounded-md text-[#6B7280] hover:bg-[#F3F4F6]">
                                    <i class="ti ti-x text-base"></i>
                                </button>
                            </div>
                            <div x-show="f.status === 'uploading'" class="h-1.5 overflow-hidden rounded-full bg-[#E5E7EB]">
                                <div class="h-full rounded-full bg-[#2563EB] transition-all duration-200" :style="'width:' + f.progress + '%'"></div>
                            </div>
                        </div>
                    </template>
                </div>

                <p x-show="uploadNote" x-text="uploadNote" class="text-xs text-[#DC2626]"></p>
                @error('documents')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
                @error('documents.*')<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror
            </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-[#E5E7EB] px-5 py-5 sm:px-7">
            <p class="min-w-[200px] flex-1 text-xs leading-[18px]"
               :class="ok ? 'text-[#6B7280]' : 'text-[#B45309]'"
               x-text="ok ? 'Semua isian wajib sudah lengkap. Periksa kembali data sebelum menyimpan.' : (uploads.some(u => u.status === 'uploading') ? 'Menunggu berkas selesai diunggah...' : 'Lengkapi semua isian wajib (*) dan unggah minimal satu dokumen.')"></p>
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