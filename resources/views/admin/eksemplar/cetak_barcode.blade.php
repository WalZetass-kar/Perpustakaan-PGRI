<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Stiker Barcode &amp; QR Code Eksemplar Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }
            body {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-sm flex items-center justify-between gap-4">
        <div>
            <h1 class="text-sm font-black text-gray-900 uppercase tracking-wide">Cetak Stiker Barcode &amp; QR Code Eksemplar</h1>
            <p class="text-xs text-gray-500">Label stiker siap tempel pada buku fisik perpustakaan SMK PGRI Pekanbaru.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl transition shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 012-2v-4a2 2 0 01-2-2H5a2 2 0 01-2 2v4a2 2 0 012 2h2m2 4h6a2 2 0 012-2v-4a2 2 0 01-2-2H9a2 2 0 01-2 2v4a2 2 0 012 2zm8-12V5a2 2 0 01-2-2H9a2 2 0 01-2 2v4h10z"/></svg>
                <span>Cetak Label Stiker (A4)</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Sticker Labels Grid Container -->
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($eksemplarList as $item)
                @php
                    $qrText = urlencode("BOOK-EXEMPLAR|KODE:" . $item->kode_eksemplar . "|BARCODE:" . $item->barcode . "|TITLE:" . ($item->buku->judul ?? ''));
                    $qrUrl = "https://quickchart.io/qr?text={$qrText}&size=120&margin=1";
                @endphp

                <div class="border-2 border-red-700 rounded-xl p-3 bg-white flex flex-col justify-between space-y-2 text-center shadow-2xs relative overflow-hidden">
                    <!-- School Library Header -->
                    <div class="bg-red-700 text-white py-0.5 px-2 -mx-3 -mt-3 text-[9px] font-black uppercase tracking-wider">
                        PERPUSTAKAAN SMK PGRI
                    </div>

                    <!-- Book Info -->
                    <div class="space-y-0.5 pt-1">
                        <h4 class="text-[10px] font-black text-gray-900 truncate leading-tight">{{ $item->buku->judul ?? 'Judul Buku' }}</h4>
                        <span class="text-[9px] font-bold text-red-700 block font-mono">KODE: {{ $item->kode_eksemplar }}</span>
                    </div>

                    <!-- QR Code & Barcode Display -->
                    <div class="flex items-center justify-center gap-2 py-1">
                        <div class="w-14 h-14 p-0.5 border border-gray-300 rounded bg-white">
                            <img src="{{ $qrUrl }}" alt="QR {{ $item->kode_eksemplar }}" class="w-full h-full object-contain">
                        </div>
                    </div>

                    <!-- Barcode Value Footer -->
                    <div class="bg-gray-100 border-t border-gray-200 py-1 -mx-3 -mb-3 text-[9px] font-mono text-gray-800 font-bold">
                        BARCODE: {{ $item->barcode }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>
