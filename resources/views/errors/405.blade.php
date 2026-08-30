<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>405 — Cara Akses Tidak Sesuai | Sistem Informasi Perpustakaan</title>
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
            background: #fffbeb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        h1 {
            font-size: 5rem;
            font-weight: 800;
            color: #d97706;
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
            background: #881337;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .btn:hover { background: #701a31; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg style="width: 3.5rem; height: 3.5rem; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <h1>405</h1>
        <h2>Cara Akses Tidak Sesuai</h2>
        <p>
            Alamat ini hanya melayani permintaan yang dikirim lewat tombol di dalam sistem, bukan yang diketik langsung pada bilah alamat. Silakan kembali ke beranda lalu gunakan tombol yang tersedia.
        </p>
        <a href="{{ url('/') }}" class="btn">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
