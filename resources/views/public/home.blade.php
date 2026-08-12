@extends('layouts.app')

@section('title', 'Beranda - ' . $nama_perpustakaan)

@section('content')
<!-- SECTION 1: HERO HEADER (INLISLite Landing Page Style) -->
<section class="relative bg-gradient-to-r from-emerald-600 via-teal-600 to-blue-700 text-white py-16 lg:py-20 overflow-hidden shadow-lg">
    <!-- Subtle Background Glow -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-5 text-center lg:text-left">
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                    Integrated Library System <br class="hidden sm:inline">(Inlislite)
                </h1>
                <p class="text-sm sm:text-base text-teal-100 max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    Sebuah Perpustakaan Digital Sekolah yang dikembangkan langsung untuk menghimpun koleksi kejuruan, referensi modul, dan pelayanan perpustakaan dalam bentuk digital.
                </p>
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-2">
                    <a href="{{ route('katalog') }}" class="px-6 py-3 bg-white text-emerald-800 font-extrabold text-xs rounded-xl hover:bg-emerald-50 transition shadow-md flex items-center gap-2">
                        <span>Coba Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl transition shadow-md">
                            Dashboard Sistem
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl transition shadow-md">
                            Masuk Akun
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Right Hero Card Mockup -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="w-full max-w-md bg-white/95 backdrop-blur-md rounded-3xl p-6 text-gray-800 shadow-2xl border border-white/20 transform hover:scale-[1.02] transition duration-300">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white font-extrabold flex items-center justify-center text-xl shadow-xs">
                            P
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-900 block leading-tight">INLISLITE SMK PGRI</span>
                            <span class="text-[10px] text-gray-500 font-medium">Official School Library Portal</span>
                        </div>
                    </div>

                    <!-- Search Form Box in Hero -->
                    <form action="{{ route('katalog') }}" method="GET" class="space-y-3">
                        <label class="block text-xs font-bold text-gray-700">Pencarian Koleksi Katalog (OPAC)</label>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Cari judul buku, penulis, ISBN..." class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-600 focus:bg-white focus:outline-none">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 text-white font-bold text-xs rounded-xl hover:bg-emerald-700 transition shadow-xs">
                            Cari Katalog Sekarang
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECTION 2: PUSAT DATA DAN INFORMASI (Dark Section like Inlislite Screenshot) -->
<section class="bg-gray-950 text-white py-16 lg:py-20 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
        
        <!-- Header Pill Badge -->
        <div>
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold bg-blue-600 text-white shadow-xs mb-3">
                Pusat Data dan Informasi
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Perpustakaan SMK PGRI
            </h2>
            <p class="mt-3 text-xs sm:text-sm text-gray-400 max-w-3xl mx-auto leading-relaxed">
                Kenali <strong class="text-white">"Inlislite"</strong>, satu aplikasi dengan berbagai kemudahan dalam genggaman Anda. Mulai dari pencarian koleksi, peminjaman, keanggotaan hingga akses literatur digital kejuruan. Temukan inspirasi, referensi, dan wawasan baru dalam satu aplikasi ini. 
                <span class="text-emerald-400 font-bold block mt-1">#SalamLiterasi</span>
            </p>
        </div>

        <!-- Preview Aplikasi Card Layout -->
        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6 lg:p-8 text-left max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-center shadow-xl">
            <!-- Left Mockup Graphic -->
            <div class="lg:col-span-6 bg-gradient-to-tr from-gray-800 to-gray-900 border border-gray-700/60 rounded-2xl p-4 shadow-inner">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] font-mono text-gray-400 ml-2">Dashboard Management Inlislite</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs font-bold text-white mb-3">
                    <div class="bg-blue-600/80 p-3 rounded-xl">
                        <span class="text-[10px] text-blue-100 font-normal block">Total Koleksi</span>
                        <span class="text-lg font-black">{{ number_format($stats['total_koleksi']) }} Judul</span>
                    </div>
                    <div class="bg-emerald-600/80 p-3 rounded-xl">
                        <span class="text-[10px] text-emerald-100 font-normal block">Eksemplar Fisik</span>
                        <span class="text-lg font-black">{{ number_format($stats['buku_tersedia']) }} Buku</span>
                    </div>
                </div>
                <div class="bg-gray-800 p-3 rounded-xl text-[11px] text-gray-300 font-mono">
                    System INLISLite v11 - Status Normal
                </div>
            </div>

            <!-- Right Content Text -->
            <div class="lg:col-span-6 space-y-4">
                <h3 class="text-xl font-bold text-white">Preview Aplikasi</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Aplikasi ini dirancang langsung untuk memberikan pengalaman terbaik dalam mengelola perpustakaan berbasis digital demi mewujudkan modernisasi di lingkup sekolah kejuruan.
                </p>
                <ul class="space-y-2.5 text-xs text-gray-300 font-medium">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Dashboard yang mudah dipahami dan informatif.</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Manajemen data sirkulasi cepat dan efisien.</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Integrasi Kartu Digital & Barcode Siswa.</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</section>

