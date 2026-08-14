<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa - Perpustakaan SMK PGRI Pekanbaru</title>
    <!-- Favicon Logo SMK PGRI -->
    <link rel="icon" type="image/png" href="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Official Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-sans">

    <!-- Background Image with Soft Red Overlay -->
    <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-20 pointer-events-none" style="background-image: url('https://smkpgripekanbaru.sch.id/images/pgri.webp');"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand-700/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Login Container Card -->
    <div class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-200 grid grid-cols-1 md:grid-cols-12">
        
        <!-- Left Side: Siswa Portal Banner -->
        <div class="md:col-span-5 bg-gradient-to-br from-brand-800 via-brand-700 to-red-900 text-white p-8 sm:p-10 flex flex-col justify-between relative">
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-white/10 text-white border border-white/20 hover:bg-white/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <div class="pt-4">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-14 h-14 object-contain drop-shadow-md">
                    <h2 class="mt-4 text-2xl font-black leading-snug">Portal Siswa &amp; Anggota</h2>
                    <p class="mt-2 text-xs text-red-100 leading-relaxed font-normal">
                        Akses modul perpustakaan digital, peminjaman buku kejuruan, riwayat transaksi, dan kartu anggota sekolah.
                    </p>
                </div>
            </div>

            <!-- Switch Role Helper -->
            <div class="pt-6 border-t border-white/10">
                <span class="text-[11px] text-red-200 block font-medium">Pengelola / Petugas Perpustakaan?</span>
                <a href="{{ route('admin.login.form') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-300 hover:text-white transition mt-1">
                    <span>Login Khusus Admin &amp; Staff</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Right Side: Siswa Login Form -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center space-y-6">
            <div>
                <h3 class="text-xl font-extrabold text-gray-900">Masuk Akun Siswa</h3>
                <p class="text-xs text-gray-500 mt-1">Gunakan alamat email siswa terdaftar di SMK PGRI Pekanbaru.</p>
            </div>

            @if($errors->any())
                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label for="email" class="block font-bold text-gray-700 mb-1.5">Email Siswa <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="contoh: siswa@smkpgri.sch.id"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    </div>
                </div>

                <div>
                    <label for="password" class="block font-bold text-gray-700 mb-1.5">Kata Sandi <span class="text-rose-500">*</span></label>
                    <div class="relative" x-data="{ showPass: false }">
                        <input :type="showPass ? 'text' : 'password'" name="password" id="password" required placeholder="••••••••"
                            class="w-full pl-10 pr-11 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
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
                        <span>Ingat Saya</span>
                    </label>
                    <span class="text-gray-400 font-medium">Lupa sandi? Hubungi Pustakawan</span>
                </div>

                <button type="submit" class="w-full py-3 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                    <span>Masuk Akun Siswa</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </button>
            </form>

            <div class="border-t border-gray-100 pt-4 text-center">
                <p class="text-[11px] text-gray-600 font-medium">
                    Belum memiliki akun siswa terdaftar? 
                    <a href="{{ route('register') }}" class="font-extrabold text-brand-700 hover:underline">Daftar Akun Baru</a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>
