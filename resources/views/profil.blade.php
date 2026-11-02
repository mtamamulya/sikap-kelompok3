<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil mahasiswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Jika Alpine belum terpasang via npm, aktifkan baris di bawah --}}
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-[#F9FAFB] text-[#111827] antialiased">
@php
    // Data dari controller: compact('mahasiswa'). Fallback untuk preview.
    $nama     = $mahasiswa->nama     ?? 'Arya Bima';
    $angkatan = $mahasiswa->angkatan ?? '2024';
    $email    = $mahasiswa->email    ?? 'aryabima@mhs.polines.ac.id';
    $nim      = $mahasiswa->nim      ?? '43325115';
    $prodi    = $mahasiswa->prodi    ?? 'Teknologi Rekayasa Komputer';
    $status   = $mahasiswa->status   ?? 'Aktif';

    // Masking email: a***********@domain
    [$local, $domain] = explode('@', $email, 2);
    $maskedEmail = substr($local, 0, 1) . str_repeat('*', max(strlen($local) - 1, 3)) . '@' . $domain;
@endphp

<div class="flex min-h-screen flex-col"
     x-data="{
        open: false,
        pw: '',
        confirm: '',
        showPw: false,
        showConfirm: false,
        get rules() {
            return [
                { label: 'Minimal 8 karakter',  ok: this.pw.length >= 8 },
                { label: 'Mengandung huruf',    ok: /[A-Za-z]/.test(this.pw) },
                { label: 'Mengandung angka',    ok: /\d/.test(this.pw) },
                { label: 'Mengandung simbol',   ok: /[^A-Za-z0-9]/.test(this.pw) },
            ];
        },
        get count() { return this.rules.filter(r => r.ok).length },
        get strong() { return this.count === 4 },
        get barColor() { return this.strong ? '#80CC16' : '#FF3F3F' },
        get matches() { return this.confirm.length > 0 && this.pw === this.confirm },
        get canSubmit() { return this.strong && this.matches },
        close() { this.open = false; this.pw = ''; this.confirm = ''; this.showPw = false; this.showConfirm = false; }
     }"
     x-on:keydown.escape.window="close()">

    {{-- Header --}}
    <header class="flex h-[104px] shrink-0 items-center justify-between overflow-hidden bg-white px-8 shadow-[0_3px_4px_rgba(0,0,0,0.14)]">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/logo-trk.png') }}" alt="" class="size-16">
            <div class="whitespace-pre-line text-[22px] font-bold leading-[130%] text-[#003385]">TEKNOLOGI&#10;REKAYASA KOMPUTER</div>
        </div>
    </header>

    <main class="flex flex-1 flex-col gap-6 px-4 py-6 sm:px-8">
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
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-1 rounded-md bg-[#FF3F3F] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 8v-2a2 2 0 0 0-2-2h-7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-2"/><path d="M9 12h12l-3-3"/><path d="M18 15l3-3"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            {{-- Kartu avatar --}}
            <section class="flex flex-col items-center gap-4 rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
                <div class="size-24 rounded-full bg-[lightgray]"></div>
                <div class="flex flex-col items-center gap-2 self-stretch text-center">
                    <div class="text-2xl font-medium leading-[30px] tracking-[-0.15px] text-[#111827]">{{ $nama }}</div>
                    <div class="text-sm leading-5 text-[#6B7280]">Mahasiswa • Angkatan {{ $angkatan }}</div>
                </div>
            </section>

            {{-- Informasi mahasiswa --}}
            <section class="flex flex-col gap-4 rounded-2xl border border-[#E5E7EB] bg-white p-4 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
                <div class="flex flex-col gap-2">
                    <h2 class="text-[22px] font-semibold text-[#111827]">Informasi mahasiswa</h2>
                    <p class="text-sm leading-5 text-[#6B7280]">Data identitas yang terdaftar pada sistem akademik.</p>
                </div>

                <div class="flex flex-wrap gap-5">
                    <x-profil.field label="Email" detail="Email mahasiswa">
                        <div class="break-all text-[19px] font-medium leading-[27px] tracking-[-0.216px] text-[#0A0A0A]">{{ $email }}</div>
                    </x-profil.field>
                    <x-profil.field label="NIM" :value="$nim" detail="Nomor Induk Mahasiswa" />
                </div>

                <div class="flex flex-wrap gap-5">
                    <x-profil.field label="Prodi (Program Studi)" :value="$prodi" detail="Diploma IV / Sarjana Terapan" />
                    <x-profil.field label="Status akademik" detail="Terdaftar pada semester berjalan">
                        <div class="w-fit rounded-lg border border-[#D6EDB5] bg-[#E6F3D3] px-2 py-0.5 text-[15px] font-semibold leading-4 tracking-[-0.12px] text-[#4C7D08]">{{ $status }}</div>
                    </x-profil.field>
                </div>

                <div class="flex items-center gap-2">
                    <svg class="size-4 shrink-0 text-[#2277FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M11.46 20.846a12 12 0 0 1-7.96-14.846a12 12 0 0 0 8.5-3a12 12 0 0 0 8.5 3a12 12 0 0 1-.09 7.06"/><path d="M15 19l2 2l4-4"/></svg>
                    <span class="text-xs tracking-[-0.12px] text-[#6B7280]">Data mahasiswa dikelola oleh bagian administrasi akademik.</span>
                </div>
            </section>
        </div>
    </main>

    {{-- Dialog Ubah Kata Sandi --}}
    <div x-show="open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/40 p-4"
         x-on:click.self="close()">
        <form method="POST" action="{{ route('password.otp.send') }}" role="dialog" aria-modal="true"
              class="flex w-full max-w-[872px] flex-col overflow-hidden rounded-lg bg-white shadow-[-1px_2px_5px_rgba(0,0,0,0.1),-4px_9px_10px_rgba(0,0,0,0.09),-10px_20px_13px_rgba(0,0,0,0.05)] md:flex-row">
            @csrf

            <aside class="flex shrink-0 flex-col gap-6 bg-[#F2F6FD] px-5 pb-6 pt-5 md:w-[314px] md:justify-between md:gap-12">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-2">
                        <div class="text-base tracking-[-0.18px] text-[#00235C]">K E A M A N A N &nbsp;A K U N</div>
                        <div class="text-2xl font-medium leading-[30px] tracking-[-0.15px] text-[#0A0A0A]">Ubah Kata Sandi</div>
                    </div>
                    <ol class="flex flex-col gap-8 px-4 pb-6 pt-4">
                        <li class="flex items-start gap-2.5">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-[#27F] bg-white text-xs font-medium text-[#0162DD]">01</span>
                            <div class="flex flex-col gap-2">
                                <div class="text-base font-semibold leading-[22px] tracking-[-0.18px] text-[#0A0A0A]">Kata sandi baru</div>
                                <div class="text-base leading-[22px] tracking-[-0.18px] text-[#0A0A0A]">Buat kata sandi yang kuat</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-[#A1AEBE] bg-white text-xs font-medium text-[#242E39]">02</span>
                            <div class="flex flex-col gap-2">
                                <div class="text-base font-semibold leading-[22px] tracking-[-0.18px] text-[#A8A8A8]">Verifikasi OTP</div>
                                <div class="text-base leading-[22px] tracking-[-0.18px] text-[#A8A8A8]">Konfirmasi dengan kode dari email Anda.</div>
                            </div>
                        </li>
                    </ol>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="size-6 shrink-0 text-[#2277FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11.46 20.846a12 12 0 0 1-7.96-14.846a12 12 0 0 0 8.5-3a12 12 0 0 0 8.5 3a12 12 0 0 1-.09 7.06"/><path d="M15 19l2 2l4-4"/></svg>
                    <p class="max-w-[196px] text-sm font-medium leading-5 tracking-[-0.16px] text-[#2E2E2E]">
                        Kata sandi lama tidak diperlukan. Identitas Anda dipastikan lewat kode OTP.
                    </p>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col gap-8 px-5 pb-6 pt-5">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-2">
                        <h2 class="text-2xl font-medium leading-[30px] tracking-[-0.15px] text-[#0A0A0A]">Buat kata sandi baru</h2>
                        <p class="text-base leading-[22px] tracking-[-0.18px] text-[#A8A8A8]">
                            Gunakan kata sandi yang berbeda dari sebelumnya dan belum pernah dipakai di tempat lain.
                        </p>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-3">
                                {{-- Kata sandi baru --}}
                                <div class="flex flex-col gap-2">
                                    <label for="password" class="text-base font-medium leading-[22px] tracking-[-0.18px] text-[#0A0A0A]">Kata sandi baru</label>
                                    <div class="flex items-center gap-1 rounded-lg border border-[rgba(10,10,10,0.06)] bg-white px-4 py-1.5 focus-within:border-[#27F]">
                                        <input id="password" name="password" x-model="pw" :type="showPw ? 'text' : 'password'"
                                               autocomplete="new-password" placeholder="Masukkan kata sandi baru"
                                               class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] text-[#0A0A0A] outline-none placeholder:text-[#A8A8A8]">
                                        <button type="button" x-on:click="showPw = !showPw"
                                                :aria-label="showPw ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="text-[#0A0A0A]">
                                            <svg x-show="showPw" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M21 12c-2.4 4-5.4 6-9 6c-3.6 0-6.6-2-9-6c2.4-4 5.4-6 9-6c3.6 0 6.6 2 9 6"/></svg>
                                            <svg x-show="!showPw" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1-4.681 1.327c-3.6 0-6.6-2-9-6c1.272-2.12 2.712-3.678 4.32-4.674m2.86-1.146a9.055 9.055 0 0 1 1.82-.18c3.6 0 6.6 2 9 6c-.666 1.11-1.379 2.067-2.138 2.87"/><path d="M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Indikator kekuatan --}}
                                <div class="flex items-center gap-2.5">
                                    <div class="flex flex-1 items-center gap-1">
                                        <template x-for="i in 4" :key="i">
                                            <div class="h-[3px] flex-1" :style="`background:${i <= count ? barColor : '#E3E3E3'}`"></div>
                                        </template>
                                    </div>
                                    <span class="w-9 text-xs font-medium tracking-[-0.12px]"
                                          :style="`color:${count === 0 ? 'transparent' : barColor}`"
                                          x-text="strong ? 'Kuat' : 'Lemah'"></span>
                                </div>

                                {{-- Checklist aturan --}}
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-x-6">
                                    <template x-for="r in rules" :key="r.label">
                                        <div class="flex items-center gap-2">
                                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full border"
                                                  :class="r.ok ? 'border-2 border-[#80CC16] bg-[#80CC16]' : 'border-[#A8A8A8]'">
                                                <svg x-show="r.ok" class="size-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5l10-10"/></svg>
                                            </span>
                                            <span class="text-sm font-medium leading-5 tracking-[-0.16px]"
                                                  :style="`color:${r.ok ? '#80CC16' : '#A8A8A8'}`" x-text="r.label"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Konfirmasi --}}
                            <div class="flex flex-col gap-2">
                                <label for="password_confirmation" class="text-base font-medium leading-[22px] tracking-[-0.18px] text-[#0A0A0A]">Konfirmasi kata sandi baru</label>
                                <div class="flex items-center gap-1 rounded-lg border bg-white px-4 py-1.5 focus-within:border-[#27F]"
                                     :class="confirm.length > 0 && !matches ? 'border-[#FF3F3F]' : 'border-[rgba(10,10,10,0.06)]'">
                                    <input id="password_confirmation" name="password_confirmation" x-model="confirm" :type="showConfirm ? 'text' : 'password'"
                                           autocomplete="new-password" placeholder="Masukkan kata sandi baru"
                                           class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] text-[#0A0A0A] outline-none placeholder:text-[#A8A8A8]">
                                    <button type="button" x-on:click="showConfirm = !showConfirm"
                                            :aria-label="showConfirm ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="text-[#0A0A0A]">
                                        <svg x-show="showConfirm" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M21 12c-2.4 4-5.4 6-9 6c-3.6 0-6.6-2-9-6c2.4-4 5.4-6 9-6c3.6 0 6.6 2 9 6"/></svg>
                                        <svg x-show="!showConfirm" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1-4.681 1.327c-3.6 0-6.6-2-9-6c1.272-2.12 2.712-3.678 4.32-4.674m2.86-1.146a9.055 9.055 0 0 1 1.82-.18c3.6 0 6.6 2 9 6c-.666 1.11-1.379 2.067-2.138 2.87"/><path d="M3 3l18 18"/></svg>
                                    </button>
                                </div>
                                <p x-show="confirm.length > 0 && !matches" x-cloak class="text-xs font-medium text-[#FF3F3F]">Konfirmasi kata sandi tidak cocok.</p>
                            </div>

                            @error('password')
                                <p class="text-xs font-medium text-[#FF3F3F]">{{ $message }}</p>
                            @enderror
                        </div>

                        <p class="text-sm font-medium leading-5 tracking-[-0.16px] text-[#A8A8A8]">
                            Kode OTP akan dikirim ke {{ $maskedEmail }}.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2">
                    <button type="button" x-on:click="close()"
                            class="rounded-md bg-[#FF3F3F] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">
                        Batal
                    </button>
                    <button type="submit" :disabled="!canSubmit"
                            class="rounded-md bg-[#27F] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)] disabled:cursor-not-allowed disabled:opacity-50">
                        Kirim Kode OTP
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>