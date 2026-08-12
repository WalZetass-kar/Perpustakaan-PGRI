<!DOCTYPE html>
<html lang="id" class="scroll-smooth" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perpustakaan SMK PGRI')</title>
    
    <!-- Favicon Logo SMK PGRI -->
    <link rel="icon" type="image/png" href="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c', // Primary Red #B91C1C
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>

    <!-- AOS (Animate On Scroll) Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Header Navigation -->
    <header class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 transition duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Official SMK PGRI Logo & Brand Name -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 group">
                <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-10 h-10 object-contain transform group-hover:scale-110 transition duration-300 drop-shadow-xs">
                <div>
                    <span class="text-sm sm:text-base font-extrabold text-gray-900 leading-tight block group-hover:text-brand-700 transition">Perpustakaan PGRI</span>
                    <span class="text-[10px] text-gray-500 font-semibold tracking-wider uppercase block">SMK PGRI</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-xs font-semibold">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-brand-700 border-b-2 border-brand-700 py-5' : 'text-gray-600 hover:text-brand-700 transition' }}">Beranda</a>
                <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') ? 'text-brand-700 border-b-2 border-brand-700 py-5' : 'text-gray-600 hover:text-brand-700 transition' }}">Katalog Buku</a>
                <a href="{{ route('home') }}#pusat-data-section" class="text-gray-600 hover:text-brand-700 transition">Pusat Data</a>
                <a href="{{ route('home') }}#fitur-section" class="text-gray-600 hover:text-brand-700 transition">Fitur Utama</a>
            </nav>

            <!-- Right Actions -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-brand-700 rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <span>Dashboard</span>
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold text-gray-700 hover:text-brand-700 transition">Masuk</a>
                    <a href="{{ route('katalog') }}" class="px-4.5 py-2 text-xs font-bold text-white bg-brand-700 rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Cari Buku</a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex md:hidden items-center gap-2">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-gray-100 bg-white px-4 pt-2 pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700">Beranda</a>
            <a href="{{ route('katalog') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700">Katalog Buku</a>
            <a href="{{ route('home') }}#pusat-data-section" class="block px-3 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700">Pusat Data</a>
            <a href="{{ route('home') }}#fitur-section" class="block px-3 py-2 rounded-lg text-xs font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700">Fitur Utama</a>
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center px-4 py-2 text-xs font-bold text-white bg-brand-700 rounded-lg hover:bg-brand-800">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold text-gray-700">Masuk</a>
                    <a href="{{ route('katalog') }}" class="px-4 py-2 text-xs font-bold text-white bg-brand-700 rounded-lg hover:bg-brand-800">Cari Buku</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-gray-100 text-xs">
                
                <!-- Col 1: Brand & Official Logo -->
                <div class="md:col-span-1 space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-9 h-9 object-contain">
                        <div>
                            <span class="font-extrabold text-gray-900 block leading-tight">Perpustakaan PGRI</span>
                            <span class="text-[10px] text-gray-500 font-semibold tracking-wider uppercase">SMK PGRI</span>
                        </div>
                    </div>
                    <p class="text-gray-500 leading-relaxed text-[11px]">
                        Layanan perpustakaan berbasis sistem informasi terpadu untuk mendukung kegiatan literasi, modul pembelajaran kejuruan, dan riset siswa.
                    </p>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div class="space-y-2">
                    <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[11px]">Navigasi Katalog</h4>
                    <ul class="space-y-1.5 text-gray-600">
                        <li><a href="{{ route('katalog') }}" class="hover:text-brand-700 transition">Katalog Koleksi Buku</a></li>
                        <li><a href="{{ route('home') }}#pusat-data-section" class="hover:text-brand-700 transition">Pusat Data Informasi</a></li>
                        <li><a href="{{ route('katalog', ['status' => 'tersedia']) }}" class="hover:text-brand-700 transition">Buku Tersedia Dipinjam</a></li>
                    </ul>
                </div>

                <!-- Col 3: Layanan & Aturan -->
                <div class="space-y-2">
                    <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[11px]">Informasi & Layanan</h4>
                    <ul class="space-y-1.5 text-gray-600">
                        <li><a href="{{ route('home') }}#fitur-section" class="hover:text-brand-700 transition">Fitur Inlislite</a></li>
                        <li><span class="text-gray-500">Peraturan & Tata Tertib</span></li>
                        <li><span class="text-gray-500">Sistem Denda & Sanksi</span></li>
                    </ul>
                </div>

                <!-- Col 4: Kontak & Alamat -->
                <div class="space-y-2">
                    <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[11px]">Alamat & Kontak</h4>
                    <p class="text-gray-600 leading-relaxed text-[11px]">
                        Jl. Pendidikan No. 45, Gedung Utama Perpustakaan SMK PGRI.<br>
                        Email: perpustakaan@smkpgri.sch.id<br>
                        Telp: (021) 7890-1234
                    </p>
                </div>

            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-[11px] text-gray-500 gap-3">
                <span>Perpustakaan SMK PGRI &copy; {{ date('Y') }}. All rights reserved.</span>
                <span class="font-medium text-gray-400">Sistem Informasi Perpustakaan Sekolah Terpadu</span>
            </div>
        </div>
    </footer>

    <!-- Initialize AOS Animation On Scroll -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100,
                easing: 'ease-out-cubic',
            });
        });
    </script>
</body>
</html>
