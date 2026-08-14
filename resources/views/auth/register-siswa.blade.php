<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Siswa - Perpustakaan SMK PGRI Pekanbaru</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
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
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-y-auto font-sans">

    <!-- Background Image with Soft Red Overlay -->
    <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-20 pointer-events-none" style="background-image: url('https://smkpgripekanbaru.sch.id/images/pgri.webp');"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand-700/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Registration Container Card -->
    <div class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-200 grid grid-cols-1 md:grid-cols-12 my-6">
        
        <!-- Left Side: Siswa Portal Banner -->
        <div class="md:col-span-5 bg-gradient-to-br from-brand-800 via-brand-700 to-red-900 text-white p-8 sm:p-10 flex flex-col justify-between relative">
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-white/10 text-white border border-white/20 hover:bg-white/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <div class="pt-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI" class="w-14 h-14 object-contain drop-shadow-md">
                    <h2 class="mt-4 text-2xl font-black leading-snug">Registrasi Anggota Siswa Baru</h2>
                    <p class="mt-2 text-xs text-red-100 leading-relaxed font-normal">
                        Daftarkan NISN dan identitas siswa Anda untuk mulai meminjam modul buku kejuruan &amp; mencetak kartu anggota resmi.
                    </p>
                </div>
            </div>

            <!-- Switch to Login Helper -->
            <div class="pt-6 border-t border-white/10">
                <span class="text-[11px] text-red-200 block font-medium">Sudah memiliki akun terdaftar?</span>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 hover:text-white transition mt-1">
                    <span>Masuk Portal Login Siswa</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Right Side: Siswa Register Form -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center space-y-6">
            <div>
                <h3 class="text-xl font-extrabold text-gray-900">Form Pendaftaran Siswa</h3>
                <p class="text-xs text-gray-500 mt-1">Isi formulir pendaftaran anggota dengan data yang valid.</p>
            </div>

            @if($errors->any())
                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-700 space-y-1">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Gagal Melakukan Pendaftaran:</span>
                    </div>
                    <ul class="list-disc pl-6 font-medium text-[11px]">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label for="name" class="block font-bold text-gray-700 mb-1.5">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Budi Santoso"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="nim" class="block font-bold text-gray-700 mb-1.5">NISN / NIS Sekolah <span class="text-rose-500">*</span></label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required placeholder="Contoh: 0081234567"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label for="program_studi" class="block font-bold text-gray-700 mb-1.5">Jurusan / Keahlian <span class="text-rose-500">*</span></label>
                        <select name="program_studi" id="program_studi" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Jurusan --</option>
                            <option value="Teknik Komputer & Jaringan (TKJ)" {{ old('program_studi') == 'Teknik Komputer & Jaringan (TKJ)' ? 'selected' : '' }}>Teknik Komputer &amp; Jaringan (TKJ)</option>
                            <option value="Rekayasa Perangkat Lunak (RPL)" {{ old('program_studi') == 'Rekayasa Perangkat Lunak (RPL)' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option>
                            <option value="Akuntansi & Keuangan Lembaga (AKL)" {{ old('program_studi') == 'Akuntansi & Keuangan Lembaga (AKL)' ? 'selected' : '' }}>Akuntansi &amp; Keuangan (AKL)</option>
                            <option value="Otomatisasi & Tata Kelola Perkantoran (OTKP)" {{ old('program_studi') == 'Otomatisasi & Tata Kelola Perkantoran (OTKP)' ? 'selected' : '' }}>Perkantoran (OTKP)</option>
                            <option value="Bisnis Daring & Pemasaran (BDP)" {{ old('program_studi') == 'Bisnis Daring & Pemasaran (BDP)' ? 'selected' : '' }}>Bisnis Daring &amp; Pemasaran (BDP)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="email" class="block font-bold text-gray-700 mb-1.5">Email Siswa <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="budi@smkpgri.sch.id"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label for="phone" class="block font-bold text-gray-700 mb-1.5">No. WhatsApp (Opsional)</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="0812-3456-7890"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="password" class="block font-bold text-gray-700 mb-1.5">Kata Sandi <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block font-bold text-gray-700 mb-1.5">Konfirmasi Sandi <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi kata sandi"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Daftar Akun Anggota Siswa</span>
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>

            <div class="border-t border-gray-100 pt-3 text-center">
                <p class="text-[11px] text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-extrabold text-brand-700 hover:underline">Masuk di sini</a></p>
            </div>
        </div>

    </div>

</body>
</html>