<!-- SECTION 3: KENAPA INLISLITE? (Light Section with 3 White Cards) -->
<section class="bg-gray-100 py-16 lg:py-20 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-10">
        <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
            Kenapa Inlislite?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-white p-8 rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-gray-900 text-white flex items-center justify-center mx-auto shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Otomasi & Digitalisasi Terpadu</h3>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-8 rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-gray-900 text-white flex items-center justify-center mx-auto shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Open Source</h3>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-8 rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-gray-900 text-white flex items-center justify-center mx-auto shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Penggunaan & Modifikasi Etis</h3>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 4: FITUR UTAMA KAMI (Dark Section with 5 Colored Feature Cards) -->
<section class="bg-gray-950 text-white py-16 lg:py-20 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
        <div>
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold bg-blue-600 text-white shadow-xs mb-3">
                Fitur & Modul Inlislite
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Fitur Utama Kami
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-gray-400 max-w-2xl mx-auto">
                Kenali beberapa fitur utama yang kami miliki untuk kelola seluruh administrasi perpustakaan mulai dari katalog digital canggih, pengelolaan anggota, buku tamu hingga pengaturan back-office secara real-time.
            </p>
        </div>

        <!-- 5 Colored Feature Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto text-left">
            
            <!-- Card 1 (Green) -->
            <div class="bg-emerald-600 rounded-2xl p-6 space-y-3 shadow-md hover:bg-emerald-500 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <h3 class="text-sm font-bold text-white">Form Entri Katalog Sederhana</h3>
                </div>
                <p class="text-xs text-emerald-100 leading-relaxed">
                    Katalog digital yang disimpan dalam pangkalan data Inlislite mengikuti standar metadata MARC & INDOMARC.
                </p>
            </div>

            <!-- Card 2 (Cyan/Blue) -->
            <div class="bg-sky-600 rounded-2xl p-6 space-y-3 shadow-md hover:bg-sky-500 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h3 class="text-sm font-bold text-white">Kardeks Terbitan Berkala</h3>
                </div>
                <p class="text-xs text-sky-100 leading-relaxed">
                    Inlislite telah dilengkapi dengan kardeks terbitan berkala untuk memudahkan pemantauan pengadaan majalah, buletin, dan surat kabar.
                </p>
            </div>

            <!-- Card 3 (Dark Blue) -->
            <div class="bg-blue-700 rounded-2xl p-6 space-y-3 shadow-md hover:bg-blue-600 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <h3 class="text-sm font-bold text-white">Pilihan Model Kartu Anggota</h3>
                </div>
                <p class="text-xs text-blue-100 leading-relaxed">
                    Inlislite memungkinkan pengelola perpustakaan memilih bentuk model layout kartu anggota resmi terintegrasi barcode.
                </p>
            </div>

            <!-- Card 4 (Emerald Row 2) -->
            <div class="bg-emerald-600 md:col-span-2 rounded-2xl p-6 space-y-2 shadow-md hover:bg-emerald-500 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <h3 class="text-sm font-bold text-white">Facet Search pada Modul OPAC</h3>
                </div>
                <p class="text-xs text-emerald-100 leading-relaxed">
                    Pencarian multi-kategori cerdas pada katalog digital online untuk mempermudah siswa menemukan buku kejuruan secara akurat.
                </p>
            </div>

            <!-- Card 5 (Cyan Row 2) -->
            <div class="bg-sky-500 rounded-2xl p-6 space-y-2 shadow-md hover:bg-sky-400 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <h3 class="text-sm font-bold text-white">Autoresponsive</h3>
                </div>
                <p class="text-xs text-sky-100 leading-relaxed">
                    Tampilan fleksibel dan lancar diakses via smartphone, tablet, maupun layar komputer desktop.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- SECTION 5: SIAP TRANSFORMASI DIGITAL? (Light Action Section) -->
<section class="bg-gray-100 py-16 lg:py-20 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
            Siap Transformasi Digital?
        </h2>
        <p class="text-xs sm:text-sm text-gray-600 max-w-xl mx-auto">
            Bergabunglah dengan ribuan perpustakaan yang telah mempercayai <strong class="text-gray-900">INLISlite</strong> sebagai sistem aplikasi terpadu mereka.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-3 pt-2 text-xs">
            <a href="{{ route('katalog') }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 font-bold hover:bg-gray-50 transition shadow-2xs flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Lihat Katalog</span>
            </a>
            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 font-bold hover:bg-gray-50 transition shadow-2xs flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Demo Sistem</span>
            </a>
        </div>
    </div>
</section>

<!-- SECTION 6: KATALOG & BERITA REKOMENDASI PERPUSTAKAAN -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 text-center">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Koleksi Terpopuler & Berita Terbaru</h2>
            <p class="text-xs text-gray-500 mt-1">Tetap terhubung dengan perkembangan dan koleksi modul kejuruan SMK PGRI</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
            @foreach($buku_populer as $buku)
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-2xs hover:shadow-md transition flex gap-4">
                    <div class="w-20 h-28 bg-emerald-700 text-white font-black text-2xl rounded-xl flex items-center justify-center shrink-0">
                        {{ substr($buku->judul, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            {{ $buku->kategori->nama ?? 'Kejuruan' }}
                        </span>
                        <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug">
                            <a href="{{ route('buku.detail', $buku->id) }}" class="hover:text-emerald-700 transition">{{ $buku->judul }}</a>
                        </h3>
                        <p class="text-[11px] text-gray-500">Penulis: {{ $buku->penulis->nama ?? '-' }}</p>
                        <a href="{{ route('buku.detail', $buku->id) }}" class="inline-block pt-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700">Lihat Detail Buku &rarr;</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
