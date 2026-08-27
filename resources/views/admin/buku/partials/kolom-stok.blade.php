{{-- Kolom "Stok": sisa eksemplar terhadap total, hijau bila masih ada. --}}
@php
    $tersedia = $buku->available_quantity > 0;
@endphp
<div class="inline-flex flex-col items-center">
    <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-black {{ $tersedia ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
        {{ $buku->available_quantity }} / {{ $buku->total_quantity }} Eks
    </span>
    <span class="text-[9.5px] text-gray-400 font-medium mt-0.5">
        {{ $tersedia ? 'Tersedia' : 'Habis Dipinjam' }}
    </span>
</div>
