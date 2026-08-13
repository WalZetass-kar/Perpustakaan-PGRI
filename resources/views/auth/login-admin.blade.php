<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator & Pustakawan - Perpustakaan SMK PGRI Pekanbaru</title>
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

    <!-- Ambient Background Orbs (Unified Siswa Theme) -->
    <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-15 pointer-events-none" style="background-image: url('https://smkpgripekanbaru.sch.id/images/pgri.webp');"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand-700/15 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none animate-bounce" style="animation-duration: 8s;"></div>

    <!-- Login Container Card -->
    <div class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-gray-200 grid grid-cols-1 md:grid-cols-12">
        
        <!-- Left Side: Red Brand Theme Banner -->
        <div class="md:col-span-5 bg-gradient-to-br from-brand-800 via-brand-700 to-red-900 text-white p-8 sm:p-10 flex flex-col justify-between relative">
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 text-white border border-white/20 hover:bg-white/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <div class="pt-4">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-14 h-14 object-contain drop-shadow-md">
                    <span class="inline-block px-3 py-1 mt-4 rounded-full text-[10px] font-black bg-white/10 text-emerald-300 border border-white/20 uppercase tracking-wider">
                        Back-Office Portal
                    </span>
                    <h2 class="mt-3 text-2xl font-black leading-snug">Portal Admin &amp; Staff Pustakawan</h2>
                    <p class="mt-2 text-xs text-red-100 leading-relaxed font-normal">
                        Akses khusus sirkulasi peminjaman, entri katalog master, denda, pendaftaran user, dan audit log.
                    </p>
                </div>
            </div>

            <!-- Switch Role Helper -->
            <div class="pt-6 border-t border-white/10">
                <span class="text-[11px] text-red-200 block font-medium">Siswa / Anggota Biasa?</span>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-300 hover:text-white transition mt-1">
                    <span>Masuk Portal Siswa</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Right Side: Admin Login Form -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center space-y-6 bg-white">
            <div>
                <h3 class="text-xl font-extrabold text-gray-900">Autentikasi Pengelola</h3>
                <p class="text-xs text-gray-500 mt-1">Masukkan kredensial Administrator atau Petugas Pustakawan resmi.</p>
            </div>

            @if($errors->any())
                <div class="p-3.5 bg-rose-50 border-2 border-rose-200 rounded-xl text-xs font-bold text-rose-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label for="email" class="block font-bold text-gray-700 mb-1.5">Email Pengelola <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="contoh: admin@smkpgri.sch.id"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium text-gray-900 transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>

                <div>
                    <label for="password" class="block font-bold text-gray-700 mb-1.5">Kata Sandi Akses <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium text-gray-900 transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-600 font-medium">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-700 focus:ring-brand-700">
                        <span>Ingat Akses Saya</span>
                    </label>
                    <span class="text-gray-400 font-medium">Akses Back-Office</span>
                </div>

                <button type="submit" class="w-full py-3.5 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                    <span>Masuk Portal Admin</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </button>
            </form>

            <div class="border-t-2 border-gray-100 pt-4 text-center">
                <p class="text-[11px] text-gray-500 font-medium flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Autentikasi Terenkripsi Pengelola Perpustakaan SMK PGRI</span>
                </p>
            </div>
        </div>

    </div>

</body>
</html>
