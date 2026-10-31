@extends('layouts.mahasiswa')

@section('title', 'Profil mahasiswa')

@section('content')
@php
    $nama     = $mahasiswa->nama     ?? 'Arya Bima';
    $angkatan = $mahasiswa->angkatan ?? '2024';
    $email    = $mahasiswa->email    ?? 'aryabima@mhs.polines.ac.id';
    $nim      = $mahasiswa->nim      ?? '43325115';
    $prodi    = $mahasiswa->prodi    ?? 'Teknologi Rekayasa Komputer';
    $status   = $mahasiswa->status   ?? 'Aktif';

    [$local, $domain] = explode('@', $email, 2);
    $maskedEmail = substr($local, 0, 1).str_repeat('*', max(strlen($local) - 1, 3)).'@'.$domain;
@endphp

<div class="mx-auto flex w-full max-w-7xl flex-col gap-6"
     x-data="profilMahasiswa()"
     x-on:keydown.escape.window="close(); openPhotoModal = false">

    {{-- Tombol Kembali --}}
    <a href="{{ url('/mahasiswa/dashboard') }}"
       class="inline-flex w-fit items-center gap-1.5 text-xs font-semibold text-[#6B7280] transition hover:text-[#111827]">
        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    {{-- Judul & tombol aksi --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-[#111827] sm:text-2xl">Profil mahasiswa</h1>
            <p class="mt-0.5 text-xs text-[#6B7280]">Informasi identitas dan status akademikmu dalam satu tempat.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" x-on:click="open = true; step = 1"
                    class="flex items-center gap-1.5 rounded-xl bg-[#2563EB] px-3.5 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-blue-700">
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Ubah Kata Sandi
            </button>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="flex items-center gap-1.5 rounded-xl bg-[#EF4444] px-3.5 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-red-600">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>

    {{-- Kartu foto profil --}}
    <section class="flex flex-col items-center gap-3 rounded-2xl border border-[#E5E7EB] bg-white p-6 text-center shadow-xs sm:p-8">
        <div class="relative size-20 sm:size-24">
            <div class="flex size-full items-center justify-center overflow-hidden rounded-2xl border border-[#E5E7EB] bg-[#E0E7FF] text-2xl font-bold text-[#2563EB] shadow-xs sm:text-3xl">
                <template x-if="previewPhoto">
                    <img :src="previewPhoto" alt="Foto profil" class="size-full object-cover">
                </template>
                <template x-if="!previewPhoto">
                    <span>{{ strtoupper(substr($nama, 0, 1)) }}</span>
                </template>
            </div>

            <button type="button" aria-label="Ubah foto profil"
                    x-on:click="openPhotoModal = true; resetCropState()"
                    class="absolute -right-1 -bottom-1 flex size-7 items-center justify-center rounded-xl border-2 border-white bg-[#2563EB] text-white shadow-md transition hover:scale-105 hover:bg-blue-700">
                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>

        <div class="flex flex-col items-center gap-0.5">
            <h2 class="text-lg font-bold text-[#111827] sm:text-xl">{{ $nama }}</h2>
            <p class="text-xs text-[#6B7280]">Mahasiswa • Angkatan {{ $angkatan }}</p>
        </div>
    </section>

    {{-- Informasi mahasiswa --}}
    <section class="flex flex-col gap-5 rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-xs sm:p-6">
        <div>
            <h2 class="text-base font-bold text-[#111827] sm:text-lg">Informasi mahasiswa</h2>
            <p class="mt-0.5 text-xs text-[#6B7280]">Data identitas yang terdaftar pada sistem akademik.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-[#F3F4F6] bg-[#F9FAFB] p-3.5">
                <span class="text-xs font-medium text-[#6B7280]">Email</span>
                <p class="mt-1 text-xs font-bold break-all text-[#111827] sm:text-sm">{{ $email }}</p>
                <span class="mt-0.5 block text-[10px] text-[#9CA3AF]">Email mahasiswa</span>
            </div>

            <div class="rounded-xl border border-[#F3F4F6] bg-[#F9FAFB] p-3.5">
                <span class="text-xs font-medium text-[#6B7280]">NIM</span>
                <p class="mt-1 text-xs font-bold text-[#111827] sm:text-sm">{{ $nim }}</p>
                <span class="mt-0.5 block text-[10px] text-[#9CA3AF]">Nomor Induk Mahasiswa</span>
            </div>

            <div class="rounded-xl border border-[#F3F4F6] bg-[#F9FAFB] p-3.5">
                <span class="text-xs font-medium text-[#6B7280]">Prodi (Program Studi)</span>
                <p class="mt-1 text-xs font-bold text-[#111827] sm:text-sm">{{ $prodi }}</p>
                <span class="mt-0.5 block text-[10px] text-[#9CA3AF]">Diploma IV / Sarjana Terapan</span>
            </div>

            <div class="rounded-xl border border-[#F3F4F6] bg-[#F9FAFB] p-3.5">
                <span class="text-xs font-medium text-[#6B7280]">Status akademik</span>
                <div class="mt-1">
                    <span class="rounded-full bg-[#ECFDF5] px-2.5 py-0.5 text-xs font-semibold text-[#047857]">{{ $status }}</span>
                </div>
                <span class="mt-1 block text-[10px] text-[#9CA3AF]">Terdaftar pada semester berjalan</span>
            </div>
        </div>

        <div class="flex items-center gap-2 pt-1 text-xs text-[#6B7280]">
            <svg class="size-4 shrink-0 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Data mahasiswa dikelola oleh bagian administrasi akademik.</span>
        </div>
    </section>

    {{-- ================= MODAL: ATUR FOTO PROFIL ================= --}}
    <div x-show="openPhotoModal" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs"
         x-on:click.self="openPhotoModal = false">
        <div role="dialog" aria-modal="true" class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl sm:p-6">
            <div class="flex items-center justify-between border-b border-[#F3F4F6] pb-3">
                <h3 class="text-base font-bold text-[#111827]">Atur Foto Profil</h3>
                <button type="button" aria-label="Tutup" x-on:click="openPhotoModal = false" class="rounded-lg p-1 text-[#9CA3AF] hover:bg-gray-100">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex flex-col items-center gap-4 py-4">
                {{-- Bingkai canvas kotak --}}
                <div class="relative flex size-60 cursor-grab items-center justify-center overflow-hidden rounded-2xl border border-[#E5E7EB] bg-[#F3F4F6] select-none active:cursor-grabbing sm:size-64"
                     x-on:mousedown="startDrag($event)"
                     x-on:mousemove="onDrag($event)"
                     x-on:mouseup="stopDrag()"
                     x-on:mouseleave="stopDrag()"
                     x-on:touchstart="startDrag($event)"
                     x-on:touchmove="onDrag($event)"
                     x-on:touchend="stopDrag()"
                     x-on:wheel="handleWheel($event)">

                    <canvas x-ref="cropCanvas" width="300" height="300" class="size-full" x-show="imageLoaded"></canvas>

                    <template x-if="!imageLoaded">
                        <div class="flex flex-col items-center gap-2 text-[#9CA3AF]">
                            <svg class="size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-xs font-medium">Pilih foto terlebih dahulu</span>
                        </div>
                    </template>
                </div>

                {{-- Pilih foto & zoom --}}
                <div class="flex w-full items-center justify-between pt-1">
                    <label for="modal-upload-input"
                           class="flex cursor-pointer items-center gap-2 rounded-xl border border-[#2563EB] bg-[#EFF6FF] px-3.5 py-2 text-xs font-semibold text-[#2563EB] transition hover:bg-blue-100">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span x-text="imageLoaded ? 'Ganti Foto' : 'Pilih Foto'"></span>
                        <input type="file" id="modal-upload-input" class="hidden" accept="image/*" x-on:change="handleFileSelect($event)">
                    </label>

                    <div class="flex items-center gap-1.5">
                        <button type="button" aria-label="Perbesar" x-on:click="zoomIn()" :disabled="!imageLoaded"
                                class="flex size-8 items-center justify-center rounded-lg border border-[#E5E7EB] bg-white text-sm font-bold text-[#374151] shadow-2xs transition hover:bg-gray-100 disabled:opacity-40">+</button>
                        <button type="button" aria-label="Perkecil" x-on:click="zoomOut()" :disabled="!imageLoaded"
                                class="flex size-8 items-center justify-center rounded-lg border border-[#E5E7EB] bg-white text-sm font-bold text-[#374151] shadow-2xs transition hover:bg-gray-100 disabled:opacity-40">-</button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-[#F3F4F6] pt-4">
                <button type="button" x-on:click="openPhotoModal = false"
                        class="rounded-xl border border-[#E5E7EB] bg-white px-4 py-2 text-xs font-semibold text-[#4B5563] transition hover:bg-gray-50">
                    Batal
                </button>
                <button type="button" x-on:click="savePhoto()" :disabled="!imageLoaded"
                        class="rounded-xl bg-[#2563EB] px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50">
                    Simpan Foto
                </button>
            </div>
        </div>
    </div>

    {{-- ================= MODAL: UBAH KATA SANDI ================= --}}
    <div x-show="open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-3 backdrop-blur-xs sm:p-4"
         x-on:click.self="close()">
        <div role="dialog" aria-modal="true" class="flex w-full max-w-[800px] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl md:flex-row">

            {{-- Panel kiri: langkah-langkah --}}
            <aside class="flex shrink-0 flex-col justify-between bg-[#F2F6FD] p-6 md:w-[280px]">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-0.5">
                        <div class="text-[10px] font-bold tracking-wider text-[#00235C] uppercase">Keamanan akun</div>
                        <div class="text-lg font-extrabold text-[#0A0A0A]">Ubah Kata Sandi</div>
                    </div>

                    <ol class="flex flex-col gap-6 pt-1">
                        <li class="flex items-start gap-3">
                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold transition-colors"
                                  :class="step === 1 ? 'border-[#2563EB] bg-white text-[#2563EB]' : 'border-[#80CC16] bg-[#80CC16] text-white'">
                                <template x-if="step === 1"><span>01</span></template>
                                <template x-if="step === 2">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </template>
                            </span>
                            <div class="flex flex-col gap-0.5">
                                <div class="text-xs font-bold" :class="step === 1 ? 'text-[#0A0A0A]' : 'text-[#6B7280]'">Kata sandi baru</div>
                                <div class="text-[11px] leading-tight text-[#6B7280]">Buat kata sandi yang kuat</div>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold transition-colors"
                                  :class="step === 2 ? 'border-[#2563EB] bg-white text-[#2563EB]' : 'border-[#A1AEBE] bg-white text-[#6B7280]'">02</span>
                            <div class="flex flex-col gap-0.5">
                                <div class="text-xs font-bold" :class="step === 2 ? 'text-[#0A0A0A]' : 'text-[#9CA3AF]'">Verifikasi OTP</div>
                                <div class="text-[11px] leading-tight" :class="step === 2 ? 'text-[#6B7280]' : 'text-[#9CA3AF]'">Konfirmasi dengan kode dari email Anda.</div>
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="flex items-start gap-2 pt-6 text-[11px] text-[#4B5563] md:pt-0">
                    <svg class="mt-0.5 size-4 shrink-0 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <p class="leading-tight">Kata sandi lama tidak diperlukan. Identitas Anda dipastikan lewat kode OTP.</p>
                </div>
            </aside>

            {{-- Panel kanan --}}
            <div class="flex min-w-0 flex-1 flex-col justify-between p-6">

                {{-- LANGKAH 1: kata sandi baru --}}
                <div x-show="step === 1" class="flex h-full flex-col justify-between gap-6">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-[#0A0A0A]">Buat kata sandi baru</h2>
                            <p class="mt-0.5 text-xs text-[#6B7280]">Gunakan kata sandi yang berbeda dari sebelumnya dan belum pernah dipakai di tempat lain.</p>
                        </div>

                        <div class="flex flex-col gap-3.5">
                            {{-- Kata sandi baru --}}
                            <div class="flex flex-col gap-1">
                                <label for="password" class="text-xs font-semibold text-[#0A0A0A]">Kata sandi baru</label>
                                <div class="flex items-center gap-2 rounded-lg border border-[#E5E7EB] bg-white px-3 py-2 focus-within:border-[#2563EB]">
                                    <input id="password" name="password" x-model="pw" :type="showPw ? 'text' : 'password'"
                                           autocomplete="new-password" placeholder="Masukkan kata sandi baru"
                                           class="w-full bg-transparent text-xs text-[#0A0A0A] outline-none placeholder:text-[#9CA3AF]">
                                    <button type="button" aria-label="Tampilkan atau sembunyikan kata sandi" x-on:click="showPw = !showPw" class="text-[#6B7280]">
                                        <svg x-show="showPw" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="!showPw" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.222-.563c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-2.115 2.115a3 3 0 11-4.243-4.243M3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Indikator kekuatan --}}
                            <div class="flex items-center gap-2">
                                <div class="flex flex-1 items-center gap-1">
                                    <template x-for="i in 4" :key="i">
                                        <div class="h-1 flex-1 rounded-full transition-all" :style="`background:${i <= count ? barColor : '#E5E7EB'}`"></div>
                                    </template>
                                </div>
                                <span class="text-[10px] font-semibold" :style="`color:${count === 0 ? 'transparent' : barColor}`" x-text="strong ? 'Kuat' : 'Lemah'"></span>
                            </div>

                            {{-- Daftar syarat --}}
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <template x-for="r in rules" :key="r.label">
                                    <div class="flex items-center gap-1.5">
                                        <span class="flex size-4 shrink-0 items-center justify-center rounded-full border text-[10px] transition-colors"
                                              :class="r.ok ? 'border-[#80CC16] bg-[#80CC16] text-white' : 'border-[#9CA3AF] bg-white text-transparent'">
                                            <svg class="size-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                        <span class="text-[11px] font-medium" :class="r.ok ? 'text-[#111827]' : 'text-[#6B7280]'" x-text="r.label"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Konfirmasi --}}
                            <div class="flex flex-col gap-1">
                                <label for="password_confirmation" class="text-xs font-semibold text-[#0A0A0A]">Konfirmasi kata sandi baru</label>
                                <div class="flex items-center gap-2 rounded-lg border bg-white px-3 py-2 focus-within:border-[#2563EB]"
                                     :class="confirm.length > 0 && !matches ? 'border-[#EF4444]' : 'border-[#E5E7EB]'">
                                    <input id="password_confirmation" name="password_confirmation" x-model="confirm" :type="showConfirm ? 'text' : 'password'"
                                           autocomplete="new-password" placeholder="Ulangi kata sandi baru"
                                           class="w-full bg-transparent text-xs text-[#0A0A0A] outline-none placeholder:text-[#9CA3AF]">
                                    <button type="button" aria-label="Tampilkan atau sembunyikan konfirmasi" x-on:click="showConfirm = !showConfirm" class="text-[#6B7280]">
                                        <svg x-show="showConfirm" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="!showConfirm" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.222-.563c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-2.115 2.115a3 3 0 11-4.243-4.243M3 3l18 18"/></svg>
                                    </button>
                                </div>
                                <p x-show="confirm.length > 0 && !matches" x-cloak class="text-[11px] font-semibold text-[#EF4444]">Konfirmasi kata sandi tidak cocok.</p>
                            </div>

                            <p class="text-[11px] text-[#9CA3AF]">Kode OTP akan dikirim ke {{ $maskedEmail }}.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" x-on:click="close()"
                                class="rounded-lg bg-[#EF4444] px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-600">
                            Batal
                        </button>
                        <button type="button" x-on:click="goToStep2()" :disabled="!canSubmit"
                                class="rounded-lg bg-[#2563EB] px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                            Kirim Kode OTP
                        </button>
                    </div>
                </div>

                {{-- LANGKAH 2: verifikasi OTP --}}
                <div x-show="step === 2" x-cloak class="flex h-full flex-col justify-between gap-6">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-[#0A0A0A]">Masukkan kode OTP</h2>
                            <p class="mt-0.5 text-xs text-[#6B7280]">Kami mengirimkan kode 6 digit ke {{ $maskedEmail }}. Kode berlaku 5 menit.</p>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-[#0A0A0A]">Kode OTP</span>
                                <span class="text-xs font-bold text-[#4B5563]" x-text="formattedTimer"></span>
                            </div>

                            <div class="flex items-center justify-between gap-2" x-on:paste="onOtpPaste($event)">
                                <template x-for="(digit, idx) in 6" :key="idx">
                                    <input :id="`otp-${idx}`"
                                           type="text"
                                           inputmode="numeric"
                                           pattern="[0-9]*"
                                           maxlength="1"
                                           x-on:input="onOtpInput($event, idx)"
                                           x-on:keydown="onOtpKeydown($event, idx)"
                                           x-on:focus="$event.target.select()"
                                           class="size-10 rounded-xl border border-[#E5E7EB] bg-white text-center text-lg font-bold text-[#111827] outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-blue-100 sm:size-11">
                                </template>
                            </div>

                            <div class="flex items-center justify-start">
                                <button type="button" x-on:click="startTimer()" class="text-xs font-semibold text-[#2563EB] hover:underline">
                                    Kirim ulang kode
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-[#F3F4F6] pt-4">
                        <button type="button" x-on:click="step = 1"
                                class="rounded-lg border border-[#E5E7EB] bg-white px-4 py-2 text-xs font-semibold text-[#4B5563] transition hover:bg-gray-50">
                            Kembali
                        </button>
                        {{-- Sementara dummy. Nanti backend mengganti ini dengan form POST ke route verifikasi OTP. --}}
                        <button type="button" x-on:click="simpanKataSandi()" :disabled="!isOtpComplete"
                                class="rounded-lg bg-[#2563EB] px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                            Simpan Kata Sandi
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function profilMahasiswa() {
        return {
            // --- Modal ---
            open: false,
            step: 1,
            openPhotoModal: false,
            previewPhoto: null,

            // --- Editor foto (canvas) ---
            imageLoaded: false,
            imgObj: null,
            zoom: 1,
            offsetX: 0,
            offsetY: 0,
            isDragging: false,
            startX: 0,
            startY: 0,

            // --- Form kata sandi ---
            pw: '',
            confirm: '',
            showPw: false,
            showConfirm: false,

            // --- OTP ---
            otp: ['', '', '', '', '', ''],
            timer: 300,
            timerInterval: null,

            init() {
                try {
                    this.previewPhoto = localStorage.getItem('user_avatar');
                } catch (e) {
                    this.previewPhoto = null;
                }
            },

            get rules() {
                return [
                    { label: 'Minimal 8 karakter', ok: this.pw.length >= 8 },
                    { label: 'Mengandung huruf', ok: /[A-Za-z]/.test(this.pw) },
                    { label: 'Mengandung angka', ok: /\d/.test(this.pw) },
                    { label: 'Mengandung simbol', ok: /[^A-Za-z0-9]/.test(this.pw) },
                ];
            },
            get count() { return this.rules.filter((r) => r.ok).length; },
            get strong() { return this.count === 4; },
            get barColor() {
                if (this.count === 0) return '#E5E7EB';
                if (this.strong) return '#80CC16';
                return '#FF3F3F';
            },
            get matches() { return this.confirm.length > 0 && this.pw === this.confirm; },
            get canSubmit() { return this.strong && this.matches; },

            get formattedTimer() {
                const m = Math.floor(this.timer / 60).toString().padStart(2, '0');
                const s = (this.timer % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            },
            get isOtpComplete() { return this.otp.every((d) => d !== ''); },

            startTimer() {
                this.timer = 300;
                clearInterval(this.timerInterval);
                this.timerInterval = setInterval(() => {
                    if (this.timer > 0) {
                        this.timer--;
                    } else {
                        clearInterval(this.timerInterval);
                    }
                }, 1000);
            },

            goToStep2() {
                if (!this.canSubmit) return;
                this.step = 2;
                this.startTimer();
                setTimeout(() => {
                    const first = document.getElementById('otp-0');
                    if (first) {
                        first.focus();
                        first.select();
                    }
                }, 150);
            },

            // --- Handler OTP ---
            focusOtp(idx) {
                const el = document.getElementById(`otp-${idx}`);
                if (el) {
                    el.focus();
                    el.select();
                }
            },

            onOtpInput(e, idx) {
                const val = e.target.value.replace(/[^0-9]/g, '');
                if (val.length > 0) {
                    const char = val.slice(-1);
                    this.otp[idx] = char;
                    e.target.value = char;
                    if (idx < 5) this.focusOtp(idx + 1);
                } else {
                    this.otp[idx] = '';
                    e.target.value = '';
                }
            },

            onOtpKeydown(e, idx) {
                if (e.key === 'Backspace') {
                    if (!e.target.value && idx > 0) this.focusOtp(idx - 1);
                } else if (e.key === 'ArrowLeft' && idx > 0) {
                    this.focusOtp(idx - 1);
                } else if (e.key === 'ArrowRight' && idx < 5) {
                    this.focusOtp(idx + 1);
                }
            },

            onOtpPaste(e) {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData)
                    .getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                if (!pasted) return;
                for (let i = 0; i < 6; i++) {
                    const char = pasted[i] || '';
                    this.otp[i] = char;
                    const el = document.getElementById(`otp-${i}`);
                    if (el) el.value = char;
                }
                this.focusOtp(Math.min(pasted.length, 5));
            },

            simpanKataSandi() {
                if (!this.isOtpComplete) return;
                alert('Kata sandi berhasil disimpan!');
                this.close();
            },

            close() {
                this.open = false;
                this.step = 1;
                this.pw = '';
                this.confirm = '';
                this.showPw = false;
                this.showConfirm = false;
                this.otp = ['', '', '', '', '', ''];
                clearInterval(this.timerInterval);
            },

            // --- Editor foto ---
            resetCropState() {
                this.imageLoaded = false;
                this.imgObj = null;
                this.zoom = 1;
                this.offsetX = 0;
                this.offsetY = 0;
                this.isDragging = false;
            },

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        this.imgObj = img;
                        this.zoom = 1;
                        this.offsetX = 0;
                        this.offsetY = 0;
                        this.imageLoaded = true;
                        this.$nextTick(() => this.drawCanvas());
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            zoomIn() {
                if (!this.imageLoaded) return;
                this.zoom = Math.min(this.zoom + 0.15, 3.5);
                this.drawCanvas();
            },

            zoomOut() {
                if (!this.imageLoaded) return;
                this.zoom = Math.max(this.zoom - 0.15, 0.5);
                this.drawCanvas();
            },

            handleWheel(e) {
                if (!this.imageLoaded) return;
                e.preventDefault();
                if (e.deltaY < 0) {
                    this.zoomIn();
                } else {
                    this.zoomOut();
                }
            },

            startDrag(e) {
                if (!this.imageLoaded) return;
                this.isDragging = true;
                const x = e.touches ? e.touches[0].clientX : e.clientX;
                const y = e.touches ? e.touches[0].clientY : e.clientY;
                this.startX = x - this.offsetX;
                this.startY = y - this.offsetY;
            },

            onDrag(e) {
                if (!this.isDragging) return;
                const x = e.touches ? e.touches[0].clientX : e.clientX;
                const y = e.touches ? e.touches[0].clientY : e.clientY;
                this.offsetX = x - this.startX;
                this.offsetY = y - this.startY;
                this.drawCanvas();
            },

            stopDrag() {
                this.isDragging = false;
            },

            drawCanvas() {
                const canvas = this.$refs.cropCanvas;
                if (!canvas || !this.imgObj) return;
                const ctx = canvas.getContext('2d');
                const width = canvas.width;
                const height = canvas.height;

                ctx.clearRect(0, 0, width, height);

                const baseScale = Math.max(width / this.imgObj.width, height / this.imgObj.height);
                const scale = baseScale * this.zoom;
                const w = this.imgObj.width * scale;
                const h = this.imgObj.height * scale;
                const x = (width - w) / 2 + this.offsetX;
                const y = (height - h) / 2 + this.offsetY;

                ctx.drawImage(this.imgObj, x, y, w, h);
            },

            savePhoto() {
                const canvas = this.$refs.cropCanvas;
                if (!canvas || !this.imageLoaded) return;

                const out = document.createElement('canvas');
                out.width = 300;
                out.height = 300;
                out.getContext('2d').drawImage(canvas, 0, 0, 300, 300);

                const dataUrl = out.toDataURL('image/png');
                this.previewPhoto = dataUrl;
                try {
                    localStorage.setItem('user_avatar', dataUrl);
                } catch (e) {
                    // penyimpanan penuh atau diblokir: foto tetap tampil untuk sesi ini
                }
                window.dispatchEvent(new CustomEvent('avatar-updated', { detail: dataUrl }));

                this.openPhotoModal = false;
                this.resetCropState();
            },
        };
    }
</script>
@endsection