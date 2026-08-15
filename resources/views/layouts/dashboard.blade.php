<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Perpustakaan') | Perpustakaan SMK PGRI Pekanbaru</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
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
                    },
                    boxShadow: {
                        '2xs': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                        'xs': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
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
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @keyframes skeleton-shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton-shimmer {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.5s infinite ease-in-out;
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-gray-50 flex overflow-hidden relative"
      x-data="{
          sidebarOpen: false,
          openManageBuku: {{ request()->routeIs('admin.buku*', 'admin.kategori*', 'admin.penulis*', 'admin.penerbit*', 'admin.rak*') ? 'true' : 'false' }},
          openSirkulasi: {{ request()->routeIs('admin.peminjaman*', 'admin.riwayat*') ? 'true' : 'false' }},
          openSetting: {{ request()->routeIs('admin.pengaturan*', 'admin.anggota*', 'admin.audit-log*') ? 'true' : 'false' }}
      }">

    <div id="global-dashboard-skeleton" class="fixed inset-0 z-[9999] bg-gray-50 flex overflow-hidden transition-opacity duration-300 pointer-events-auto">

        <div class="w-72 bg-white border-r-2 border-gray-200 hidden lg:flex flex-col p-5 space-y-6 shrink-0">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                <div class="w-12 h-12 rounded-xl skeleton-shimmer shrink-0"></div>
                <div class="space-y-2 flex-1">
                    <div class="h-4 w-32 rounded-md skeleton-shimmer"></div>
                    <div class="h-3 w-20 rounded-md skeleton-shimmer"></div>
                </div>
            </div>
            <div class="space-y-3 flex-1">
                <div class="h-10 w-full rounded-xl skeleton-shimmer"></div>
                <div class="h-10 w-full rounded-xl skeleton-shimmer"></div>
                <div class="h-10 w-full rounded-xl skeleton-shimmer"></div>
                <div class="h-10 w-full rounded-xl skeleton-shimmer"></div>
                <div class="h-10 w-full rounded-xl skeleton-shimmer"></div>
            </div>
            <div class="h-14 w-full rounded-xl skeleton-shimmer"></div>
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
                            <span>Rak Perpustakaan</span>
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
                        <svg class="w-3.5 h-3.5 text-gray-400 transform transition-transform duration-200" :class="openSirkulasi ? 'rotate-180 text-amber-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="openSirkulasi" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                        <a href="{{ route('admin.peminjaman') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.peminjaman*') ? 'bg-amber-50 text-amber-800 border-amber-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.peminjaman*') ? 'bg-amber-600' : 'bg-gray-300' }}"></span>
                            <span>Peminjaman Aktif</span>
                        </a>
                        <a href="{{ route('admin.riwayat') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.riwayat*') ? 'bg-amber-50 text-amber-800 border-amber-200 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.riwayat*') ? 'bg-amber-600' : 'bg-gray-300' }}"></span>
                            <span>Riwayat Peminjaman</span>
                        </a>
                    </div>
                </div>

                <div class="space-y-1">
                    <button type="button" @click="openSetting = !openSetting"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 transition duration-200 border border-transparent font-extrabold text-xs">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>SETTING</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400 transform transition-transform duration-200" :class="openSetting ? 'rotate-180 text-gray-700' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="openSetting" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                        <a href="{{ route('admin.pengaturan') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.pengaturan*') ? 'bg-gray-100 text-gray-900 border-gray-300 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.pengaturan*') ? 'bg-gray-900' : 'bg-gray-300' }}"></span>
                            <span>Pengaturan Sistem</span>
                        </a>
                        <a href="{{ route('admin.anggota') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition text-xs font-bold border {{ request()->routeIs('admin.anggota*') ? 'bg-gray-100 text-gray-900 border-gray-300 shadow-2xs font-black' : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.anggota*') ? 'bg-gray-900' : 'bg-gray-300' }}"></span>
                            <span>Data Pengguna / Anggota</span>
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
                    <p class="text-[10px] font-bold text-gray-500 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Pengelola Perpustakaan</span>
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
