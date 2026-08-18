<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Perpustakaan') - {{ $pengaturan['nama_perpustakaan'] ?? 'SMK PGRI Pekanbaru' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .skeleton-shimmer {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: shimmer 1.2s infinite ease-in-out;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="h-full font-sans text-gray-800 antialiased flex flex-col bg-gray-100 selection:bg-brand-700 selection:text-white"
      x-data="{
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
          toggleSidebar() {
              if (window.innerWidth < 1024) {
                  this.sidebarOpen = !this.sidebarOpen;
              } else {
                  this.sidebarCollapsed = !this.sidebarCollapsed;
                  localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
              }
          },
          openManageBuku: {{ request()->routeIs('admin.buku*', 'admin.data-buku*', 'admin.kategori*', 'admin.penulis*', 'admin.penerbit*', 'admin.rak*') ? 'true' : 'false' }},
          openSirkulasi: {{ request()->routeIs('admin.peminjaman*', 'admin.riwayat*') ? 'true' : 'false' }},
          openAdmin: {{ request()->routeIs('admin.anggota*', 'admin.pengaturan*', 'admin.audit-log*') ? 'true' : 'false' }}
      }">

    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-950/60 backdrop-blur-xs z-40 lg:hidden" x-cloak></div>

    <div class="flex h-screen overflow-hidden">
        <aside class="fixed inset-y-0 left-0 z-50 bg-white border-r-2 border-gray-200/90 flex flex-col justify-between transform transition-all duration-300 ease-in-out lg:static shrink-0 shadow-lg lg:shadow-none"
               :class="{
                   'translate-x-0 w-72': sidebarOpen,
                   '-translate-x-full lg:translate-x-0': !sidebarOpen,
                   'lg:w-72': !sidebarCollapsed,
                   'lg:w-20': sidebarCollapsed
               }">

            <div class="flex flex-col flex-1 min-h-0">
                <div class="h-20 flex items-center border-b-2 border-gray-100 bg-gradient-to-r from-gray-50 to-white shrink-0"
                     :class="sidebarCollapsed ? 'lg:flex-col lg:justify-center lg:gap-1 px-2' : 'justify-between px-4'">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0" :title="sidebarCollapsed ? 'Perpustakaan SMK PGRI' : ''">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI Pekanbaru" class="w-9 h-9 object-contain drop-shadow-xs shrink-0">
                        <div class="leading-tight truncate" x-show="!sidebarCollapsed || sidebarOpen">
                            <span class="text-sm font-black text-gray-900 tracking-tight block truncate">{{ $pengaturan['nama_sekolah'] ?? 'SMK PGRI' }}</span>
                            <span class="text-[10px] font-extrabold text-brand-700 tracking-wider uppercase block">Perpustakaan</span>
                        </div>
                    </a>
                    <div class="flex items-center gap-1">
                        <button @click="toggleSidebar()" class="hidden lg:flex p-1.5 rounded-xl text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition shrink-0 cursor-pointer" :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Kecilkan Sidebar'" aria-label="Buka/Tutup Sidebar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-xl text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition shrink-0" aria-label="Tutup Sidebar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <nav class="p-3 space-y-2 overflow-y-auto flex-1 text-xs font-bold">
                    <a href="{{ route('admin.dashboard') }}" title="Dashboard"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.dashboard') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1 font-black' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700 font-bold' }}"
                       :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.temukan-buku') }}" title="Temukan Buku"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.temukan-buku*') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1 font-black' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700 font-bold' }}"
                       :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Temukan Buku</span>
                    </a>

                    <div class="space-y-1">
                        <button type="button" @click="openManageBuku = !openManageBuku" title="MANAGE BUKU"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 transition duration-200 border border-transparent font-extrabold text-xs"
                                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">MANAGE BUKU</span>
                            </div>
                            <svg x-show="!sidebarCollapsed || sidebarOpen" class="w-3.5 h-3.5 text-gray-400 transform transition-transform duration-200 shrink-0" :class="openManageBuku ? 'rotate-180 text-brand-700' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openManageBuku" x-collapse class="space-y-1" :class="sidebarCollapsed ? 'lg:pl-0' : 'pl-4 pr-1 py-1'">
                            <a href="{{ route('admin.data-buku') }}" title="Data Buku"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.data-buku*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <i class="fa-solid fa-book-open text-brand-700 text-sm shrink-0"></i>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Data Buku</span>
                            </a>
                            <a href="{{ route('admin.buku') }}" title="Koleksi Buku"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.buku*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Koleksi Buku</span>
                            </a>
                            <a href="{{ route('admin.kategori') }}" title="Kategori"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.kategori*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Kategori</span>
                            </a>
                            <a href="{{ route('admin.penulis') }}" title="Penulis"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.penulis*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Penulis</span>
                            </a>
                            <a href="{{ route('admin.penerbit') }}" title="Penerbit"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.penerbit*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Penerbit</span>
                            </a>
                            <a href="{{ route('admin.rak') }}" title="Rak &amp; Laci Buku"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.rak*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Rak &amp; Laci Buku</span>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button type="button" @click="openSirkulasi = !openSirkulasi" title="SIRKULASI PINJAM"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 transition duration-200 border border-transparent font-extrabold text-xs"
                                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">SIRKULASI PINJAM</span>
                            </div>
                            <div class="flex items-center gap-1.5" x-show="!sidebarCollapsed || sidebarOpen">
                                @if(($pendingRequestsCount ?? 0) > 0)
                                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                @endif
                                <svg class="w-3.5 h-3.5 text-gray-400 transform transition-transform duration-200 shrink-0" :class="openSirkulasi ? 'rotate-180 text-brand-700' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        <div x-show="openSirkulasi" x-collapse class="space-y-1" :class="sidebarCollapsed ? 'lg:pl-0' : 'pl-4 pr-1 py-1'">
                            <a href="{{ route('admin.peminjaman.request') }}" title="Request Peminjaman"
                               class="flex items-center justify-between px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.peminjaman.request*') ? 'bg-amber-50 text-amber-900 border-amber-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <i class="fa-solid fa-inbox text-amber-600 text-sm shrink-0"></i>
                                    <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Request Peminjaman</span>
                                </div>
                                @if(($pendingRequestsCount ?? 0) > 0)
                                    <span x-show="!sidebarCollapsed || sidebarOpen" class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-rose-600 text-white shrink-0">
                                        {{ $pendingRequestsCount }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.peminjaman') }}" title="Peminjaman Aktif"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.peminjaman') ? 'bg-amber-50 text-amber-900 border-amber-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Peminjaman Aktif</span>
                            </a>
                            <a href="{{ route('admin.riwayat') }}" title="Riwayat Transaksi"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.riwayat*') ? 'bg-amber-50 text-amber-900 border-amber-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Riwayat Transaksi</span>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button type="button" @click="openAdmin = !openAdmin" title="PENGATURAN &amp; AKUN"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 transition duration-200 border border-transparent font-extrabold text-xs"
                                :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">PENGATURAN &amp; AKUN</span>
                            </div>
                            <svg x-show="!sidebarCollapsed || sidebarOpen" class="w-3.5 h-3.5 text-gray-400 transform transition-transform duration-200 shrink-0" :class="openAdmin ? 'rotate-180 text-brand-700' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openAdmin" x-collapse class="space-y-1" :class="sidebarCollapsed ? 'lg:pl-0' : 'pl-4 pr-1 py-1'">
                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('admin.anggota') }}" title="Akun Pengelola"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.anggota*') ? 'bg-emerald-50 text-emerald-900 border-emerald-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Akun Pengelola</span>
                                </a>
                            @endif
                            <a href="{{ route('admin.pengaturan') }}" title="Pengaturan Sistem"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.pengaturan*') ? 'bg-emerald-50 text-emerald-900 border-emerald-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Pengaturan Sistem</span>
                            </a>
                            <a href="{{ route('admin.audit-log') }}" title="Audit Log"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.audit-log*') ? 'bg-gray-100 text-gray-900 border-gray-300 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                <svg class="w-4 h-4 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span x-show="!sidebarCollapsed || sidebarOpen" class="truncate">Audit Log</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="p-3 border-t-2 border-gray-100 bg-gray-50/80 shrink-0">
                <div class="flex items-center p-2.5 rounded-xl bg-white border-2 border-gray-200 shadow-xs"
                     :class="sidebarCollapsed ? 'lg:justify-center lg:p-2' : 'justify-between'">
                    <div class="min-w-0 pr-2" x-show="!sidebarCollapsed || sidebarOpen">
                        <p class="text-xs font-black text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold {{ auth()->user()->isSuperAdmin() ? 'text-amber-700' : 'text-brand-700' }} flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full {{ auth()->user()->isSuperAdmin() ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            <span>{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}</span>
                        </p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmLogout(event)" title="Keluar dari Dashboard" class="p-2 text-gray-400 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition border border-transparent hover:border-rose-200 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="h-20 bg-white border-b-2 border-gray-200/90 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 shrink-0 shadow-2xs">
                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 shrink-0 transition cursor-pointer" title="Buka Sidebar" aria-label="Buka Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-xs sm:text-base font-black text-gray-900 truncate tracking-tight leading-tight">@yield('page_heading', 'Overview')</h1>
                        <p class="text-[10px] sm:text-[11px] text-gray-500 font-medium truncate hidden sm:block">{{ $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <span class="text-xs font-black text-gray-800 block">{{ date('l, d M Y') }}</span>
                        <span class="text-[10px] text-emerald-600 font-bold flex items-center justify-end gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Sistem Aktif</span>
                        </span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-100/60" x-data="{ isLoading: true }" x-init="setTimeout(() => isLoading = false, 150)">
                @if(session('success'))
                    <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-4 rounded-2xl bg-rose-50 border-2 border-rose-200 text-rose-900 text-xs font-bold flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 p-4 rounded-2xl bg-rose-50 border-2 border-rose-200 text-rose-900 text-xs font-bold space-y-1 shadow-sm">
                        <div class="flex items-center gap-2 text-rose-700">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="font-extrabold">Terjadi Kesalahan Validasi:</span>
                        </div>
                        <ul class="list-disc list-inside text-[11px] text-rose-800 space-y-0.5 pl-2">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
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
                confirmButtonText: 'Ya, Lanjutkan!',
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

        function confirmLogout(event) {
            event.preventDefault();
            const form = event.target.closest('form');

            Swal.fire({
                title: 'Konfirmasi Keluar?',
                text: 'Apakah Anda yakin ingin keluar dari dashboard admin perpustakaan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#B91C1C',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    @stack('scripts')
</body>
</html>
