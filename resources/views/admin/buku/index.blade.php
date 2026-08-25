@extends('layouts.dashboard')

@section('title', 'Manajemen Koleksi Buku')
@section('page_heading', 'Koleksi Buku Perpustakaan')

@push('styles')
<style>
    .dataTables_wrapper {
        padding: 1rem;
        font-size: 0.75rem;
    }
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 0.75rem;
    }
    .dataTables_wrapper .dataTables_length select {
        padding: 0.35rem 1.75rem 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #374151;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        outline: none;
    }
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 0.75rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: #111827;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        outline: none;
        min-width: 220px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #b91c1c;
        background-color: #ffffff;
    }
    .dataTables_wrapper .dataTables_info {
        padding-top: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        margin-left: 0.25rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #374151 !important;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
        color: #b91c1c !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #b91c1c !important;
        border-color: #b91c1c !important;
        color: #ffffff !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
        color: #9ca3af !important;
    }
    table.dataTable no-footer {
        border-bottom: 1px solid #e5e7eb;
    }

    /* Mode List: tabel penuh khusus desktop, kartu ringkas khusus mobile/tablet.
       Ditulis sebagai media query biasa (bukan varian Tailwind) agar tidak
       bergantung pada CSS yang di-generate Tailwind CDN di sisi browser. */
    @media (max-width: 1023px) {
        #tabel-buku { display: none !important; }
    }
    @media (min-width: 1024px) {
        #list-buku-mobile-container { display: none !important; }
    }

    /* Kedua container ini disisipkan ke dalam .dataTables_wrapper, tepat setelah
       <table>. Di atasnya ada .dataTables_length yang ber-float:left. Saat mode
       Grid, tabel diberi class "hidden" sehingga clear:both bawaan
       table.dataTable ikut hilang dari alur dokumen. Tanpa clear di sini,
       container Grid -- yang membentuk formatting context sendiri -- akan
       menyusut menghindari float tersebut dan tampak mengumpul, tidak memenuhi
       lebar. */
    #grid-buku-container,
    #list-buku-mobile-container { clear: both; }

    /* Di layar HP (<640px) kartu mode Grid sebelumnya jatuh menjadi satu kolom
       memanjang ke bawah, sehingga sekali layar hanya memuat satu buku.
       Dijadikan dua kolom, kiri-kanan, lalu lanjut ke bawah.

       Ditulis sebagai media query ber-selektor id, bukan varian Tailwind, dengan
       dua alasan: menang atas grid-cols-1 bawaan tanpa perlu !important, dan
       tidak bergantung pada kelas yang mungkin tidak ikut ter-generate di
       public/vendor/tailwind/tailwind.min.css yang sudah di-purge. */
    @media (max-width: 639px) {
        /* Padding bertumpuk tiga lapis (main 16px + wrapper 16px + container
           8px) memakan 80px dari layar 360px -- 22% habis jadi ruang kosong,
           sisanya baru dibagi dua. Dirampingkan supaya kartunya dapat lebar
           yang layak; inilah sebab utama tampilan terasa sesak. */
        .dataTables_wrapper { padding: 0.5rem; }

        #grid-buku-container {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.5rem;
            padding: 0.25rem;
        }

        .kartu-grid-buku-isi {
            padding: 0.5rem;
            gap: 0.25rem;
        }

        /* Garis pemisah di atas baris kaki tidak perlu jarak selega desktop. */
        .kartu-grid-buku-kaki { padding-top: 0.25rem; }

        /* Banner "Menampilkan hasil untuk ..." (muncul saat datang dari menu
           Temukan Buku dengan ?search=). Isinya satu baris mendatar: ikon +
           label, teks petunjuk, lalu tombol reset yang ber-shrink-0. Di layar
           360px tombol itu mempertahankan lebarnya sehingga teks petunjuk
           terjepit tinggal +-50px dan pecah satu kata per baris.

           Di HP ketiganya ditumpuk ke bawah, dan teks petunjuk diberi baris
           sendiri supaya utuh terbaca. */
        #search-filter-banner {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            padding: 0.75rem;
        }

        .banner-cari-teks {
            flex-wrap: wrap;
            align-items: flex-start;
        }

        /* flex-basis penuh memaksa petunjuk turun ke barisnya sendiri; margin
           kiri menyejajarkannya dengan label di atas, melewati lebar ikon. */
        .banner-cari-petunjuk {
            flex-basis: 100%;
            margin-left: 1.4rem;
        }

        #btn-reset-search-filter { width: 100%; }

        /* CoverImageService me-resize sampul berdasarkan LEBAR saja, jadi rasio
           aslinya yang tegak tetap utuh. Kotak h-44 (176px) di kartu selebar
           ~148px nyaris persegi, sehingga object-cover memangkas sampul di sisi
           kiri-kanan -- judul pada gambarnya ikut terpotong.

           Dikunci ke rasio supaya tingginya ikut lebar kartu, berapa pun lebar
           layarnya. Angkanya kompromi antara sampul yang utuh dan kartu yang
           pendek; makin mendekati 1, makin pendek kartunya dan makin terpangkas
           sisi sampulnya. Urutannya: 2/3 (222px, utuh) - 3/4 (197px) -
           4/5 (185px, dipakai sekarang) - 1/1 (148px, paling pendek).
           Angka di bawah ini satu-satunya tempat penyetelannya. */
        .kartu-grid-buku-sampul {
            height: auto;
            aspect-ratio: 4 / 5;
        }

        /* Badge kategori seperti "Teknik Komputer & Jaringan" jatuh ke dua
           baris dan membuat tinggi kartu tidak seragam. Dikecilkan sedikit dan
           dipotong dengan elipsis, bukan dibiarkan pecah. */
        .kartu-grid-buku-badge > span {
            font-size: 9px;
            padding: 0.05rem 0.3rem;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Baris kaki: badge stok TIDAK BOLEH pecah jadi "4/4" lalu "Eks" di
           baris berikutnya. Badge dikunci satu baris, kode rak yang mengalah
           dan menyusut. */
        .kartu-grid-buku-kaki { gap: 0.25rem; }
        .kartu-grid-buku-kaki > span:last-child {
            flex-shrink: 0;
            white-space: nowrap;
            font-size: 9px;
            padding: 0.05rem 0.3rem;
        }
        .kartu-grid-buku-kaki > span:first-child {
            min-width: 0;
            flex: 1 1 auto;
            max-width: none;
            font-size: 9px;
        }
    }

    /* Jaring pengaman: pastikan tabel selalu selebar wadahnya. Aturan bawaan
       table.dataTable adalah width:100% dengan margin:0 auto, sehingga lebar
       apa pun yang lebih kecil akan tampil terpusat dan menyisakan ruang kosong
       di kiri-kanan. */
    #tabel-buku { width: 100% !important; }
