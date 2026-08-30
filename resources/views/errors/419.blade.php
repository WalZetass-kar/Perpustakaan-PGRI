<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Sesi Halaman Kedaluwarsa | Sistem Informasi Perpustakaan</title>
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
            background: #ecfeff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        h1 {
            font-size: 5rem;
            font-weight: 800;
            color: #0891b2;
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
            <svg style="width: 3.5rem; height: 3.5rem; color: #0891b2;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h1>419</h1>
        <h2>Sesi Halaman Kedaluwarsa</h2>
        <p>
            Halaman ini sudah terbuka terlalu lama, atau ada aktivitas masuk pada peramban yang sama, sehingga pengaman formulirnya tidak lagi berlaku. Muat ulang halaman lalu ulangi pengisian Anda.
        </p>
        <a href="{{ url('/') }}" class="btn">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
