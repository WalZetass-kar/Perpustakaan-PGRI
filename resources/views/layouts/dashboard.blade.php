<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Perpustakaan')</title>

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
                        poppins: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c', // Primary Red
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
    <!-- SweetAlert2 CDN for Modern Premium Dialog Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Official Google Fonts: Poppins (Official SMK PGRI Font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> 
        [x-cloak] { display: none !important; }
        body { font-family: 'Poppins', sans-serif; } 
    </style>
</head>
<body class="bg-gray-100/70 text-gray-800 flex h-screen overflow-hidden font-sans">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-xs lg:hidden" x-transition.opacity></div>

    <!-- Sidebar Component (With Distinct Red-White Border Accent & Clear Dividers) -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r-2 border-gray-200/90 shadow-xl flex flex-col justify-between transition-transform duration-300 ease-in-out lg:translate-x-0 shrink-0">
        
        <div>
            <!-- Sidebar Header with Divider -->
            <div class="h-20 flex items-center justify-between px-5 border-b-2 border-gray-100 bg-gray-50/50">
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-10 h-10 object-contain drop-shadow-xs transform group-hover:scale-105 transition duration-300">
                    <div>
                        <span class="text-sm font-black text-gray-900 block leading-tight group-hover:text-brand-700 transition">Perpustakaan</span>
                        <span class="text-[10px] font-bold tracking-wider text-brand-700 uppercase block">SMK PGRI Pekanbaru</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links with Section Dividers & Active Indicator Pills -->
            <nav class="p-3 space-y-1 overflow-y-auto max-h-[calc(100vh-10rem)]">
                @php
                    $roleName = auth()->user()->role->name ?? 'mahasiswa';
                @endphp

                <!-- 1. ADMIN MENU -->
                @if($roleName === 'admin')
                    <div class="px-3 pt-3 pb-1 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Modul Utama</span>
                        <span class="w-8 h-px bg-gray-200"></span>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.dashboard') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.buku') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.buku*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Koleksi Buku</span>
                    </a>
                    <a href="{{ route('admin.kategori') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.kategori*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M11 7h8M11 11h8M11 15h8"/></svg>
                        <span>Kategori Buku</span>
                    </a>
                    <a href="{{ route('admin.rak') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.rak*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span>Rak Perpustakaan</span>
                    </a>
                    <a href="{{ route('admin.eksemplar') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.eksemplar*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Eksemplar & Barcode</span>
                    </a>
                    <a href="{{ route('admin.anggota') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.anggota*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Data User & Anggota</span>
                    </a>
                    
                    <div class="px-3 pt-5 pb-1 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center justify-between border-t border-gray-100 my-2">
                        <span>Manajemen & Laporan</span>
                        <span class="w-8 h-px bg-gray-200"></span>
                    </div>
                    <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.laporan*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Laporan Sistem</span>
                    </a>
                    <a href="{{ route('admin.audit-log') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.audit-log*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Audit Log</span>
                    </a>
                    <a href="{{ route('admin.pengaturan') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.pengaturan*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Pengaturan Aturan</span>
                    </a>
                @endif

                <!-- 2. PUSTAKAWAN MENU -->
                @if($roleName === 'pustakawan')
                    <div class="px-3 pt-3 pb-1 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Layanan Sirkulasi</span>
                        <span class="w-8 h-px bg-gray-200"></span>
                    </div>
                    <a href="{{ route('pustakawan.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('pustakawan.dashboard') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('pustakawan.peminjaman') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('pustakawan.peminjaman*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Peminjaman Cepat</span>
                    </a>
                    <a href="{{ route('pustakawan.pengembalian') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('pustakawan.pengembalian*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                        <span>Pengembalian Cepat</span>
                    </a>

                    <div class="px-3 pt-5 pb-1 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center justify-between border-t border-gray-100 my-2">
                        <span>Katalog & Anggota</span>
                        <span class="w-8 h-px bg-gray-200"></span>
                    </div>
                    <a href="{{ route('pustakawan.buku') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('pustakawan.buku*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Data Buku</span>
                    </a>
                    <a href="{{ route('pustakawan.eksemplar') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('pustakawan.eksemplar*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Eksemplar & Barcode</span>
                    </a>
                    <a href="{{ route('pustakawan.anggota') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('pustakawan.anggota*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Data Anggota</span>
                    </a>
                    <a href="{{ route('pustakawan.reservasi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('pustakawan.reservasi*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Reservasi Antrean</span>
                    </a>
                    <a href="{{ route('pustakawan.denda') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('pustakawan.denda*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Denda & Pembayaran</span>
                    </a>
                @endif

                <!-- 3. MAHASISWA MENU -->
                @if($roleName === 'mahasiswa')
                    <div class="px-3 pt-3 pb-1 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Aktivitas Saya</span>
                        <span class="w-8 h-px bg-gray-200"></span>
                    </div>
                    <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('mahasiswa.peminjaman') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('mahasiswa.peminjaman') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Peminjaman Saya</span>
                    </a>
                    <a href="{{ route('mahasiswa.riwayat') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('mahasiswa.riwayat') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat Peminjaman</span>
                    </a>
                    <a href="{{ route('mahasiswa.reservasi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('mahasiswa.reservasi') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Reservasi Antrean</span>
                    </a>
                    <a href="{{ route('mahasiswa.denda') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('mahasiswa.denda') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Catatan Denda</span>
                    </a>

                    <div class="px-3 pt-5 pb-1 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center justify-between border-t border-gray-100 my-2">
                        <span>Identitas & Akun</span>
                        <span class="w-8 h-px bg-gray-200"></span>
                    </div>
                    <a href="{{ route('mahasiswa.kartu') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('mahasiswa.kartu') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        <span>Kartu Perpustakaan</span>
                    </a>
                    <a href="{{ route('mahasiswa.notifikasi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('mahasiswa.notifikasi') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span>Notifikasi</span>
                    </a>
                    <a href="{{ route('mahasiswa.profil') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 border {{ request()->routeIs('mahasiswa.profil') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profil Saya</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- User Info Footer with Divider Line -->
        <div class="p-3 border-t-2 border-gray-100 bg-gray-50/80">
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border-2 border-gray-200 shadow-xs hover:border-brand-200 transition">
                <div class="min-w-0 pr-2">
                    <p class="text-xs font-black text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] font-bold text-gray-500 capitalize flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ auth()->user()->role->display_name ?? auth()->user()->role->name }}</span>
                    </p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar Akun" class="p-2 text-gray-400 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition border border-transparent hover:border-rose-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Top App Bar with Strong Border Divider & Shadow -->
        <header class="h-20 bg-white border-b-2 border-gray-200/90 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 shrink-0 shadow-2xs">
            <div class="flex items-center gap-3.5">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-black text-gray-900 truncate tracking-tight">@yield('page_heading', 'Overview')</h1>
                    <p class="text-[11px] text-gray-500 font-medium hidden sm:block">Perpustakaan SMK PGRI Pekanbaru</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('katalog') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-gray-700 bg-gray-50 hover:bg-brand-50 hover:text-brand-700 rounded-xl border border-gray-200 transition">
                    <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Pratinjau Katalog OPAC</span>
                </a>
                <span class="h-5 w-2px bg-gray-200 hidden sm:block"></span>
                <div class="text-right hidden sm:block">
                    <span class="text-xs font-black text-gray-800 block">{{ date('l, d M Y') }}</span>
                    <span class="text-[10px] text-emerald-600 font-bold flex items-center justify-end gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>System Live</span>
                    </span>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Body with Smooth Loading Skeleton -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-100/60" x-data="{ isLoading: true }" x-init="setTimeout(() => isLoading = false, 350)">
            
            <!-- Skeleton Loader UI (Displayed briefly when switching pages / loading) -->
            <div x-show="isLoading" class="space-y-6 animate-pulse" x-cloak>
                <div class="h-20 bg-white rounded-2xl border-2 border-gray-200 p-5 flex items-center justify-between">
                    <div class="space-y-2">
                        <div class="h-4 w-48 bg-gray-200 rounded-lg"></div>
                        <div class="h-3 w-72 bg-gray-100 rounded-lg"></div>
                    </div>
                    <div class="h-9 w-36 bg-gray-200 rounded-xl"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="h-24 bg-white rounded-2xl border-2 border-gray-200 p-5 space-y-3">
                        <div class="h-3 w-20 bg-gray-200 rounded-lg"></div>
                        <div class="h-6 w-12 bg-gray-300 rounded-lg"></div>
                    </div>
                    <div class="h-24 bg-white rounded-2xl border-2 border-gray-200 p-5 space-y-3">
                        <div class="h-3 w-20 bg-gray-200 rounded-lg"></div>
                        <div class="h-6 w-12 bg-gray-300 rounded-lg"></div>
                    </div>
                    <div class="h-24 bg-white rounded-2xl border-2 border-gray-200 p-5 space-y-3">
                        <div class="h-3 w-20 bg-gray-200 rounded-lg"></div>
                        <div class="h-6 w-12 bg-gray-300 rounded-lg"></div>
                    </div>
                    <div class="h-24 bg-white rounded-2xl border-2 border-gray-200 p-5 space-y-3">
                        <div class="h-3 w-20 bg-gray-200 rounded-lg"></div>
                        <div class="h-6 w-12 bg-gray-300 rounded-lg"></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border-2 border-gray-200 p-6 space-y-4">
                    <div class="h-10 bg-gray-100 rounded-xl w-full"></div>
                    <div class="h-12 bg-gray-50 rounded-xl w-full"></div>
                    <div class="h-12 bg-gray-50 rounded-xl w-full"></div>
                    <div class="h-12 bg-gray-50 rounded-xl w-full"></div>
                </div>
            </div>

            <!-- Actual Page Content (Smooth Fade In) -->
            <div x-show="!isLoading" x-transition.opacity.duration.300ms>
                <!-- Flash Message Alerts -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-200 flex items-center justify-between text-xs font-bold text-emerald-800 shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 text-base">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border-2 border-rose-200 flex items-center justify-between text-xs font-bold text-rose-800 shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-800 text-base">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(event, titleText = 'Apakah Anda yakin?', messageText = 'Data ini akan dihapus permanen!') {
            event.preventDefault();
            const form = event.target.closest('form');
            
            Swal.fire({
                title: titleText,
                text: messageText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#B91C1C',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus Data!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl border-2 border-gray-200 shadow-2xl font-sans',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-xs',
                    cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
    </script>
</body>
</html>
