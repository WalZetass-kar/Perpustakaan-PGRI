    <!DOCTYPE html>
<html lang="id" class="scroll-smooth" x-data="{ mobileMenuOpen: false }">
<head>
    @php
        /*
        |----------------------------------------------------------------------
        | Konfigurasi SEO terpusat
        |----------------------------------------------------------------------
        | Setiap halaman publik boleh menimpa nilai di bawah lewat @section:
        |   title, meta_description, meta_keywords, robots, canonical,
        |   og_type, og_image, og_image_alt
        | dan menambah structured data lewat @push('schema').
        */
        $seoSite   = trim($pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru');
        $seoSchool = trim($pengaturan['nama_sekolah'] ?? 'SMK PGRI Pekanbaru');
        $seoHome   = url('/');

        /*
        | Laravel meng-escape isi @section('nama', 'nilai') saat disimpan, jadi
        | nilainya harus di-decode dulu -- kalau tidak, {{ }} akan meng-escape
        | untuk kedua kalinya dan "&" berubah jadi "&amp;amp;".
        */
        $seoSection = fn ($name) => trim(html_entity_decode(
            $__env->yieldContent($name), ENT_QUOTES | ENT_HTML5, 'UTF-8'
        ));

        // Judul halaman + suffix nama perpustakaan (tidak digandakan bila sudah ada).
        $seoPageTitle = $seoSection('title');
        $seoTitle = $seoPageTitle === ''
            ? $seoSite . ' - ' . $seoSchool
            : (\Illuminate\Support\Str::contains($seoPageTitle, $seoSite)
                ? $seoPageTitle
                : $seoPageTitle . ' | ' . $seoSite);

        $seoDescription = $seoSection('meta_description');
        if ($seoDescription === '') {
            $seoDescription = 'Katalog OPAC ' . $seoSite . ' ' . $seoSchool . '. Telusuri koleksi buku, modul kejuruan, dan referensi digital lengkap dengan lokasi rak serta pengajuan peminjaman online.';
        }
        // Dipotong di batas kata supaya cuplikan di Google tidak terputus di tengah kata.
        $seoDescription = \Illuminate\Support\Str::limit(
            trim(preg_replace('/\s+/', ' ', strip_tags($seoDescription))), 160, '...', true
        );

        $seoKeywords = $seoSection('meta_keywords');
        if ($seoKeywords === '') {
            $seoKeywords = 'perpustakaan sekolah, katalog opac, perpustakaan digital, ' . mb_strtolower($seoSchool) . ', peminjaman buku online, koleksi modul kejuruan';
        }

        $seoRobots    = $seoSection('robots') ?: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        $seoCanonical = $seoSection('canonical') ?: url()->current();
        $seoImage     = $seoSection('og_image') ?: asset('images/logo.png');
        $seoImageAlt  = $seoSection('og_image_alt') ?: 'Logo ' . $seoSite;
        $seoType      = $seoSection('og_type') ?: 'website';

        /*
        | Data kontak hanya ikut ke structured data kalau memang tersimpan di
        | tabel pengaturan -- jangan pernah mengirim data placeholder ke Google.
        */
        $seoAddress = trim($pengaturan['alamat'] ?? '');
        $seoEmail   = trim($pengaturan['email_perpustakaan'] ?? '');
        $seoPhone   = trim($pengaturan['telepon'] ?? '');

        $seoOrganization = array_filter([
            '@type'         => ['Library', 'EducationalOrganization'],
            '@id'           => $seoHome . '#organization',
            'name'          => $seoSite,
            'alternateName' => $seoSchool,
            'url'           => $seoHome,
            'logo'          => asset('images/logo.png'),
            'image'         => asset('images/logo.png'),
            'description'   => 'Perpustakaan digital ' . $seoSchool . ' dengan layanan katalog OPAC, penelusuran lokasi rak dan laci, serta sirkulasi peminjaman buku.',
            'email'         => $seoEmail ?: null,
            'telephone'     => $seoPhone ?: null,
            'address'       => $seoAddress ? [
                '@type'          => 'PostalAddress',
                'streetAddress'  => $seoAddress,
                'addressCountry' => 'ID',
            ] : null,
            'parentOrganization' => [
                '@type' => 'EducationalOrganization',
                'name'  => $seoSchool,
            ],
        ]);

        $seoWebsite = [
            '@type'      => 'WebSite',
            '@id'        => $seoHome . '#website',
            'url'        => $seoHome,
            'name'       => $seoSite,
            'inLanguage' => 'id-ID',
            'publisher'  => ['@id' => $seoHome . '#organization'],
            // Sitelinks search box: Google boleh mengarahkan pencarian ke OPAC.
            'potentialAction' => [
                '@type'  => 'SearchAction',
                'target' => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => route('katalog') . '?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];

        $seoJsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG;
    @endphp

    {{-- ============================ Dasar ============================ --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    {{-- ====================== Title & deskripsi ====================== --}}
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <meta name="googlebot" content="{{ $seoRobots }}">
    <meta name="author" content="{{ $seoSchool }}">
    <meta name="publisher" content="{{ $seoSchool }}">
    <meta name="language" content="id">
    <meta name="geo.region" content="ID-RI">
    <meta name="geo.placename" content="Pekanbaru">

    <link rel="canonical" href="{{ $seoCanonical }}">
    <link rel="alternate" hreflang="id" href="{{ $seoCanonical }}">
    <link rel="alternate" hreflang="x-default" href="{{ $seoCanonical }}">

    {{-- ==================== Open Graph (Facebook/WA) ================== --}}
    <meta property="og:type" content="{{ $seoType }}">
    <meta property="og:site_name" content="{{ $seoSite }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:secure_url" content="{{ $seoImage }}">
    <meta property="og:image:alt" content="{{ $seoImageAlt }}">
    <meta property="og:locale" content="id_ID">
    @hasSection('og_extra')
        @yield('og_extra')
    @endif

    {{-- ============================ Twitter =========================== --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <meta name="twitter:image:alt" content="{{ $seoImageAlt }}">

    {{-- ======================== Ikon & PWA-ish ======================== --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <meta name="theme-color" content="#b91c1c">
    <meta name="color-scheme" content="light">
    <meta name="application-name" content="{{ $seoSite }}">
    <meta name="apple-mobile-web-app-title" content="{{ $seoSite }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- =================== Structured data (JSON-LD) ================== --}}
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => [$seoOrganization, $seoWebsite]], $seoJsonFlags) !!}</script>
    @stack('schema')

    {{-- ================= Performance: hint koneksi CDN ================ --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://unpkg.com">

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
                        }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script defer src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }

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

    @stack('styles')
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen font-sans relative selection:bg-brand-700 selection:text-white">

    <div id="page-skeleton-loader" class="fixed inset-0 z-50 bg-gray-50 flex flex-col pointer-events-none transition-opacity duration-300">
        <div class="h-20 bg-white border-b border-gray-200 px-4 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gray-200 skeleton-shimmer"></div>
                <div class="space-y-1.5">
                    <div class="w-36 h-4 bg-gray-200 rounded-md skeleton-shimmer"></div>
                    <div class="w-24 h-2.5 bg-gray-200 rounded-md skeleton-shimmer"></div>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-6">
                <div class="w-20 h-4 bg-gray-200 rounded-md skeleton-shimmer"></div>
                <div class="w-24 h-4 bg-gray-200 rounded-md skeleton-shimmer"></div>
                <div class="w-20 h-4 bg-gray-200 rounded-md skeleton-shimmer"></div>
            </div>
            <div class="w-28 h-9 bg-gray-200 rounded-xl skeleton-shimmer"></div>
        </div>

        <div class="max-w-7xl w-full mx-auto p-4 sm:p-8 space-y-6 flex-1">
            <div class="w-full h-44 sm:h-56 bg-gray-200 rounded-3xl skeleton-shimmer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div class="h-72 bg-white rounded-2xl border border-gray-200 p-4 space-y-3">
                    <div class="w-full h-40 bg-gray-200 rounded-xl skeleton-shimmer"></div>
                    <div class="w-3/4 h-4 bg-gray-200 rounded skeleton-shimmer"></div>
                    <div class="w-1/2 h-3 bg-gray-200 rounded skeleton-shimmer"></div>
                </div>
                <div class="h-72 bg-white rounded-2xl border border-gray-200 p-4 space-y-3 hidden sm:block">
                    <div class="w-full h-40 bg-gray-200 rounded-xl skeleton-shimmer"></div>
                    <div class="w-3/4 h-4 bg-gray-200 rounded skeleton-shimmer"></div>
                    <div class="w-1/2 h-3 bg-gray-200 rounded skeleton-shimmer"></div>
                </div>
                <div class="h-72 bg-white rounded-2xl border border-gray-200 p-4 space-y-3 hidden md:block">
                    <div class="w-full h-40 bg-gray-200 rounded-xl skeleton-shimmer"></div>
                    <div class="w-3/4 h-4 bg-gray-200 rounded skeleton-shimmer"></div>
                    <div class="w-1/2 h-3 bg-gray-200 rounded skeleton-shimmer"></div>
                </div>
                <div class="h-72 bg-white rounded-2xl border border-gray-200 p-4 space-y-3 hidden lg:block">
                    <div class="w-full h-40 bg-gray-200 rounded-xl skeleton-shimmer"></div>
                    <div class="w-3/4 h-4 bg-gray-200 rounded skeleton-shimmer"></div>
                    <div class="w-1/2 h-3 bg-gray-200 rounded skeleton-shimmer"></div>
                </div>
            </div>
        </div>
    </div>

    <header class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 transition duration-300 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            <a href="{{ route('home') }}" class="flex items-center gap-3.5 shrink-0 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI" class="w-12 h-12 object-contain transform group-hover:scale-105 transition duration-300 drop-shadow-xs">
                <div>
                    <span class="text-base sm:text-lg font-black text-gray-900 leading-tight block group-hover:text-brand-700 transition">Sistem Perpustakaan</span>
                    <span class="text-xs text-gray-500 font-bold tracking-wider uppercase block">{{ $pengaturan['nama_sekolah'] ?? 'SMK PGRI Pekanbaru' }}</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-bold">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-brand-700 border-b-2 border-brand-700 py-6' : 'text-gray-700 hover:text-brand-700 transition' }}">Beranda</a>
                <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') || request()->routeIs('buku.detail') ? 'text-brand-700 border-b-2 border-brand-700 py-6' : 'text-gray-700 hover:text-brand-700 transition' }}">Katalog OPAC</a>
                <a href="{{ route('home') }}#pusat-data-section" class="text-gray-700 hover:text-brand-700 transition">Pusat Data</a>
            </nav>

            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('katalog') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs sm:text-sm font-bold text-gray-700 hover:text-brand-700 hover:bg-gray-50 rounded-xl transition border border-gray-200">
                    <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari Buku</span>
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-extrabold text-white bg-brand-700 rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <span>Dashboard Pengelola</span>
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-extrabold text-white bg-brand-700 rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Login Admin</span>
                    </a>
                @endauth
            </div>

            <div class="flex md:hidden items-center gap-2">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2.5 rounded-xl text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none" aria-label="Toggle Mobile Menu">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-gray-100 bg-white px-5 pt-3 pb-5 space-y-3 shadow-lg">
            <a href="{{ route('home') }}" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-gray-800 hover:bg-brand-50 hover:text-brand-700">Beranda</a>
            <a href="{{ route('katalog') }}" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-gray-800 hover:bg-brand-50 hover:text-brand-700">Katalog OPAC</a>
            <a href="{{ route('home') }}#pusat-data-section" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-gray-800 hover:bg-brand-50 hover:text-brand-700">Pusat Data</a>
            <div class="pt-3 border-t border-gray-100 space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full py-2.5 text-center bg-brand-700 text-white font-extrabold text-xs rounded-xl block">
                        Dashboard Pengelola
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full py-2.5 text-center bg-brand-700 text-white font-extrabold text-xs rounded-xl block">
                        Login Admin
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-black border-t border-white/10 py-12 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

                <div class="space-y-3 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI" class="w-10 h-10 object-contain drop-shadow-xs">
                        <div>
                            <span class="text-sm font-black text-white block">{{ $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru' }}</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $pengaturan['nama_sekolah'] ?? 'SMK PGRI Pekanbaru' }}</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-xs leading-relaxed max-w-sm">
                        Sistem Informasi Perpustakaan Digital Sekolah (Inlislite) terintegrasi untuk katalogisasi, lokasi rak, laci, dan sirkulasi peminjaman buku.
                    </p>
                </div>

                <div class="space-y-2">
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Informasi & Layanan</h4>
                    <ul class="space-y-1.5 text-gray-300">
                        <li><a href="{{ route('katalog') }}" class="hover:text-brand-400 transition">Pencarian Koleksi Modul</a></li>
                        <li><span class="text-gray-400">{{ $pengaturan['jam_operasional'] ?? 'Senin - Jumat: 07.00 - 15.30 WIB' }}</span></li>
                        <li><span class="text-gray-400">Sirkulasi Peminjaman Hari Ini</span></li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Alamat & Kontak</h4>
                    <p class="text-gray-400 leading-relaxed text-[11px]">
                        {{ $pengaturan['alamat'] ?? 'Jl. Pendidikan No. 45, Gedung Utama Perpustakaan SMK PGRI Pekanbaru.' }}<br>
                        Email: {{ $pengaturan['email_perpustakaan'] ?? 'perpustakaan@smkpgri.sch.id' }}<br>
                        Telp: {{ $pengaturan['telepon'] ?? '(021) 7890-1234' }}
                    </p>
                </div>

            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-[11px] text-gray-400 gap-3 border-t border-white/10">
                <span>{{ $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru' }} &copy; {{ date('Y') }}. All rights reserved.</span>
                <div>
                    <span class="font-medium text-gray-500">Sistem Informasi Perpustakaan Sekolah Terpadu</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var loader = document.getElementById('page-skeleton-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(function() {
                        if (loader.parentNode) loader.parentNode.removeChild(loader);
                    }, 300);
                }
            }, 120);

            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 100,
                    easing: 'ease-out-cubic',
                });
            }

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#b91c1c',
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#b91c1c'
                });
            @endif
        });
    </script>
</body>
</html>
