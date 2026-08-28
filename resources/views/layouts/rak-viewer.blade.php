<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="theme-color" content="#b91c1c">
    <title>@yield('title', 'Data Buku') - {{ $pengaturan['nama_perpustakaan'] ?? 'Sistem Informasi Perpustakaan' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="stylesheet" href="{{ asset('vendor/tailwind/tailwind.min.css') }}">

    <script defer src="{{ asset('vendor/alpine/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    @stack('styles')

    @include('partials.gaya-formulir')

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
