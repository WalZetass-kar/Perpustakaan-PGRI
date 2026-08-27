{{-- Kolom "Aksi": menu titik-tiga berisi Edit dan Hapus.

     Kedua tombol aksi di dalam menu ini menutup dropdown-nya sendiri
     (@click="open = false"). Tanpa itu, menu tetap terbuka setelah
     "Edit Buku" diklik dan -- karena di-teleport ke body dengan z-[100] --
     ia mengambang di ATAS modal edit, menutupi isinya. Hal yang sama
     berlaku untuk konfirmasi hapus (SweetAlert). --}}
<div class="flex items-center justify-end" x-data="{ open: false, menuStyle: '' }" @scroll.window="open = false">
    <button @click.stop="open = !open; $nextTick(() => { const r = $el.getBoundingClientRect(); menuStyle = `top:${r.bottom + 6}px; left:${r.right - 144}px;` })" type="button" class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition">
        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
    </button>
    <template x-teleport="body">
        <div x-show="open" x-cloak @click.outside="open = false" :style="menuStyle"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed z-[100] w-36 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
            <button type="button" @click="open = false" data-buku='{!! $dataBuku !!}' class="btn-edit-buku w-full flex items-center gap-2 px-3 py-2 text-[11px] font-bold text-amber-700 hover:bg-amber-50 transition">
                <i class="fa-solid fa-pen-to-square w-3.5 text-center"></i>
                <span>Edit Buku</span>
            </button>
            <form action="{{ route('admin.buku.delete', $buku->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Hapus Judul Buku?', 'Master buku ini akan dihapus dari katalog.')">
                @csrf
                <button type="submit" @click="open = false" class="w-full flex items-center gap-2 px-3 py-2 text-[11px] font-bold text-rose-600 hover:bg-rose-50 transition">
                    <i class="fa-solid fa-trash-can w-3.5 text-center"></i>
                    <span>Hapus Buku</span>
                </button>
            </form>
        </div>
    </template>
</div>
