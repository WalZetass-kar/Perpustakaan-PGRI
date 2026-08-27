{{-- Kolom "Buku" pada tabel Koleksi Buku: sampul kecil + judul + ISBN. --}}
<div class="flex items-center gap-3">
    <div class="w-10 h-14 bg-gray-100 rounded-lg overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
        @if ($buku->cover_url)
            <img src="{{ $buku->cover_thumb_url }}" alt="Cover" width="40" height="56" loading="lazy" class="w-full h-full object-cover">
        @else
            {{-- Tanpa sampul: tampilkan huruf pertama judulnya di atas gradasi. --}}
            <div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white font-black text-xs flex flex-col items-center justify-center p-1 border-l-2 border-amber-400/50 shadow-inner"><i class="fa-solid fa-book text-[11px] opacity-40"></i><span class="text-[7.5px] mt-0.5">{{ substr($buku->judul, 0, 1) }}</span></div>
        @endif
    </div>
    <div class="min-w-0 overflow-hidden">
        <p class="font-bold text-gray-900 line-clamp-2 text-xs leading-snug">{{ $buku->judul }}</p>
        <p class="text-[10px] text-gray-400 font-mono truncate mt-0.5">ISBN: {{ $buku->isbn ?? 'Tanpa ISBN' }} &bull; {{ $buku->tahun_terbit }}</p>
    </div>
</div>
