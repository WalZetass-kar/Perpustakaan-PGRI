<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Data Buku') - {{ $pengaturan['nama_perpustakaan'] ?? 'Sistem Informasi Perpustakaan' }}</title>

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

    <script defer src="{{ asset('vendor/alpine/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full font-sans text-gray-800 antialiased bg-gray-100 selection:bg-brand-700 selection:text-white">

    {{-- Top bar ringkas: hanya branding, tanpa sidebar dashboard --}}
    <header class="sticky top-0 z-40 bg-white border-b-2 border-gray-200 shadow-sm">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 py-2.5 flex items-center gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-lg object-contain shrink-0" onerror="this.style.display='none'">
                <div class="min-w-0">
                    <span class="text-xs font-black text-gray-900 tracking-tight block truncate">{{ $pengaturan['nama_sekolah'] ?? 'Perpustakaan' }}</span>
                    <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Mode Tampilan Rak &middot; Baca Saja</span>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 py-5">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
