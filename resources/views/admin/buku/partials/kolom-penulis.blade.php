{{-- Kolom "Penulis": nama penulis dengan penerbit sebagai baris kecil di bawahnya. --}}
<p class="font-bold text-gray-800 text-xs truncate">{{ $buku->penulis->nama ?? '-' }}</p>
<p class="text-[10.5px] text-gray-500 truncate mt-0.5">{{ $buku->penerbit->nama ?? '-' }}</p>
