{{-- Kolom "Kategori": label kategori, ditambah label kelas bila bukunya
     memang diperuntukkan bagi satu jenjang tertentu. --}}
<div class="flex flex-col items-start gap-0.5">
    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200 inline-block">{{ $buku->kategori->nama ?? 'Umum' }}</span>
    @if ($buku->kelas?->nama_kelas)
        <span class="mt-1 px-2 py-0.5 rounded text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 inline-block">{{ $buku->kelas->nama_kelas }}</span>
    @endif
</div>