</style>
@endpush

@section('content')
<div class="space-y-5" x-data="{ 
    openAddModal: false, 
    openEditModal: false, 
    editData: {}, 
    allRaks: {{ json_encode($rakList) }},
    addRakId: '',
    addLaciId: '',
    editRakId: '',
    editLaciId: '',
    get addAvailableLacis() {
        const found = this.allRaks.find(r => r.id == this.addRakId);
        return found ? found.laci : [];
    },
    get editAvailableLacis() {
        const found = this.allRaks.find(r => r.id == this.editRakId);
        return found ? found.laci : [];
    }
}" x-init="openAddModal = false; openEditModal = false; editData = {}" id="buku-container">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm space-y-3">
        <div class="flex items-center justify-between gap-2 sm:gap-3">
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" id="btn-toggle-filter-buku" onclick="toggleKoleksiBukuFilter()" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 flex items-center justify-center border border-gray-200 transition shrink-0" title="Sembunyikan/Tampilkan Pencarian & Filter" aria-label="Toggle Pencarian & Filter">
                    <svg id="icon-toggle-filter-buku" class="w-3.5 h-3.5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="flex items-center bg-gray-100 rounded-xl p-0.5 border border-gray-200 shrink-0">
                    <button type="button" id="btn-view-grid-buku" onclick="setKoleksiBukuView('grid')"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-gray-500 hover:text-gray-700" title="Tampilan Grid">
                        <i class="fa-solid fa-grip text-[11px]"></i>
                        <span class="hidden sm:inline">Grid</span>
                    </button>
                    <button type="button" id="btn-view-list-buku" onclick="setKoleksiBukuView('list')"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-white text-brand-700 shadow-xs border border-gray-200" title="Tampilan List">
                        <i class="fa-solid fa-list text-[11px]"></i>
                        <span class="hidden sm:inline">List</span>
                    </button>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end shrink-0">
                {{-- Di bawah 1024px kedua tombol ini sengaja disembunyikan: versi
                     mobile/tablet-nya sudah tersedia di dasar panel filter. Kalau
                     ditampilkan di sini juga, tombol export jadi muncul dua kali
                     sekaligus dan berdesakan dengan tombol panah di kiri. --}}
                <a href="{{ route('admin.buku.export.excel') }}" class="hidden lg:flex px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm items-center gap-1.5 shrink-0" title="Export seluruh data buku ke format Excel">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
                <a href="{{ route('admin.buku.export.pdf') }}" target="_blank" class="hidden lg:flex px-3.5 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm items-center gap-1.5 shrink-0" title="Cetak / Simpan Laporan PDF Resmi">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Cetak / PDF</span>
                </a>
                <button @click="addRakId = ''; addLaciId = ''; openAddModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-plus text-emerald-300"></i>
                    <span>Tambah Buku</span>
                </button>
            </div>
        </div>

        {{-- Pencarian & Filter Cepat (bisa disembunyikan) --}}
        <div id="koleksi-buku-filter-wrap" class="pt-3 border-t border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 text-xs">
                <div class="relative sm:col-span-1">
                    <input type="text" id="search-buku" placeholder="Cari judul, ISBN, penulis..."
                           class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs absolute left-3 top-1/2 -translate-y-1/2"></i>
                </div>
                <select id="filter-kategori-buku" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kt)
                        <option value="{{ $kt->id }}">{{ $kt->nama }}</option>
                    @endforeach
                </select>
                <select id="filter-rak-buku" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Rak</option>
                    @foreach($rakList as $rk)
                        <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                    @endforeach
                </select>
                <select id="filter-kelas-buku" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kl)
                        <option value="{{ $kl->id }}">{{ $kl->label_lengkap }}</option>
                    @endforeach
                </select>
                <select id="filter-status-buku" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Status Stok</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="habis">Stok Habis</option>
                </select>
            </div>

            {{-- Export Excel / PDF: mobile & tablet saja, taruh paling bawah panel filter --}}
            <div class="lg:hidden grid grid-cols-2 gap-2 mt-2 text-xs">
                <a href="{{ route('admin.buku.export.excel') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center justify-center gap-1.5" title="Export seluruh data buku ke format Excel">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
                <a href="{{ route('admin.buku.export.pdf') }}" target="_blank" class="px-3.5 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center justify-center gap-1.5" title="Cetak / Simpan Laporan PDF Resmi">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Cetak / PDF</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Banner: ditampilkan via JS saat ada ?search= di URL --}}
    <div id="search-filter-banner" class="hidden bg-blue-50 border border-blue-200 rounded-2xl px-4 py-3 flex items-center justify-between gap-3 text-xs">
        <div class="banner-cari-teks flex items-center gap-2 text-blue-800">
            <i class="fa-solid fa-filter text-blue-500"></i>
            <span id="search-filter-text" class="font-semibold"></span>
            <span class="banner-cari-petunjuk text-blue-500 font-normal">— klik tombol aksi ⋮ pada buku untuk edit data</span>
        </div>
        <button id="btn-reset-search-filter" type="button" class="shrink-0 px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-xl transition text-[11px]">
            Tampilkan Semua Buku
        </button>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table id="tabel-buku" class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3 px-4 font-bold w-[38%]">Buku & Cover</th>
                        <th class="py-3 px-4 font-bold w-[22%]">Penulis / Penerbit</th>
                        <th class="py-3 px-4 font-bold w-[18%]">Kategori</th>
                        <th class="py-3 px-4 font-bold text-center w-[12%]">Stok Fisik</th>
                        <th class="py-3 px-4 lg:pr-8 font-bold text-right w-[10%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-book text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Tambah Koleksi Buku Baru</h3>
                        <p class="text-[10px] text-gray-500">Masukkan detail buku, lokasi rak & laci, serta jumlah stok fisik</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form action="{{ route('admin.buku.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" required placeholder="Contoh: Pemrograman Web Dasar" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">ISBN (Opsional)</label>
                        <input type="text" name="isbn" placeholder="978-602-xxx" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tahun Terbit <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_terbit" value="{{ date('Y') }}" required min="1900" max="{{ date('Y') + 1 }}" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="total_quantity" value="1" required min="1" max="1000" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium font-bold text-brand-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Penulis Buku</label>
                        <select name="penulis_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penulis --</option>
                            @foreach($penulisList as $pn)
                                <option value="{{ $pn->id }}">{{ $pn->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Penerbit Buku</label>
                        <select name="penerbit_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penerbit --</option>
                            @foreach($penerbitList as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kategori Buku</label>
                        <select name="kategori_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $kt)
                                <option value="{{ $kt->id }}">{{ $kt->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kelas</label>
                        <select name="kelas_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kl)
                                <option value="{{ $kl->id }}">{{ $kl->label_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Lokasi Rak</label>
                        <select name="rak_id" x-model="addRakId" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Rak --</option>
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tingkat / Laci</label>
                        <select name="rak_laci_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Laci --</option>
                            <template x-for="laci in addAvailableLacis" :key="laci.id">
                                <option :value="laci.id" x-text="laci.nama_laci"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Cover Buku (Opsional)</label>
                    <input type="file" name="cover" accept="image/*" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none text-[11px] file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-brand-700 file:text-white">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Sinopsis / Ringkasan (Opsional)</label>
                    <textarea name="sinopsis" rows="2" placeholder="Ringkasan materi atau buku..." class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium resize-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">
                        Keterangan Posisi Buku (Opsional)
                        <span class="text-[10px] font-normal text-gray-400 ml-1">— posisi fisik buku di dalam laci/rak</span>
                    </label>
                    <textarea name="keterangan_posisi" rows="2"
                              placeholder="Contoh: Baris ke-2 dari depan, urutan ke-5 dari kiri. Atau: Di pojok kanan laci bagian bawah."
                              class="w-full px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg focus:ring-1.5 focus:ring-amber-500 focus:bg-white focus:outline-none font-medium text-gray-700 placeholder-gray-400 resize-none"></textarea>
                    <p class="text-[10px] text-amber-700 mt-0.5 font-medium">
                        <i class="fa-solid fa-circle-info mr-0.5"></i>
                        Membantu petugas menemukan buku secara lebih presisi di dalam laci.
                    </p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Buku</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-amber-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Edit Data Buku</h3>
                        <p class="text-[10px] text-gray-500">Perbarui rincian, lokasi rak & laci, serta stok buku</p>
                    </div>
                </div>
                <button @click="openEditModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form :action="'{{ url('/admin/buku/update') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" x-model="editData.judul" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">ISBN (Opsional)</label>
                        <input type="text" name="isbn" x-model="editData.isbn" placeholder="978-602-xxx" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tahun Terbit <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_terbit" x-model="editData.tahun_terbit" required min="1900" max="{{ date('Y') + 1 }}" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="total_quantity" x-model="editData.total_quantity" required min="1" max="1000" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium font-bold text-brand-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Penulis Buku</label>
                        <select name="penulis_id" x-model="editData.penulis_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penulis --</option>
                            @foreach($penulisList as $pn)
                                <option value="{{ $pn->id }}">{{ $pn->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Penerbit Buku</label>
                        <select name="penerbit_id" x-model="editData.penerbit_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penerbit --</option>
                            @foreach($penerbitList as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kategori Buku</label>
                        <select name="kategori_id" x-model="editData.kategori_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $kt)
                                <option value="{{ $kt->id }}">{{ $kt->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kelas</label>
                        <select name="kelas_id" x-model="editData.kelas_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kl)
                                <option value="{{ $kl->id }}">{{ $kl->label_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Lokasi Rak</label>
                        <select name="rak_id" x-model="editRakId" @change="editData.rak_id = editRakId" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Rak --</option>
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tingkat / Laci</label>
                        <select name="rak_laci_id" x-model="editData.rak_laci_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Laci --</option>
                            <template x-for="laci in editAvailableLacis" :key="laci.id">
                                <option :value="laci.id" x-text="laci.nama_laci" :selected="laci.id == editData.rak_laci_id"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Ganti Cover Buku (Opsional)</label>
                    <input type="file" name="cover" accept="image/*" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none text-[11px] file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-brand-700 file:text-white">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Sinopsis / Ringkasan (Opsional)</label>
                    <textarea name="sinopsis" x-model="editData.sinopsis" rows="2" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium resize-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">
                        Keterangan Posisi Buku (Opsional)
                        <span class="text-[10px] font-normal text-gray-400 ml-1">— posisi fisik buku di dalam laci/rak</span>
                    </label>
                    <textarea name="keterangan_posisi" x-model="editData.keterangan_posisi" rows="2"
                              placeholder="Contoh: Baris ke-2 dari depan, urutan ke-5 dari kiri. Atau: Di pojok kanan laci bagian bawah."
                              class="w-full px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg focus:ring-1.5 focus:ring-amber-500 focus:bg-white focus:outline-none font-medium text-gray-700 placeholder-gray-400 resize-none"></textarea>
                    <p class="text-[10px] text-amber-700 mt-0.5 font-medium">
                        <i class="fa-solid fa-circle-info mr-0.5"></i>
                        Membantu petugas menemukan buku secara lebih presisi di dalam laci.
                    </p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const tabelBuku = $('#tabel-buku').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.buku') }}",
                type: "GET",
                data: function(d) {
                    d.kategori_id = $('#filter-kategori-buku').val();
                    d.rak_id = $('#filter-rak-buku').val();
                    d.kelas_id = $('#filter-kelas-buku').val();
                    d.status_stok = $('#filter-status-buku').val();
                }
            },
            columns: [
                { data: 'buku', name: 'judul', orderable: true, searchable: true },
                { data: 'penulis', name: 'penulis_id', orderable: true, searchable: true },
                { data: 'kategori', name: 'kategori_id', orderable: true, searchable: true },
                { data: 'stok', name: 'available_quantity', orderable: true, searchable: false, className: 'text-center' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-right px-4 lg:pr-8' }
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            dom: 'lrtip',
            // Wajib false. Dengan autoWidth aktif, DataTables mengukur tabel lalu
            // menuliskan hasilnya sebagai lebar inline. Ketika halaman dibuka pada
            // mode Grid, tabel sedang display:none saat pengukuran berlangsung,
            // sehingga yang tersimpan adalah lebar sebesar isi kolom saja. Saat
            // pengguna pindah ke mode List, lebar inline itu menimpa width:100%
            // milik table.dataTable dan tabel tampil menyempit, lalu dipusatkan
            // oleh margin:0 auto pada aturan yang sama.
            autoWidth: false,
            language: {
                processing: '<div class="py-4 text-center text-xs font-bold text-brand-700 flex items-center justify-center gap-2"><i class="fa-solid fa-spinner fa-spin text-sm"></i> Memuat data koleksi...</div>',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ buku',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 buku',
                infoFiltered: '(disaring dari _MAX_ total)',
                zeroRecords: 'Tidak ditemukan data buku yang sesuai',
                paginate: {
                    first: '<i class="fa-solid fa-angles-left"></i>',
                    previous: '<i class="fa-solid fa-chevron-left"></i>',
                    next: '<i class="fa-solid fa-chevron-right"></i>',
                    last: '<i class="fa-solid fa-angles-right"></i>'
                }
            }
        });

        // Sisipkan container Grid & List-mobile tepat setelah <table> (di dalam wrapper DataTables)
        // supaya length/search/info/pagination bawaan DataTables tetap terlihat di semua mode.
        $('#tabel-buku').after('<div id="grid-buku-container" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-4"></div>');
        $('#grid-buku-container').after('<div id="list-buku-mobile-container" class="hidden divide-y divide-gray-100"></div>');

        function escapeHtml(str) {
            return $('<div>').text(str ?? '').html();
        }

        function buildBookCard(row) {
            const stokOk = row.available_quantity > 0;
            const stokClass = stokOk ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200';
            const coverInner = row.cover_url
                ? '<img src="' + row.cover_url + '" alt="' + escapeHtml(row.judul_raw) + '" width="300" height="176" loading="lazy" class="w-full h-full object-cover">'
                : '<div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white flex items-center justify-center"><i class="fa-solid fa-book text-3xl opacity-30"></i></div>';
            const kelasBadge = row.kelas_nama
                ? '<span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">' + escapeHtml(row.kelas_nama) + '</span>'
                : '';

            return '<div class="kartu-grid-buku bg-white rounded-2xl border-2 border-gray-200 hover:border-brand-300 hover:shadow-md transition duration-200 overflow-hidden flex flex-col">'
                + '<div class="kartu-grid-buku-sampul relative w-full h-44 bg-gray-100 overflow-hidden">'
                +     coverInner
                +     '<div class="absolute top-2 right-2 bg-white/95 rounded-lg shadow-sm">' + row.aksi + '</div>'
                + '</div>'
                + '<div class="kartu-grid-buku-isi p-3.5 flex-1 flex flex-col justify-between gap-2.5 text-xs">'
                +   '<div>'
                +     '<h3 class="font-extrabold text-gray-900 line-clamp-2 leading-snug">' + escapeHtml(row.judul_raw) + '</h3>'
                +     '<p class="text-[11px] text-gray-500 truncate mt-0.5">' + escapeHtml(row.penulis_nama) + '</p>'
                +   '</div>'
                +   '<div class="kartu-grid-buku-badge flex flex-wrap gap-1">'
                +     '<span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-200">' + escapeHtml(row.kategori_nama) + '</span>'
                +     kelasBadge
                +   '</div>'
                +   '<div class="kartu-grid-buku-kaki pt-2 border-t border-gray-100 flex items-center justify-between text-[10.5px]">'
                +     '<span class="font-mono font-bold text-gray-600 truncate max-w-[60%]">' + escapeHtml(row.rak_text) + '</span>'
                +     '<span class="px-2 py-0.5 rounded-md text-[10px] font-black border ' + stokClass + '">' + row.available_quantity + '/' + row.total_quantity + ' Eks</span>'
                +   '</div>'
                + '</div>'
            + '</div>';
        }

        // Versi ringkas buildBookCard, khusus baris List di layar mobile/tablet (<1024px)
        function buildListCardMobile(row) {
            const stokOk = row.available_quantity > 0;
            const stokClass = stokOk ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200';
            const coverInner = row.cover_url
                ? '<img src="' + row.cover_url + '" alt="' + escapeHtml(row.judul_raw) + '" width="40" height="56" loading="lazy" class="w-full h-full object-cover">'
                : '<div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white flex items-center justify-center"><i class="fa-solid fa-book text-xs opacity-40"></i></div>';
            const kelasBadge = row.kelas_nama
                ? '<span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-amber-50 text-amber-700 border border-amber-200">' + escapeHtml(row.kelas_nama) + '</span>'
                : '';

            return '<div class="p-3 flex items-start gap-3 hover:bg-gray-50/70 transition">'
                +   '<div class="w-10 h-14 rounded-lg overflow-hidden shrink-0 bg-gray-100 border border-gray-200">' + coverInner + '</div>'
                +   '<div class="flex-1 min-w-0">'
                +     '<div class="flex items-start justify-between gap-2">'
                +       '<h3 class="font-extrabold text-gray-900 text-xs line-clamp-2 leading-snug">' + escapeHtml(row.judul_raw) + '</h3>'
                +       '<div class="shrink-0">' + row.aksi + '</div>'
                +     '</div>'
                +     '<p class="text-[10.5px] text-gray-500 truncate mt-0.5">' + escapeHtml(row.penulis_nama) + '</p>'
                +     '<div class="flex flex-wrap items-center gap-1 mt-1.5">'
                +       '<span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-brand-50 text-brand-700 border border-brand-200">' + escapeHtml(row.kategori_nama) + '</span>'
                +       kelasBadge
                +       '<span class="px-1.5 py-0.5 rounded text-[9px] font-black border ' + stokClass + '">' + row.available_quantity + '/' + row.total_quantity + ' Eks</span>'
                +     '</div>'
                +     '<p class="text-[9.5px] text-gray-400 font-mono mt-1.5 truncate">' + escapeHtml(row.rak_text) + '</p>'
                +   '</div>'
                + '</div>';
        }

        // Setiap DataTables selesai fetch data, rebuild kartu Grid & List-mobile dari data yang sama (tanpa request tambahan)
        $('#tabel-buku').on('xhr.dt', function(e, settings, json) {
            if (!json || !Array.isArray(json.data)) return;
            const $grid = $('#grid-buku-container');
            const $listMobile = $('#list-buku-mobile-container');
            if (json.data.length === 0) {
                const emptyHtml = '<div class="py-14 text-center text-gray-400"><i class="fa-solid fa-book-open text-2xl mb-2 block"></i><p class="text-xs font-bold text-gray-700">Tidak ditemukan data buku yang sesuai</p></div>';
                $grid.html('<div class="col-span-full">' + emptyHtml + '</div>');
                $listMobile.html(emptyHtml);
                return;
            }
            $grid.html(json.data.map(buildBookCard).join(''));
            $listMobile.html(json.data.map(buildListCardMobile).join(''));
            if (window.Alpine) {
                window.Alpine.initTree($grid.get(0));
                window.Alpine.initTree($listMobile.get(0));
            }
        });

        // Filter cepat: Kategori / Rak / Kelas / Status Stok
        $('#filter-kategori-buku, #filter-rak-buku, #filter-kelas-buku, #filter-status-buku').on('change', function() {
            tabelBuku.draw();
        });

        // Kotak pencarian kustom (menggantikan search box bawaan DataTables)
        let searchDebounce;
        $('#search-buku').on('input', function() {
            clearTimeout(searchDebounce);
            const val = $(this).val();
            searchDebounce = setTimeout(function() {
                tabelBuku.search(val).draw();
            }, 350);
        });

        // Toggle sembunyikan/tampilkan seluruh kotak Pencarian & Filter
        window.toggleKoleksiBukuFilter = function() {
            const wrap = document.getElementById('koleksi-buku-filter-wrap');
            const isHidden = wrap.classList.toggle('hidden');
            localStorage.setItem('bukuFilterOpen', isHidden ? 'false' : 'true');
            document.getElementById('icon-toggle-filter-buku').classList.toggle('rotate-180', isHidden);
        };

        // Di mobile/tablet (<1024px), panel filter selalu default tertutup tiap halaman dimuat.
        // Di desktop, ikuti preferensi terakhir yang tersimpan (perilaku lama, tidak berubah).
        const isMobileFilterView = window.matchMedia('(max-width: 1023px)').matches;
        const filterOpen = isMobileFilterView ? false : (localStorage.getItem('bukuFilterOpen') !== 'false');
        if (!filterOpen) {
            document.getElementById('koleksi-buku-filter-wrap').classList.add('hidden');
            document.getElementById('icon-toggle-filter-buku').classList.add('rotate-180');
        }

        // Toggle tampilan Grid / List.
        // Catatan: di dalam mode List, pemilihan tabel (desktop) vs kartu (mobile)
        // sepenuhnya ditentukan media query di blok <style> halaman ini — JS cukup
        // menoggle class "hidden" untuk memilih mode Grid vs List saja.
        window.setKoleksiBukuView = function(mode) {
            localStorage.setItem('koleksiBukuView', mode);
            const btnGrid = document.getElementById('btn-view-grid-buku');
            const btnList = document.getElementById('btn-view-list-buku');
            const activeClass = ['bg-white', 'text-brand-700', 'shadow-xs', 'border', 'border-gray-200'];
            const inactiveClass = ['text-gray-500'];

            if (mode === 'grid') {
                $('#tabel-buku').addClass('hidden');
                $('#list-buku-mobile-container').addClass('hidden');
                $('#grid-buku-container').removeClass('hidden');
                btnGrid.classList.add(...activeClass);
                btnGrid.classList.remove(...inactiveClass);
                btnList.classList.remove(...activeClass);
                btnList.classList.add(...inactiveClass);
            } else {
                $('#tabel-buku').removeClass('hidden');
                $('#list-buku-mobile-container').removeClass('hidden');
                $('#grid-buku-container').addClass('hidden');
                btnList.classList.add(...activeClass);
                btnList.classList.remove(...inactiveClass);
                btnGrid.classList.remove(...activeClass);
                btnGrid.classList.add(...inactiveClass);

            }
        };

        const savedView = localStorage.getItem('koleksiBukuView') || 'list';
        setKoleksiBukuView(savedView);

        // Baca ?search= dari URL dan langsung filter DataTable
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');
        if (searchQuery && searchQuery.trim() !== '') {
            $('#search-buku').val(searchQuery.trim());
            tabelBuku.search(searchQuery.trim()).draw();

            // Tampilkan banner notifikasi filter aktif
            const banner = document.getElementById('search-filter-banner');
            const bannerText = document.getElementById('search-filter-text');
            if (banner && bannerText) {
                bannerText.textContent = 'Menampilkan hasil untuk: "' + searchQuery.trim() + '"';
                banner.classList.remove('hidden');
            }
        }

        // Tombol reset filter banner
        $(document).on('click', '#btn-reset-search-filter', function() {
            $('#search-buku').val('');
            tabelBuku.search('').draw();
            document.getElementById('search-filter-banner').classList.add('hidden');
            // Bersihkan ?search= dari URL tanpa reload halaman
            const url = new URL(window.location);
            url.searchParams.delete('search');
            window.history.replaceState({}, '', url);
        });

        $(document).on('click', '.btn-edit-buku', function() {
            const rawData = $(this).attr('data-buku');
            if (rawData) {
                const parsed = JSON.parse(rawData);
                const container = document.getElementById('buku-container');
                if (container && container._x_dataStack) {
                    const alpineData = container._x_dataStack[0];
                    alpineData.editData = parsed;
                    alpineData.editRakId = parsed.rak_id ? String(parsed.rak_id) : '';
                    alpineData.editLaciId = parsed.rak_laci_id ? String(parsed.rak_laci_id) : '';
                    alpineData.openEditModal = true;
                }
            }
        });
    });
</script>
@endpush
