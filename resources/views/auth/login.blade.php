<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pengelola - Perpustakaan SMK PGRI Pekanbaru</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                            950: '#450a0a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-sans">

    <!-- Ambient Glowing Background Orbs -->
    <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-15 pointer-events-none" style="background-image: url('https://smkpgripekanbaru.sch.id/images/pgri.webp');"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand-700/15 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none animate-bounce" style="animation-duration: 8s;"></div>

    <!-- Main Container Card -->
    <div class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-gray-200 grid grid-cols-1 md:grid-cols-12 transition-all duration-300">
        
        <!-- Left Side: Brand Banner -->
        <div class="md:col-span-5 bg-gradient-to-br from-brand-800 via-brand-700 to-red-900 text-white p-8 sm:p-10 flex flex-col justify-between relative">
            <div class="space-y-6">
                <!-- Back Home Button -->
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 text-white border border-white/20 hover:bg-white/20 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Beranda</span>
                </a>

                <div class="pt-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI Pekanbaru" class="w-16 h-16 object-contain drop-shadow-md">
                    
                    <span class="inline-block px-3 py-1 mt-4 rounded-full text-[10px] font-extrabold bg-white/10 text-emerald-300 border border-white/20 uppercase tracking-wider">
                        Portal Back-Office
                    </span>
                    <h2 class="mt-3 text-2xl font-black leading-snug">Perpustakaan SMK PGRI</h2>
                    <p class="mt-2 text-xs text-red-100 leading-relaxed font-normal">
                        Akses khusus pengelola perpustakaan untuk manajemen koleksi buku, data master, dan sirkulasi peminjaman harian.
                    </p>
                </div>
            </div>

            <!-- Bottom Badge -->
            <div class="pt-6 border-t border-white/10 flex items-center gap-2 text-[11px] text-red-100">
                <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Sistem Informasi Terenkripsi &amp; Terproteksi</span>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center space-y-6 bg-white">
            <div>
                <h3 class="text-xl font-extrabold text-gray-900">Autentikasi Pengelola</h3>
                <p class="text-xs text-gray-500 mt-1">Masukkan kredensial akun Admin Perpustakaan Anda.</p>
            </div>

            @if($errors->any())
                <div class="p-3.5 bg-rose-50 border-2 border-rose-200 rounded-xl text-xs font-bold text-rose-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label for="email" class="block font-bold text-gray-700 mb-1.5">Email Pengelola <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="contoh: admin@smkpgri.sch.id"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium text-gray-900 transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    </div>
                </div>

                <div>
                    <label for="password" class="block font-bold text-gray-700 mb-1.5">Kata Sandi Akses <span class="text-rose-500">*</span></label>
                    <div class="relative" x-data="{ showPass: false }">
                        <input :type="showPass ? 'text' : 'password'" name="password" id="password" required placeholder="••••••••"
                            class="w-full pl-10 pr-11 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium text-gray-900 transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-3.5 text-gray-400 hover:text-gray-700 focus:outline-none" :title="showPass ? 'Sembunyikan sandi' : 'Lihat sandi'">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-600 font-medium">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-700 focus:ring-brand-700">
                        <span>Ingat sesi saya</span>
                    </label>
                    <span class="text-[11px] text-gray-400 font-medium">Akses Terbatas</span>
                </div>

                <button type="submit" class="w-full py-3.5 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                    <span>Masuk ke Dashboard</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </button>
            </form>

            <div class="border-t-2 border-gray-100 pt-4 text-center">
                <p class="text-[11px] text-gray-500 font-medium">
                    Hanya untuk Petugas &amp; Administrator Perpustakaan SMK PGRI Pekanbaru.
                </p>
            </div>
        </div>

    </div>

</body>
</html>
