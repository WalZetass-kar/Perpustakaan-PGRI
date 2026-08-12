<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Perpustakaan SMK PGRI</title>
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
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col justify-center items-center p-4">

    <!-- Back to Home Link -->
    <div class="mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-600 hover:text-brand-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Beranda Utama</span>
        </a>
    </div>

    <!-- Login Card Container -->
    <div class="max-w-md w-full bg-white p-8 rounded-2xl border border-gray-200 shadow-md space-y-6">
        
        <!-- Header Brand -->
        <div class="text-center">
            <div class="w-12 h-12 rounded-xl bg-brand-700 text-white font-bold flex items-center justify-center text-xl mx-auto shadow-xs">
                P
            </div>
            <h1 class="mt-4 text-xl font-bold text-gray-900">Masuk Akun Perpustakaan</h1>
            <p class="mt-1 text-xs text-gray-500">Sistem Informasi Perpustakaan SMK PGRI</p>
        </div>

        @if($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email Sekolah</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="contoh: siswa@smkpgri.sch.id"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi</label>
                <input type="password" name="password" id="password" required placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-700 focus:ring-brand-700">
                    <span>Ingat Saya</span>
                </label>
                <span class="text-gray-400">Bantuan Hubungi Pustakawan</span>
            </div>

            <button type="submit" class="w-full py-2.5 bg-brand-700 text-white font-bold text-xs rounded-lg hover:bg-brand-800 transition shadow-xs">
                Masuk ke Sistem
            </button>
        </form>

        <div class="border-t border-gray-100 pt-4 text-center">
            <p class="text-xs text-gray-500 mb-2 font-medium">Akun Pengujian Demo (SMK PGRI):</p>
            <div class="text-[11px] text-gray-600 space-y-1 bg-gray-50 p-3 rounded-lg border border-gray-200">
                <p><strong>Admin:</strong> admin@smkpgri.sch.id | password</p>
                <p><strong>Petugas:</strong> pustakawan@smkpgri.sch.id | password</p>
                <p><strong>Siswa:</strong> siswa@smkpgri.sch.id | password</p>
            </div>
        </div>

    </div>

</body>
</html>
