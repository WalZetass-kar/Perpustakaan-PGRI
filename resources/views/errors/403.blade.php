<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | Perpustakaan SMK PGRI</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            padding: 2rem;
            max-width: 480px;
        }
        .icon {
            width: 80px;
            height: 80px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }
        h1 {
            font-size: 5rem;
            font-weight: 800;
            color: #ef4444;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }
        p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .btn:hover { background: #1d4ed8; }
        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #fef3c7;
            color: #92400e;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚫</div>
        <h1>403</h1>
        <h2>Akses Ditolak</h2>
        @auth
            <div class="role-badge">
                Role: {{ auth()->user()->role->name ?? 'unknown' }}
            </div>
        @endauth
        <p>
            Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Halaman ini hanya dapat diakses oleh pengguna dengan hak akses tertentu.
        </p>
        @auth
            <a href="{{ route('dashboard') }}" class="btn">
                ← Kembali ke Dashboard Saya
            </a>
        @else
            <a href="{{ route('login') }}" class="btn">
                ← Login ke Portal Siswa
            </a>
        @endauth
    </div>
</body>
</html>
