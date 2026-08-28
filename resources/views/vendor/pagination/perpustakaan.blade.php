{{--
    Navigasi antar halaman untuk semua daftar yang dipaginasi Laravel.

    Tampilannya sengaja dibuat sama persis dengan pagination DataTables di
    halaman Koleksi Buku, supaya pengguna tidak menemui dua gaya tombol yang
    berbeda untuk pekerjaan yang sama. Nilai warna dan ukurannya disalin dari
    aturan `.dataTables_paginate .paginate_button` di admin/buku/index.blade.php.

    Teks satuannya bisa diganti lewat parameter kedua `links()`, mis.
    `$bukuList->links('vendor.pagination.perpustakaan', ['satuan' => 'buku'])`.
--}}
@if ($paginator->hasPages())
    @once
        <style>
            /* Ditulis sebagai CSS polos, bukan kelas Tailwind, dengan alasan yang
               sama seperti tabel Koleksi Buku: public/vendor/tailwind/tailwind.min.css
               adalah hasil purge, jadi kelas yang belum pernah muncul di blade
               mana pun tidak ikut ter-generate dan diam-diam tidak berefek. */
            .paginasi {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.75rem;
            }
            .paginasi__info {
                font-size: 0.75rem;
                font-weight: 600;
                color: #6b7280;
                margin: 0;
                text-align: center;
            }
            .paginasi__info b {
                font-weight: 800;
                color: #111827;
            }
            .paginasi__nav {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 0.25rem;
            }
            .paginasi__btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 2rem;
                padding: 0.35rem 0.65rem;
                font-size: 0.75rem;
                font-weight: 700;
                line-height: 1;
                border-radius: 0.5rem;
                border: 1px solid #e5e7eb;
                background: #ffffff;
                color: #374151;
                text-decoration: none;
                transition: all 0.15s ease-in-out;
            }
            .paginasi__btn:hover {
                background: #fee2e2;
                border-color: #fca5a5;
                color: #b91c1c;
            }
            .paginasi__btn--aktif,
            .paginasi__btn--aktif:hover {
                background: #b91c1c;
                border-color: #b91c1c;
                color: #ffffff;
                cursor: default;
            }
            .paginasi__btn--mati,
            .paginasi__btn--mati:hover {
                opacity: 0.4;
                cursor: not-allowed;
                background: #f9fafb;
                border-color: #e5e7eb;
                color: #9ca3af;
            }
            .paginasi__sela {
                padding: 0 0.35rem;
                font-size: 0.75rem;
                font-weight: 700;
                color: #9ca3af;
            }
            /* Di layar lebar keterangan jumlah duduk di kiri, tombol di kanan --
               persis seperti .dataTables_info dan .dataTables_paginate. */
            @media (min-width: 640px) {
                .paginasi {
                    flex-direction: row;
                    justify-content: space-between;
                }
                .paginasi__info { text-align: left; }
            }
        </style>
    @endonce

    <nav class="paginasi" role="navigation" aria-label="Navigasi halaman">
        <p class="paginasi__info">
            Menampilkan <b>{{ number_format($paginator->firstItem()) }}</b>
            sampai <b>{{ number_format($paginator->lastItem()) }}</b>
            dari <b>{{ number_format($paginator->total()) }}</b> {{ $satuan ?? 'data' }}
        </p>

        <div class="paginasi__nav">
            @if ($paginator->onFirstPage())
                <span class="paginasi__btn paginasi__btn--mati" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="paginasi__btn" aria-label="Halaman sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="paginasi__sela" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="paginasi__btn paginasi__btn--aktif" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="paginasi__btn" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="paginasi__btn" aria-label="Halaman berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="paginasi__btn paginasi__btn--mati" aria-disabled="true" aria-label="Halaman berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
