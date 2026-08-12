<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk - Perpustakaan SMK PGRI Pekanbaru</title>
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
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-sans" x-data="{ roleTab: '{{ request()->is('admin/login') ? 'admin' : 'siswa' }}' }">

    <!-- Ambient Glowing Background Orbs -->
    <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-15 pointer-events-none" style="background-image: url('https://smkpgripekanbaru.sch.id/images/pgri.webp');"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand-700/15 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none animate-bounce" style="animation-duration: 8s;"></div>

    <!-- Main Flip/Slide Container Card -->
    <div class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-gray-200 grid grid-cols-1 md:grid-cols-12 transition-all duration-500">
        
        <!-- Left Side: Interactive Dynamic Banner (Animated Color Transition) -->
        <div class="md:col-span-5 p-8 sm:p-10 flex flex-col justify-between relative transition-all duration-700"
             :class="roleTab === 'admin' ? 'bg-gradient-to-br from-gray-950 via-gray-900 to-brand-950 text-white' : 'bg-gradient-to-br from-brand-800 via-brand-700 to-red-900 text-white'">
            
            <div class="space-y-6">
                <!-- Back Home Button -->
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 text-white border border-white/20 hover:bg-white/20 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Beranda</span>
                </a>

                <!-- Dynamic Title & Description with Animated Transition -->
                <div class="pt-2">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-14 h-14 object-contain drop-shadow-md">
                    
                    <!-- Siswa Banner View -->
                    <template x-if="roleTab === 'siswa'">
                        <div x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                            <span class="inline-block px-3 py-1 mt-4 rounded-full text-[10px] font-extrabold bg-white/10 text-emerald-300 border border-white/20 uppercase tracking-wider">
                                Portal Siswa &amp; Anggota
                            </span>
                            <h2 class="mt-3 text-2xl font-black leading-snug">Selamat Datang di Inlislite Siswa</h2>
                            <p class="mt-2 text-xs text-red-100 leading-relaxed font-normal">
                                Akses katalog modul kejuruan, peminjaman buku, riwayat transaksi, dan kartu anggota digital Anda.
                            </p>
                        </div>
                    </template>

                    <!-- Admin Banner View -->
                    <template x-if="roleTab === 'admin'">
                        <div x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                            <span class="inline-block px-3 py-1 mt-4 rounded-full text-[10px] font-black bg-brand-700 text-white border border-brand-500 uppercase tracking-wider">
                                Back-Office Portal
                            </span>
                            <h2 class="mt-3 text-2xl font-black leading-snug text-white">Portal Pengelola &amp; Staff</h2>
                            <p class="mt-2 text-xs text-gray-400 leading-relaxed font-normal">
                                Akses terbatas sirkulasi peminjaman, entri katalog master, denda, pendaftaran user, dan audit log.
                            </p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Role Switcher Control Buttons inside Left Banner -->
            <div class="pt-6 border-t border-white/10 space-y-2">
                <span class="text-[11px] text-gray-300 block font-medium">Pilih Jenis Akses Akun:</span>
                <div class="grid grid-cols-2 gap-2 p-1 bg-black/20 rounded-2xl border border-white/10 backdrop-blur-xs">
                    <button @click="roleTab = 'siswa'" :class="roleTab === 'siswa' ? 'bg-white text-brand-700 font-extrabold shadow-md' : 'text-gray-200 hover:text-white font-semibold'" class="py-2 px-3 rounded-xl text-xs transition duration-300 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span>Siswa</span>
                    </button>
                    <button @click="roleTab = 'admin'" :class="roleTab === 'admin' ? 'bg-brand-700 text-white font-extrabold shadow-md' : 'text-gray-200 hover:text-white font-semibold'" class="py-2 px-3 rounded-xl text-xs transition duration-300 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Admin</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Side: Dynamic Form Box with Smooth Crossfade Transition -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center space-y-6 relative bg-white">
            
            @if($errors->any())
                <div class="p-3.5 bg-rose-50 border-2 border-rose-200 rounded-xl text-xs font-bold text-rose-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- 1. SISWA FORM CONTENT (Smooth Animated Transition) -->
            <div x-show="roleTab === 'siswa'" 
                 x-transition:enter="transition ease-out duration-400 transform" 
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform absolute inset-0 p-8 sm:p-10"
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95"
                 class="space-y-6">
                
                <div>
                    <h3 class="text-xl font-extrabold text-gray-900">Masuk Akun Siswa</h3>
                    <p class="text-xs text-gray-500 mt-1">Masukkan email siswa terdaftar di SMK PGRI Pekanbaru.</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label for="email_siswa" class="block font-bold text-gray-700 mb-1.5">Email Siswa <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="email" name="email" id="email_siswa" value="{{ old('email', 'siswa@smkpgri.sch.id') }}" required placeholder="contoh: siswa@smkpgri.sch.id"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium text-gray-900 transition">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                    </div>

                    <div>
                        <label for="password_siswa" class="block font-bold text-gray-700 mb-1.5">Kata Sandi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="password_siswa" value="password" required placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium text-gray-900 transition">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-gray-600 font-medium">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-700 focus:ring-brand-700">
                            <span>Ingat Saya</span>
                        </label>
                        <span class="text-gray-400 font-medium">Bantuan Pustakawan</span>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Masuk Akun Siswa</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </button>
                </form>

                <div class="border-t-2 border-gray-100 pt-4 text-center">
                    <div class="bg-gray-50 p-3.5 rounded-xl border-2 border-gray-200 text-[11px] text-gray-600">
                        <span class="font-bold text-gray-900 block mb-0.5">Demo Login Akun Siswa:</span>
                        <span>Email: <strong>siswa@smkpgri.sch.id</strong> | Password: <strong>password</strong></span>
                    </div>
                </div>
            </div>

            <!-- 2. ADMIN FORM CONTENT (Smooth Animated Transition) -->
            <div x-show="roleTab === 'admin'" 
                 x-transition:enter="transition ease-out duration-400 transform" 
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform absolute inset-0 p-8 sm:p-10"
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95"
                 class="space-y-6" x-cloak>
                
                <div>
                    <h3 class="text-xl font-extrabold text-gray-900">Autentikasi Pengelola</h3>
                    <p class="text-xs text-gray-500 mt-1">Masukkan email Administrator atau Petugas Pustakawan.</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label for="email_admin" class="block font-bold text-gray-700 mb-1.5">Email Pengelola <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="email" name="email" id="email_admin" value="{{ old('email', 'admin@smkpgri.sch.id') }}" required placeholder="contoh: admin@smkpgri.sch.id"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium text-gray-900 transition">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>

                    <div>
                        <label for="password_admin" class="block font-bold text-gray-700 mb-1.5">Kata Sandi Akses <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="password_admin" value="password" required placeholder="••••••••"
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

                    <button type="submit" class="w-full py-3.5 bg-gray-900 text-white font-extrabold text-xs rounded-xl hover:bg-black transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Masuk Portal Admin</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </button>
                </form>

                <div class="border-t-2 border-gray-100 pt-4 text-center">
                    <div class="bg-gray-50 p-3.5 rounded-xl border-2 border-gray-200 text-[11px] text-gray-600 space-y-1">
                        <p class="font-bold text-gray-900 mb-0.5">Demo Login Pengelola:</p>
                        <p>Admin: <strong>admin@smkpgri.sch.id</strong> | pass: <strong>password</strong></p>
                        <p>Pustakawan: <strong>pustakawan@smkpgri.sch.id</strong> | pass: <strong>password</strong></p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
