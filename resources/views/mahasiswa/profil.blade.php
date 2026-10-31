<<<<<<< HEAD:resources/views/mahasiswa/profil.blade.php
@extends('layouts.mahasiswa')

@section('title', 'Profil mahasiswa')

@section('content')
=======
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil mahasiswa - Teknologi Rekayasa Komputer</title>
    
    <!-- Vite / Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F9FAFB] min-h-screen text-[#111827] antialiased">

>>>>>>> 98ba7d0 (fix: update navbar, header, layouts, views, dan web routes):resources/views/profil.blade.php
@php
    $nama     = $mahasiswa->nama     ?? 'Arya Bima';
    $angkatan = $mahasiswa->angkatan ?? '2024';
    $email    = $mahasiswa->email    ?? 'aryabima@mhs.polines.ac.id';
    $nim      = $mahasiswa->nim      ?? '43325115';
    $prodi    = $mahasiswa->prodi    ?? 'Teknologi Rekayasa Komputer';
    $status   = $mahasiswa->status   ?? 'Aktif';

    [$local, $domain] = explode('@', $email, 2);
    $maskedEmail = substr($local, 0, 1) . str_repeat('*', max(strlen($local) - 1, 3)) . '@' . $domain;
@endphp

<<<<<<< HEAD:resources/views/mahasiswa/profil.blade.php
<div class="flex flex-col gap-6"
=======
<div class="min-h-screen p-4 sm:p-6 md:p-10 lg:px-16 max-w-7xl mx-auto"
>>>>>>> 98ba7d0 (fix: update navbar, header, layouts, views, dan web routes):resources/views/profil.blade.php
     x-data="{
        open: false,
        step: 1,
        openPhotoModal: false,
        previewPhoto: localStorage.getItem('user_avatar') || null,
        
        // --- State Editor Foto Native Canvas (Kotak Full) ---
        imageLoaded: false,
        imgObj: null,
        zoom: 1,
        offsetX: 0,
        offsetY: 0,
        isDragging: false,
        startX: 0,
        startY: 0,
        
        // Form Kata Sandi
        pw: '',
        confirm: '',
        showPw: false,
        showConfirm: false,

        // OTP State
        otp: ['', '', '', '', '', ''],
        timer: 300,
        timerInterval: null,

        get rules() {
            return [
                { label: 'Minimal 8 karakter', ok: this.pw.length >= 8 },
                { label: 'Mengandung huruf',   ok: /[A-Za-z]/.test(this.pw) },
                { label: 'Mengandung angka',   ok: /\d/.test(this.pw) },
                { label: 'Mengandung simbol',  ok: /[^A-Za-z0-9]/.test(this.pw) },
            ];
        },
        get count() { return this.rules.filter(r => r.ok).length },
        get strong() { return this.count === 4 },
        get barColor() { 
            if (this.count === 0) return '#E5E7EB';
            if (this.strong) return '#80CC16';
            return '#FF3F3F';
        },
        get matches() { return this.confirm.length > 0 && this.pw === this.confirm },
        get canSubmit() { return this.strong && this.matches },
        
        get formattedTimer() {
            const m = Math.floor(this.timer / 60).toString().padStart(2, '0');
            const s = (this.timer % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        get isOtpComplete() {
            return this.otp.every(digit => digit !== '');
        },

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
            if (this.canSubmit) {
                this.step = 2;
                this.startTimer();
                setTimeout(() => {
                    const firstInput = document.getElementById('otp-0');
                    if (firstInput) {
                        firstInput.focus();
                        firstInput.select();
                    }
                }, 150);
            }
        },

        // --- Handler OTP ---
        onOtpInput(e, idx) {
            const val = e.target.value.replace(/[^0-9]/g, '');
            if (val.length > 0) {
                const char = val.slice(-1);
                this.otp[idx] = char;
                e.target.value = char;
                
                if (idx < 5) {
                    const nextInput = document.getElementById(`otp-${idx + 1}`);
                    if (nextInput) {
                        nextInput.focus();
                        nextInput.select();
                    }
                }
            } else {
                this.otp[idx] = '';
            }
        },

        onOtpKeydown(e, idx) {
            if (e.key === 'Backspace') {
                if (!e.target.value && idx > 0) {
                    const prevInput = document.getElementById(`otp-${idx - 1}`);
                    if (prevInput) {
                        prevInput.focus();
                        prevInput.select();
                    }
                }
            } else if (e.key === 'ArrowLeft' && idx > 0) {
                const prevInput = document.getElementById(`otp-${idx - 1}`);
                if (prevInput) prevInput.focus();
            } else if (e.key === 'ArrowRight' && idx < 5) {
                const nextInput = document.getElementById(`otp-${idx + 1}`);
                if (nextInput) nextInput.focus();
            }
        },

        onOtpPaste(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            if (pastedData) {
                for (let i = 0; i < 6; i++) {
                    const char = pastedData[i] || '';
                    this.otp[i] = char;
                    const el = document.getElementById(`otp-${i}`);
                    if (el) el.value = char;
                }
                const focusIdx = Math.min(pastedData.length, 5);
                const targetInput = document.getElementById(`otp-${focusIdx}`);
                if (targetInput) targetInput.focus();
            }
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

        // --- MANAJEMEN CROP FOTO KOTAK FULL ---
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
            if (file) {
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
            }
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
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            this.startX = clientX - this.offsetX;
            this.startY = clientY - this.offsetY;
        },

        onDrag(e) {
            if (!this.isDragging) return;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            this.offsetX = clientX - this.startX;
            this.offsetY = clientY - this.startY;
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

            ctx.save();
            const baseScale = Math.max(width / this.imgObj.width, height / this.imgObj.height);
            const scale = baseScale * this.zoom;
            const w = this.imgObj.width * scale;
            const h = this.imgObj.height * scale;

            const x = (width - w) / 2 + this.offsetX;
            const y = (height - h) / 2 + this.offsetY;

            ctx.drawImage(this.imgObj, x, y, w, h);
            ctx.restore();
        },

        savePhoto() {
            const canvas = this.$refs.cropCanvas;
            if (canvas && this.imageLoaded) {
                const outCanvas = document.createElement('canvas');
                outCanvas.width = 300;
                outCanvas.height = 300;
                const outCtx = outCanvas.getContext('2d');

                outCtx.drawImage(canvas, 0, 0, 300, 300);

                const croppedDataUrl = outCanvas.toDataURL('image/png');
                this.previewPhoto = croppedDataUrl;
                localStorage.setItem('user_avatar', croppedDataUrl);
                window.dispatchEvent(new CustomEvent('avatar-updated', { detail: croppedDataUrl }));
                
                this.openPhotoModal = false;
                this.resetCropState();
            }
        }
     }"
     x-on:keydown.escape.window="close(); openPhotoModal = false;">

<<<<<<< HEAD:resources/views/mahasiswa/profil.blade.php
    <div class="flex flex-col gap-6">
        {{-- Title row --}}
        <div class="flex flex-col gap-3">
            <a href="{{ url()->previous() }}" class="flex w-fit items-center gap-2.5 text-sm tracking-[-0.16px] text-[#6B7280]">
                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6"/></svg>
                Kembali
            </a>
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex min-w-0 flex-1 basis-72 flex-col gap-2">
                    <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px] text-[#111827]">Profil mahasiswa</h1>
                    <p class="text-sm leading-5 text-[#6B7280]">Informasi identitas dan status akademikmu dalam satu tempat.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="open = true"
                            class="flex items-center gap-1 rounded-md bg-[#27F] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l10.5-10.5a2.828 2.828 0 1 0-4-4L4 16v4"/><path d="M13.5 6.5l4 4"/></svg>
                        Ubah Kata Sandi
=======
    {{-- Tombol Kembali --}}
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#6B7280] hover:text-[#111827] transition">
            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali
        </a>
    </div>

    <main class="w-full">
        {{-- Baris Judul & Tombol Aksi --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#111827]">Profil mahasiswa</h1>
                <p class="text-xs text-[#6B7280] mt-0.5">Informasi identitas dan status akademikmu dalam satu tempat.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="open = true; step = 1" 
                        class="flex items-center gap-1.5 rounded-xl bg-[#2563EB] px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition shadow-xs">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Ubah Kata Sandi
                </button>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="flex items-center gap-1.5 rounded-xl bg-[#EF4444] px-3.5 py-2 text-xs font-semibold text-white hover:bg-red-600 transition shadow-xs">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar
>>>>>>> 98ba7d0 (fix: update navbar, header, layouts, views, dan web routes):resources/views/profil.blade.php
                    </button>
                </form>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            {{-- Card Avatar Foto Profil (Tampilan Kotak Full) --}}
            <section class="flex flex-col items-center gap-3 rounded-2xl border border-[#E5E7EB] bg-white p-6 sm:p-8 shadow-xs text-center">
                <div class="relative size-20 sm:size-24">
                    <div class="flex size-full items-center justify-center rounded-2xl bg-[#E0E7FF] text-2xl sm:text-3xl font-bold text-[#2563EB] overflow-hidden border border-[#E5E7EB] shadow-xs">
                        <template x-if="previewPhoto">
                            <img :src="previewPhoto" class="size-full object-cover">
                        </template>
                        <template x-if="!previewPhoto">
                            <span x-text="'{{ substr($nama, 0, 1) }}'"></span>
                        </template>
                    </div>

                    <button type="button" 
                            @click="openPhotoModal = true; resetCropState();" 
                            class="absolute -bottom-1 -right-1 flex size-7 items-center justify-center rounded-xl bg-[#2563EB] text-white shadow-md border-2 border-white hover:bg-blue-700 hover:scale-105 transition">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col items-center gap-0.5">
                    <h2 class="text-lg sm:text-xl font-bold text-[#111827]">{{ $nama }}</h2>
                    <p class="text-xs text-[#6B7280]">Mahasiswa • Angkatan {{ $angkatan }}</p>
                </div>
            </section>

            {{-- Informasi Mahasiswa --}}
            <section class="flex flex-col gap-5 rounded-2xl border border-[#E5E7EB] bg-white p-5 sm:p-6 shadow-xs">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#111827]">Informasi mahasiswa</h2>
                    <p class="text-xs text-[#6B7280] mt-0.5">Data identitas yang terdaftar pada sistem akademik.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-[#F9FAFB] p-3.5 border border-[#F3F4F6]">
                        <span class="text-xs text-[#6B7280] font-medium">Email</span>
                        <p class="text-xs sm:text-sm font-bold text-[#111827] mt-1 break-all">{{ $email }}</p>
                        <span class="text-[10px] text-[#9CA3AF] mt-0.5 block">Email mahasiswa</span>
                    </div>

                    <div class="rounded-xl bg-[#F9FAFB] p-3.5 border border-[#F3F4F6]">
                        <span class="text-xs text-[#6B7280] font-medium">NIM</span>
                        <p class="text-xs sm:text-sm font-bold text-[#111827] mt-1">{{ $nim }}</p>
                        <span class="text-[10px] text-[#9CA3AF] mt-0.5 block">Nomor Induk Mahasiswa</span>
                    </div>

                    <div class="rounded-xl bg-[#F9FAFB] p-3.5 border border-[#F3F4F6]">
                        <span class="text-xs text-[#6B7280] font-medium">Prodi (Program Studi)</span>
                        <p class="text-xs sm:text-sm font-bold text-[#111827] mt-1">{{ $prodi }}</p>
                        <span class="text-[10px] text-[#9CA3AF] mt-0.5 block">Diploma IV / Sarjana Terapan</span>
                    </div>

                    <div class="rounded-xl bg-[#F9FAFB] p-3.5 border border-[#F3F4F6]">
                        <span class="text-xs text-[#6B7280] font-medium">Status akademik</span>
                        <div class="mt-1">
                            <span class="rounded-full bg-[#ECFDF5] px-2.5 py-0.5 text-xs font-semibold text-[#047857]">
                                {{ $status }}
                            </span>
                        </div>
                        <span class="text-[10px] text-[#9CA3AF] mt-1 block">Terdaftar pada semester berjalan</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs text-[#6B7280] pt-1">
                    <svg class="size-4 text-[#2563EB] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Data mahasiswa dikelola oleh bagian administrasi akademik.</span>
                </div>
            </section>
        </div>
    </div>

<<<<<<< HEAD:resources/views/mahasiswa/profil.blade.php
    {{-- Dialog Ubah Kata Sandi --}}
    <div x-show="open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4"
         x-on:click.self="close()">
        @if (Route::has('password.otp.send'))
            <form method="POST" action="{{ route('password.otp.send') }}" role="dialog" aria-modal="true"
                  class="flex w-full max-w-[872px] flex-col overflow-hidden rounded-lg bg-white shadow-[-1px_2px_5px_rgba(0,0,0,0.1),-4px_9px_10px_rgba(0,0,0,0.09),-10px_20px_13px_rgba(0,0,0,0.05)] md:flex-row">
                @csrf
        @else
            <div role="dialog" aria-modal="true"
                 class="flex w-full max-w-[872px] flex-col overflow-hidden rounded-lg bg-white shadow-[-1px_2px_5px_rgba(0,0,0,0.1),-4px_9px_10px_rgba(0,0,0,0.09),-10px_20px_13px_rgba(0,0,0,0.05)] md:flex-row">
        @endif
=======
    {{-- Popup Modal Atur Foto Profil --}}
    <div x-show="openPhotoModal" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs">
        <div @click.outside="openPhotoModal = false" class="w-full max-w-md rounded-2xl bg-white p-5 sm:p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-[#F3F4F6] pb-3">
                <h3 class="text-base font-bold text-[#111827]">Atur Foto Profil</h3>
                <button type="button" @click="openPhotoModal = false" class="rounded-lg p-1 text-[#9CA3AF] hover:bg-gray-100">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
>>>>>>> 98ba7d0 (fix: update navbar, header, layouts, views, dan web routes):resources/views/profil.blade.php

            <div class="flex flex-col items-center gap-4 py-4">
                <!-- Frame Canvas Kotak Full -->
                <div class="relative size-60 sm:size-64 rounded-2xl bg-[#F3F4F6] border border-[#E5E7EB] overflow-hidden flex items-center justify-center cursor-grab active:cursor-grabbing select-none"
                     @mousedown="startDrag($event)"
                     @mousemove="onDrag($event)"
                     @mouseup="stopDrag()"
                     @mouseleave="stopDrag()"
                     @touchstart="startDrag($event)"
                     @touchmove="onDrag($event)"
                     @touchend="stopDrag()"
                     @wheel="handleWheel($event)">
                    
                    <canvas x-ref="cropCanvas" width="300" height="300" class="size-full" x-show="imageLoaded"></canvas>

                    <template x-if="!imageLoaded">
                        <div class="flex flex-col items-center gap-2 text-[#9CA3AF]">
                            <svg class="size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-medium">Pilih foto terlebih dahulu</span>
                        </div>
                    </template>
                </div>

                <!-- Tombol Kontrol: Ganti Foto & Zoom (+ / -) -->
                <div class="flex items-center justify-between w-full pt-1">
                    <label for="modal-upload-input" class="flex cursor-pointer items-center gap-2 rounded-xl border border-[#2563EB] bg-[#EFF6FF] px-3.5 py-2 text-xs font-semibold text-[#2563EB] hover:bg-blue-100 transition">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span x-text="imageLoaded ? 'Ganti Foto' : 'Pilih Foto'"></span>
                        <input type="file" id="modal-upload-input" class="hidden" accept="image/*" @change="handleFileSelect($event)">
                    </label>

                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="zoomIn()" :disabled="!imageLoaded" 
                                class="flex size-8 items-center justify-center rounded-lg border border-[#E5E7EB] bg-white text-[#374151] hover:bg-gray-100 disabled:opacity-40 transition font-bold text-sm shadow-2xs">
                            +
                        </button>
                        <button type="button" @click="zoomOut()" :disabled="!imageLoaded" 
                                class="flex size-8 items-center justify-center rounded-lg border border-[#E5E7EB] bg-white text-[#374151] hover:bg-gray-100 disabled:opacity-40 transition font-bold text-sm shadow-2xs">
                            -
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-[#F3F4F6] pt-4">
                <button type="button" @click="openPhotoModal = false" class="rounded-xl border border-[#E5E7EB] bg-white px-4 py-2 text-xs font-semibold text-[#4B5563] hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" @click="savePhoto()" :disabled="!imageLoaded" class="rounded-xl bg-[#2563EB] px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition">
                    Simpan Foto
                </button>
            </div>
        </div>
    </div>

    {{-- Dialog Modal Ubah Kata Sandi --}}
    <div x-show="open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-3 sm:p-4 backdrop-blur-xs"
         x-on:click.self="close()">
        <div class="flex w-full max-w-[800px] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl md:flex-row">
            
            {{-- Panel Kiri Sidebar Modal --}}
            <aside class="flex shrink-0 flex-col justify-between bg-[#F2F6FD] p-6 md:w-[280px]">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-0.5">
                        <div class="text-[10px] font-bold tracking-wider text-[#00235C] uppercase">KEAMANAN AKUN</div>
                        <div class="text-lg font-extrabold text-[#0A0A0A]">Ubah Kata Sandi</div>
                    </div>
                    
                    <ol class="flex flex-col gap-6 pt-1">
                        <!-- Step 1 -->
                        <li class="flex items-start gap-3">
                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold transition-colors"
                                  :class="step === 1 ? 'border-[#2563EB] bg-white text-[#2563EB]' : 'border-[#80CC16] bg-[#80CC16] text-white'">
                                <template x-if="step === 1"><span>01</span></template>
                                <template x-if="step === 2">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </template>
                            </span>
                            <div class="flex flex-col gap-0.5">
                                <div class="text-xs font-bold" :class="step === 1 ? 'text-[#0A0A0A]' : 'text-[#6B7280]'">Kata sandi baru</div>
                                <div class="text-[11px] text-[#6B7280] leading-tight">Buat kata sandi yang kuat</div>
                            </div>
                        </li>

                        <!-- Step 2 -->
                        <li class="flex items-start gap-3">
                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold transition-colors"
                                  :class="step === 2 ? 'border-[#2563EB] bg-white text-[#2563EB]' : 'border-[#A1AEBE] bg-white text-[#6B7280]'">
                                02
                            </span>
                            <div class="flex flex-col gap-0.5">
                                <div class="text-xs font-bold" :class="step === 2 ? 'text-[#0A0A0A]' : 'text-[#9CA3AF]'">Verifikasi OTP</div>
                                <div class="text-[11px] leading-tight" :class="step === 2 ? 'text-[#6B7280]' : 'text-[#9CA3AF]'">Konfirmasi dengan kode dari email Anda.</div>
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="flex items-start gap-2 text-[11px] text-[#4B5563] pt-6 md:pt-0">
                    <svg class="size-4 shrink-0 text-[#2563EB] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <p class="leading-tight">Kata sandi lama tidak diperlukan. Identitas Anda dipastikan lewat kode OTP.</p>
                </div>
            </aside>

            {{-- Panel Kanan Form --}}
            <div class="flex min-w-0 flex-1 flex-col justify-between p-6">
                
                {{-- STEP 1: Buat Kata Sandi Baru --}}
                <div x-show="step === 1" class="flex flex-col justify-between h-full gap-6">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-[#0A0A0A]">Buat kata sandi baru</h2>
                            <p class="text-xs text-[#6B7280] mt-0.5">Gunakan kata sandi yang berbeda dari sebelumnya dan belum pernah dipakai di tempat lain.</p>
                        </div>

<<<<<<< HEAD:resources/views/mahasiswa/profil.blade.php
                        @if (Route::has('password.otp.send'))
                            <p class="text-sm font-medium leading-5 tracking-[-0.16px] text-[#A8A8A8]">
                                Kode OTP akan dikirim ke {{ $maskedEmail }}.
                            </p>
                        @else
                            <p class="text-sm font-medium leading-5 text-[#B45309]">
                                Pengiriman OTP belum tersedia karena route pengiriman belum dikonfigurasi.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2">
                    <button type="button" x-on:click="close()"
                            class="rounded-md bg-[#FF3F3F] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">
                        Batal
                    </button>
                    @if (Route::has('password.otp.send'))
                        <button type="submit" :disabled="!canSubmit"
                                class="rounded-md bg-[#27F] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)] disabled:cursor-not-allowed disabled:opacity-50">
                            Kirim Kode OTP
                        </button>
                    @else
                        <button type="button" disabled
                                class="rounded-md bg-[#27F] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] opacity-50">
                            OTP belum tersedia
                        </button>
                    @endif
=======
                        <div class="flex flex-col gap-3.5">
                            <!-- Input Kata Sandi Baru -->
                            <div class="flex flex-col gap-1">
                                <label for="password" class="text-xs font-semibold text-[#0A0A0A]">Kata sandi baru</label>
                                <div class="flex items-center gap-2 rounded-lg border border-[#E5E7EB] bg-white px-3 py-2 focus-within:border-[#2563EB]">
                                    <input id="password" name="password" x-model="pw" :type="showPw ? 'text' : 'password'"
                                           autocomplete="new-password" placeholder="Masukkan kata sandi baru"
                                           class="w-full bg-transparent text-xs text-[#0A0A0A] outline-none placeholder:text-[#9CA3AF]">
                                    <button type="button" @click="showPw = !showPw" class="text-[#6B7280]">
                                        <svg x-show="showPw" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg x-show="!showPw" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.222-.563c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-2.115 2.115a3 3 0 11-4.243-4.243M3 3l18 18"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Indicator Bar -->
                            <div class="flex items-center gap-2">
                                <div class="flex flex-1 items-center gap-1">
                                    <template x-for="i in 4" :key="i">
                                        <div class="h-1 flex-1 rounded-full transition-all" :style="`background:${i <= count ? barColor : '#E5E7EB'}`"></div>
                                    </template>
                                </div>
                                <span class="text-[10px] font-semibold" :style="`color:${count === 0 ? 'transparent' : barColor}`" x-text="strong ? 'Kuat' : 'Lemah'"></span>
                            </div>

                            <!-- Requirement List -->
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <template x-for="r in rules" :key="r.label">
                                    <div class="flex items-center gap-1.5">
                                        <span class="flex size-4 shrink-0 items-center justify-center rounded-full border text-[10px] transition-colors"
                                              :class="r.ok ? 'border-[#80CC16] bg-[#80CC16] text-white' : 'border-[#9CA3AF] bg-white text-transparent'">
                                            <svg class="size-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </span>
                                        <span class="text-[11px] font-medium" :class="r.ok ? 'text-[#111827]' : 'text-[#6B7280]'" x-text="r.label"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Input Konfirmasi Kata Sandi -->
                            <div class="flex flex-col gap-1">
                                <label for="password_confirmation" class="text-xs font-semibold text-[#0A0A0A]">Konfirmasi kata sandi baru</label>
                                <div class="flex items-center gap-2 rounded-lg border bg-white px-3 py-2 focus-within:border-[#2563EB]"
                                     :class="confirm.length > 0 && !matches ? 'border-[#EF4444]' : 'border-[#E5E7EB]'">
                                    <input id="password_confirmation" name="password_confirmation" x-model="confirm" :type="showConfirm ? 'text' : 'password'"
                                           autocomplete="new-password" placeholder="Masukkan kata sandi baru"
                                           class="w-full bg-transparent text-xs text-[#0A0A0A] outline-none placeholder:text-[#9CA3AF]">
                                    <button type="button" @click="showConfirm = !showConfirm" class="text-[#6B7280]">
                                        <svg x-show="showConfirm" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg x-show="!showConfirm" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.222-.563c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-2.115 2.115a3 3 0 11-4.243-4.243M3 3l18 18"></path></svg>
                                    </button>
                                </div>
                                <p x-show="confirm.length > 0 && !matches" x-cloak class="text-[11px] font-semibold text-[#EF4444]">Konfirmasi kata sandi tidak cocok.</p>
                            </div>

                            <p class="text-[11px] text-[#9CA3AF]">Kode OTP akan dikirim ke {{ $maskedEmail }}.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4">
                        <button type="button" @click="close()" 
                                class="rounded-lg bg-[#EF4444] px-4 py-2 text-xs font-semibold text-white hover:bg-red-600 transition">
                            Batal
                        </button>
                        <button type="button" @click="goToStep2()" :disabled="!canSubmit" 
                                class="rounded-lg bg-[#2563EB] px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition">
                            Kirim Kode OTP
                        </button>
                    </div>
                </div>

                {{-- STEP 2: Verifikasi Kode OTP --}}
                <div x-show="step === 2" x-cloak class="flex flex-col justify-between h-full gap-6">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-[#0A0A0A]">Masukkan kode OTP</h2>
                            <p class="text-xs text-[#6B7280] mt-0.5">Kami mengirimkan kode 6 digit ke {{ $maskedEmail }}. Kode berlaku 5 menit.</p>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-[#0A0A0A]">Kode OTP</span>
                                <span class="text-xs font-bold text-[#4B5563]" x-text="formattedTimer"></span>
                            </div>

                            <!-- 6 Box Kode OTP -->
                            <div class="flex items-center justify-between gap-2" @paste="onOtpPaste($event)">
                                <template x-for="(digit, idx) in 6" :key="idx">
                                    <input :id="`otp-${idx}`"
                                           type="text" 
                                           inputmode="numeric"
                                           pattern="[0-9]*"
                                           @input="onOtpInput($event, idx)"
                                           @keydown="onOtpKeydown($event, idx)"
                                           @focus="$event.target.select()"
                                           class="size-10 sm:size-11 rounded-xl border border-[#E5E7EB] bg-white text-center text-lg font-bold text-[#111827] outline-none focus:border-[#2563EB] focus:ring-2 focus:ring-blue-100 transition">
                                </template>
                            </div>

                            <div class="flex items-center justify-start">
                                <button type="button" @click="startTimer()" class="text-xs font-semibold text-[#2563EB] hover:underline">
                                    Kirim ulang kode
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#F3F4F6]">
                        <button type="button" @click="step = 1" 
                                class="rounded-lg bg-[#EF4444] px-4 py-2 text-xs font-semibold text-white hover:bg-red-600 transition">
                            Batal
                        </button>
                        <button type="button" @click="alert('Kata sandi berhasil disimpan!'); close();" :disabled="!isOtpComplete" 
                                class="rounded-lg bg-[#2563EB] px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition">
                            Simpan Kata Sandi
                        </button>
                    </div>
>>>>>>> 98ba7d0 (fix: update navbar, header, layouts, views, dan web routes):resources/views/profil.blade.php
                </div>

            </div>
<<<<<<< HEAD:resources/views/mahasiswa/profil.blade.php
        @if (Route::has('password.otp.send'))
            </form>
        @else
            </div>
        @endif
    </div>
</div>
@endsection
=======
        </div>
    </div>
</div>

</body>
</html>
>>>>>>> 98ba7d0 (fix: update navbar, header, layouts, views, dan web routes):resources/views/profil.blade.php
