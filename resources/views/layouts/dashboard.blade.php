<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Perpustakaan SMK PGRI Pekanbaru</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#FEF2F2',
                            100: '#FEE2E2',
                            200: '#FECACA',
                            300: '#FCA5A5',
                            400: '#F87171',
                            500: '#EF4444',
                            600: '#DC2626',
                            700: '#B91C1C',
                            800: '#991B1B',
                            900: '#7F1D1D',
                            950: '#450A0A',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [x-cloak] { display: none !important; }
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
          openManageBuku: true,
          openSirkulasi: true,
          openAdmin: true,
          openQuickSearch: false,
          quickQuery: '',
          quickResults: [],
          quickLoading: false,
          searchQuick() {
              if (this.quickQuery.trim().length < 2) {
                  this.quickResults = [];
                  return;
              }
              this.quickLoading = true;
              fetch('/api/buku/search-suggestions?q=' + encodeURIComponent(this.quickQuery))
                  .then(res => res.json())
                  .then(data => {
                      this.quickResults = data;
                      this.quickLoading = false;
                  })
                  .catch(() => { this.quickLoading = false; });
          }
      }"
      @keydown.window.ctrl.k.prevent="openQuickSearch = true; $nextTick(() => $refs.quickSearchInput.focus())"
      @keydown.window.meta.k.prevent="openQuickSearch = true; $nextTick(() => $refs.quickSearchInput.focus())">

    <div id="global-dashboard-skeleton" class="fixed inset-0 bg-gray-100 z-50 flex transition-opacity duration-300 pointer-events-auto">
        <div class="w-72 bg-white border-r-2 border-gray-200 p-5 space-y-6 hidden lg:block shrink-0">
            <div class="h-10 w-40 rounded-xl skeleton-shimmer"></div>
            <div class="space-y-3 pt-4">
                <div class="h-9 rounded-xl skeleton-shimmer"></div>
                <div class="h-9 rounded-xl skeleton-shimmer"></div>
                <div class="h-9 rounded-xl skeleton-shimmer"></div>
                <div class="h-9 rounded-xl skeleton-shimmer"></div>
                <div class="h-9 rounded-xl skeleton-shimmer"></div>
            </div>
        </div>
        <div class="flex-1 flex flex-col min-w-0">
            <div class="h-20 bg-white border-b-2 border-gray-200 px-6 flex items-center justify-between">
                <div class="h-6 w-48 rounded-md skeleton-shimmer"></div>
                <div class="flex items-center gap-3">
                    <div class="h-9 w-36 rounded-xl skeleton-shimmer"></div>
                    <div class="h-10 w-10 rounded-full skeleton-shimmer"></div>
                </div>
            </div>
            <div class="p-6 sm:p-8 space-y-6 flex-1 overflow-hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="h-28 rounded-2xl bg-white border-2 border-gray-100 skeleton-shimmer"></div>
                    <div class="h-28 rounded-2xl bg-white border-2 border-gray-100 skeleton-shimmer"></div>
                    <div class="h-28 rounded-2xl bg-white border-2 border-gray-100 skeleton-shimmer"></div>
                    <div class="h-28 rounded-2xl bg-white border-2 border-gray-100 skeleton-shimmer"></div>
                </div>
                <div class="h-80 rounded-2xl bg-white border-2 border-gray-100 skeleton-shimmer"></div>
            </div>
        </div>
    </div>

    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-950/60 backdrop-blur-xs z-40 lg:hidden" x-cloak></div>

    <div class="flex h-screen overflow-hidden">
        <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r-2 border-gray-200/90 flex flex-col justify-between transform transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-lg lg:shadow-none"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            <div class="flex flex-col flex-1 min-h-0">
                <div class="h-20 flex items-center justify-between px-5 border-b-2 border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI Pekanbaru" class="w-10 h-10 object-contain drop-shadow-xs">
                        <div class="leading-tight">
                            <span class="text-sm font-black text-gray-900 tracking-tight block">SMK PGRI</span>
                            <span class="text-[10px] font-extrabold text-brand-700 tracking-wider uppercase block">Perpustakaan</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-xl text-gray-400 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="p-3 space-y-2 overflow-y-auto flex-1 text-xs font-bold">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 border {{ request()->routeIs('admin.dashboard') ? 'bg-brand-700 text-white border-brand-800 shadow-md transform translate-x-1' : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-brand-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>

                    <div class="space-y-1">
                        <button type="button" @click="openManageBuku = !openManageBuku"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 transition duration-200 border border-transparent font-extrabold text-xs">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>MANAGE BUKU</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 transform transition-transform duration-200" :class="openManageBuku ? 'rotate-180 text-brand-700' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openManageBuku" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                            <a href="{{ route('admin.buku') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.buku*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.buku*') ? 'bg-brand-700' : 'bg-gray-300' }}"></span>
                                <span>Koleksi Buku</span>
                            </a>
                            <a href="{{ route('admin.kategori') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.kategori*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.kategori*') ? 'bg-brand-700' : 'bg-gray-300' }}"></span>
                                <span>Kategori</span>
                            </a>
                            <a href="{{ route('admin.penulis') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.penulis*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.penulis*') ? 'bg-brand-700' : 'bg-gray-300' }}"></span>
                                <span>Penulis</span>
                            </a>
                            <a href="{{ route('admin.penerbit') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.penerbit*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.penerbit*') ? 'bg-brand-700' : 'bg-gray-300' }}"></span>
                                <span>Penerbit</span>
                            </a>
                            <a href="{{ route('admin.rak') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.rak*') ? 'bg-brand-50 text-brand-700 border-brand-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.rak*') ? 'bg-brand-700' : 'bg-gray-300' }}"></span>
                                <span>Rak & Laci Buku</span>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button type="button" @click="openSirkulasi = !openSirkulasi"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 transition duration-200 border border-transparent font-extrabold text-xs">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span>SIRKULASI PINJAM</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 transform transition-transform duration-200" :class="openSirkulasi ? 'rotate-180 text-brand-700' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openSirkulasi" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                            <a href="{{ route('admin.peminjaman') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.peminjaman') ? 'bg-amber-50 text-amber-900 border-amber-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.peminjaman') ? 'bg-amber-600' : 'bg-gray-300' }}"></span>
                                <span>Peminjaman Aktif</span>
                            </a>
                            <a href="{{ route('admin.riwayat') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.riwayat*') ? 'bg-amber-50 text-amber-900 border-amber-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.riwayat*') ? 'bg-amber-600' : 'bg-gray-300' }}"></span>
                                <span>Riwayat Transaksi</span>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button type="button" @click="openAdmin = !openAdmin"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 transition duration-200 border border-transparent font-extrabold text-xs">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>PENGATURAN & AKUN</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 transform transition-transform duration-200" :class="openAdmin ? 'rotate-180 text-brand-700' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openAdmin" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                            <a href="{{ route('admin.anggota') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.anggota*') ? 'bg-emerald-50 text-emerald-900 border-emerald-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.anggota*') ? 'bg-emerald-600' : 'bg-gray-300' }}"></span>
                                <span>Akun Pengelola</span>
                            </a>
                            <a href="{{ route('admin.pengaturan') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.pengaturan*') ? 'bg-emerald-50 text-emerald-900 border-emerald-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.pengaturan*') ? 'bg-emerald-600' : 'bg-gray-300' }}"></span>
                                <span>Pengaturan Sistem</span>
                            </a>
                            <a href="{{ route('admin.audit-log') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.audit-log*') ? 'bg-gray-100 text-gray-900 border-gray-300 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.audit-log*') ? 'bg-gray-900' : 'bg-gray-300' }}"></span>
                                <span>Audit Log</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="p-3 border-t-2 border-gray-100 bg-gray-50/80">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border-2 border-gray-200 shadow-xs">
                    <div class="min-w-0 pr-2">
                        <p class="text-xs font-black text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold {{ auth()->user()->isSuperAdmin() ? 'text-amber-700' : 'text-brand-700' }} flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full {{ auth()->user()->isSuperAdmin() ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            <span>{{ auth()->user()->isSuperAdmin() ? 'Super Administrator' : 'Admin Perpustakaan' }}</span>
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
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-xs sm:text-base font-black text-gray-900 truncate tracking-tight leading-tight">@yield('page_heading', 'Overview')</h1>
                        <p class="text-[10px] sm:text-[11px] text-gray-500 font-medium truncate hidden sm:block">Perpustakaan SMK PGRI Pekanbaru</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="openQuickSearch = true; $nextTick(() => $refs.quickSearchInput.focus())" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-gray-500 bg-gray-50 hover:bg-gray-100 hover:text-gray-900 rounded-xl border border-gray-200 transition">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span class="hidden md:inline">Cari Cepat Buku & Rak</span>
                        <kbd class="hidden sm:inline-block px-1.5 py-0.5 bg-gray-200 text-gray-600 rounded text-[10px] font-mono font-bold">Ctrl+K</kbd>
                    </button>

                    <a href="{{ route('katalog') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-gray-700 bg-gray-50 hover:bg-brand-50 hover:text-brand-700 rounded-xl border border-gray-200 transition">
                        <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Katalog OPAC</span>
                    </a>
                    <span class="h-5 w-2px bg-gray-200 hidden sm:block"></span>
                    <div class="text-right hidden sm:block">
                        <span class="text-xs font-black text-gray-800 block">{{ date('l, d M Y') }}</span>
                        <span class="text-[10px] text-emerald-600 font-bold flex items-center justify-end gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Sistem Aktif</span>
                        </span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="button" onclick="confirmLogout(event)" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-extrabold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
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

    <div x-show="openQuickSearch" @click.self="openQuickSearch = false" class="fixed inset-0 z-50 flex items-start justify-center bg-gray-950/70 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto" x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-2xl w-full max-h-[85vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all mt-10">
            <div class="p-4 border-b border-gray-100 flex items-center gap-3 bg-gray-50/80">
                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-ref="quickSearchInput" x-model="quickQuery" @input.debounce.200ms="searchQuick()" placeholder="Ketik judul buku, ISBN, atau penulis untuk cek rak & stok..." class="w-full text-xs sm:text-sm font-bold text-gray-900 bg-transparent focus:outline-none">
                <div x-show="quickLoading" x-cloak>
                    <svg class="animate-spin w-4 h-4 text-brand-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
                <button @click="openQuickSearch = false" class="text-xs font-bold text-gray-400 hover:text-gray-700 px-2 py-1 bg-gray-200/70 rounded-lg">ESC</button>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-2 text-xs">
                <template x-if="quickResults.length > 0">
                    <div>
                        <div class="px-2 py-1 text-[10px] font-black text-gray-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Hasil Pencarian Cepat</span>
                            <span class="text-emerald-600 font-bold" x-text="quickResults.length + ' Buku Ditemukan'"></span>
                        </div>
                        <div class="space-y-1.5 mt-1">
                            <template x-for="book in quickResults" :key="book.id">
                                <a :href="book.detail_url" target="_blank" class="p-3 rounded-2xl border border-gray-200 hover:border-brand-300 hover:bg-brand-50/50 flex items-center gap-3 transition group">
                                    <div class="w-10 h-14 bg-gray-100 rounded-lg overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                                        <template x-if="book.cover_url">
                                            <img :src="book.cover_url" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!book.cover_url">
                                            <div class="w-full h-full bg-brand-700 text-white font-black text-xs flex items-center justify-center" x-text="book.judul.substr(0, 1)"></div>
                                        </template>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-extrabold text-gray-900 text-xs truncate group-hover:text-brand-700" x-text="book.judul"></h4>
                                        <p class="text-[11px] text-gray-500 truncate" x-text="book.penulis + ' • ' + book.kategori"></p>
                                        <div class="flex items-center gap-2 mt-1.5 text-[10px]">
                                            <span class="px-2 py-0.5 rounded-md bg-gray-100 font-bold text-gray-800 border border-gray-200" x-text="'📍 ' + book.rak + ' (' + book.laci + ')'"></span>
                                            <span class="px-2 py-0.5 rounded-md font-black" :class="book.available_quantity > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'" x-text="'Stok: ' + book.available_quantity + ' / ' + book.total_quantity + ' Eks'"></span>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-700 shrink-0 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="quickQuery.trim().length >= 2 && quickResults.length === 0 && !quickLoading">
                    <div class="py-12 text-center text-gray-400 space-y-2">
                        <svg class="w-8 h-8 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-bold">Tidak ada buku ditemukan untuk "<span x-text="quickQuery"></span>"</p>
                    </div>
                </template>

                <template x-if="quickQuery.trim().length < 2">
                    <div class="py-10 text-center text-gray-400 space-y-1">
                        <p class="font-bold text-xs text-gray-500">Ketik minimal 2 karakter untuk memulai pencarian cepat</p>
                        <p class="text-[10px] text-gray-400">Pencarian mencakup judul buku, pengarang, nomor rak, dan laci</p>
                    </div>
                </template>
            </div>
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

        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('global-dashboard-skeleton');
            if (skeleton) {
                setTimeout(() => {
                    skeleton.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => skeleton.remove(), 350);
                }, 120);
            }
        });
    </script>
</body>
</html>
