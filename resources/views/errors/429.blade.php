<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 — Terlalu Banyak Permintaan | Sistem Informasi Perpustakaan</title>
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
            background: #f5f3ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        h1 {
            font-size: 5rem;
            font-weight: 800;
            color: #7c3aed;
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
            <svg style="width: 3.5rem; height: 3.5rem; color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>
        </div>
        <h1>429</h1>
        <h2>Terlalu Banyak Permintaan</h2>
        <p>
            Permintaan dari perangkat Anda datang terlalu cepat dan untuk sementara ditahan demi menjaga server perpustakaan tetap ringan. Tunggu kira-kira satu menit, lalu coba lagi.
        </p>
        <a href="{{ url('/') }}" class="btn">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
