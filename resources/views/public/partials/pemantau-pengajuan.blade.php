{{--
    Pemantau keputusan pengajuan peminjaman (OPAC).

    Dipakai dua halaman sekaligus — katalog dan detail buku — yang formulir
    pengajuannya memang kembar. Ditaruh di satu berkas supaya perbaikan di
    kemudian hari tidak perlu diingat dua kali.

    Alurnya: setelah popup "Pengajuan Terkirim" ditutup siswa, popup ini
    menggantikannya dan menanyakan keputusan petugas berulang kali sampai ada
    jawabannya — diterima (centang) atau ditolak (silang beserta alasannya).

    Pemantauan sengaja hanya hidup selama halaman terbuka. Siswa yang menutup
    halaman tidak kehilangan apa pun: pengajuannya tetap tercatat dan tinggal
    ditanyakan di meja sirkulasi dengan kode referensinya.
--}}
<script>
(function () {
    'use strict';

    var POLA_URL  = @json(route('katalog.pengajuan.status', ['id' => '__ID__']));
    var JEDA_MS   = 5000;             // jarak antar pemeriksaan
    var BATAS_MS  = 5 * 60 * 1000;    // sesudah ini berhenti sendiri, bukan error
    var WARNA     = '#991b1b';

    function aman(teks) {
        var el = document.createElement('div');
        el.textContent = (teks === null || teks === undefined) ? '' : String(teks);
        return el.innerHTML;
    }

    function blokKode(kode) {
        return '<p class="text-sm font-mono font-bold text-brand-800 bg-gray-100 py-1.5 px-3 rounded-lg border border-gray-200">Kode Ref: '
             + aman(kode) + '</p>';
    }

    /**
     * Pesan kegagalan pengajuan yang bisa dimengerti siswa.
     *
     * Dipisah dari pemanggilnya karena satu status perlu perlakuan khusus: 419.
     * Status itu muncul ketika token CSRF halaman sudah tidak berlaku — paling
     * sering karena ada yang login petugas di peramban yang sama, yang membuat
     * Laravel meregenerasi token demi menangkal session fixation. Pesan
     * bawaannya, "CSRF token mismatch.", tidak berarti apa-apa bagi siswa dan
     * tidak memberi tahu bahwa cukup memuat ulang halaman.
     *
     * @param {number} status  kode status HTTP dari server
     * @param {string} pesan   pesan dari server, untuk kegagalan biasa
     */
    window.tampilkanKegagalanPengajuan = function (status, pesan) {
        var teks = pesan || 'Gagal mengajukan peminjaman buku.';

        if (typeof Swal === 'undefined') {
            alert(status === 419 ? 'Halaman sudah kedaluwarsa. Muat ulang halaman lalu ajukan kembali.' : teks);
            return;
        }

        if (status === 419) {
            Swal.fire({
                icon: 'warning',
                title: 'Halaman Perlu Dimuat Ulang',
                html: '<p class="text-xs text-gray-600 mb-2">Sesi halaman ini sudah kedaluwarsa, biasanya karena ada aktivitas masuk petugas di peramban yang sama.</p>'
                    + '<p class="text-[11px] text-gray-500">Muat ulang halaman lalu ajukan kembali. Data buku yang Anda pilih tidak hilang.</p>',
                showCancelButton: true,
                confirmButtonText: 'Muat Ulang',
                cancelButtonText: 'Nanti',
                confirmButtonColor: WARNA,
                cancelButtonColor: '#6b7280'
            }).then(function (hasil) {
                if (hasil && hasil.isConfirmed) { window.location.reload(); }
            });
            return;
        }

        Swal.fire({
            icon: 'error',
            title: 'Pengajuan Gagal',
            text: teks,
            confirmButtonColor: WARNA
        });
    };

    /**
     * @param {number|string} id    id pengajuan yang baru dikirim
     * @param {string} judul        judul buku, untuk ditampilkan kembali
     * @param {string} kode         kode referensi pengajuan
     */
    window.pantauPengajuan = function (id, judul, kode) {
        if (!id || typeof Swal === 'undefined') {
            return;
        }

        var url     = POLA_URL.replace('__ID__', encodeURIComponent(id));
        var mulai   = Date.now();
        var timer   = null;
        var berhenti = false;

        function hentikan() {
            berhenti = true;
            if (timer) { clearTimeout(timer); timer = null; }
            document.removeEventListener('visibilitychange', saatKembali);
        }

        function jadwalkan() {
            if (berhenti) { return; }
            timer = setTimeout(periksa, JEDA_MS);
        }

        function saatKembali() {
            // Siswa membuka lagi tabnya: jangan menunggu sisa jeda, langsung
            // periksa supaya keputusan yang sudah ada tidak terasa lambat.
            if (document.hidden || berhenti) { return; }
            if (timer) { clearTimeout(timer); timer = null; }
            periksa();
        }

        function periksa() {
            if (berhenti) { return; }

            if (Date.now() - mulai > BATAS_MS) {
                hentikan();
                tampilkanBelumDiproses();
                return;
            }

            // Tab yang tidak terlihat tidak perlu membebani server sekolah;
            // penantiannya tetap berjalan, hanya permintaannya yang ditunda.
            if (document.hidden) { jadwalkan(); return; }

            fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(r.status); })
            .then(function (data) {
                if (berhenti) { return; }

                if (data.status === 'pending') { jadwalkan(); return; }

                hentikan();
                if (data.status === 'ditolak') {
                    tampilkanDitolak(data);
                } else {
                    tampilkanDiterima(data);
                }
            })
            .catch(function () {
                // Gangguan jaringan sesaat bukan alasan menyerah — WiFi sekolah
                // putus-nyambung itu biasa. Batas waktu di atas yang menutupnya.
                jadwalkan();
            });
        }

        /**
         * Angka stok dan antrean di katalog ditanam ke HTML saat halaman
         * dirender, jadi keputusan petugas yang baru saja masuk membuatnya
         * basi — buku yang sudah dipotong stoknya masih tampil utuh sampai
         * halaman dimuat ulang. Keduanya berubah baik saat disetujui (stok dan
         * antrean berkurang) maupun ditolak (antrean berkurang), jadi
         * pemuatan ulang dilakukan sesudah siswa membaca hasilnya.
         *
         * Dipilih memuat ulang seluruh halaman, bukan menambal angkanya lewat
         * JS: stok yang sama tampil di kartu grid, kartu daftar, dan modal
         * detail sekaligus, dan menambal ketiganya jauh lebih mudah meleset
         * daripada meminta ulang datanya ke server.
         */
        function muatUlangHalaman() {
            window.location.reload();
        }

        function tampilkanDiterima(data) {
            var jatuhTempo = data.jatuh_tempo
                ? '<p class="text-[11px] text-gray-500 mt-2">Harap dikembalikan paling lambat <strong>' + aman(data.jatuh_tempo) + '</strong>.</p>'
                : '';

            Swal.fire({
                icon: 'success',
                title: 'Peminjaman Diterima!',
                html: '<p class="text-xs text-gray-600 mb-2">Pengajuan buku <strong>' + aman(data.judul_buku || judul)
                    + '</strong> telah disetujui petugas perpustakaan.</p>'
                    + blokKode(data.kode || kode)
                    + '<p class="text-[11px] text-gray-500 mt-2">Silakan ambil bukunya di meja sirkulasi dengan menunjukkan kode di atas.</p>'
                    + jatuhTempo,
                confirmButtonColor: WARNA
            }).then(muatUlangHalaman);
        }

        function tampilkanDitolak(data) {
            var alasan = data.alasan_penolakan
                ? '<p class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg py-2 px-3 mt-2 text-left"><strong>Alasan:</strong> '
                  + aman(data.alasan_penolakan) + '</p>'
                : '';

            Swal.fire({
                icon: 'error',
                title: 'Pengajuan Ditolak',
                html: '<p class="text-xs text-gray-600 mb-2">Pengajuan buku <strong>' + aman(data.judul_buku || judul)
                    + '</strong> tidak dapat diproses.</p>'
                    + alasan
                    + '<p class="text-[11px] text-gray-500 mt-2">Silakan tanyakan ke petugas perpustakaan bila perlu penjelasan lebih lanjut.</p>',
                confirmButtonColor: WARNA
            }).then(muatUlangHalaman);
        }

        function tampilkanBelumDiproses() {
            Swal.fire({
                icon: 'info',
                title: 'Belum Diproses',
                html: '<p class="text-xs text-gray-600 mb-2">Petugas perpustakaan belum memberi keputusan atas pengajuan buku <strong>'
                    + aman(judul) + '</strong>.</p>'
                    + blokKode(kode)
                    + '<p class="text-[11px] text-gray-500 mt-2">Pengajuan Anda tetap tercatat. Simpan kode di atas dan tanyakan di meja sirkulasi perpustakaan.</p>',
                confirmButtonColor: WARNA
            });
        }

        document.addEventListener('visibilitychange', saatKembali);

        Swal.fire({
            title: 'Menunggu Verifikasi Petugas',
            html: '<p class="text-xs text-gray-600 mb-3"><i class="fa-solid fa-spinner fa-spin text-brand-800 mr-1"></i> Pengajuan buku <strong>'
                + aman(judul) + '</strong> sedang menunggu diperiksa petugas perpustakaan.</p>'
                + blokKode(kode)
                + '<p class="text-[11px] text-gray-500 mt-2">Hasilnya akan muncul di sini begitu petugas memutuskan. Halaman ini boleh ditutup — pengajuan Anda tetap tercatat.</p>',
            allowOutsideClick: false,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Tutup',
            cancelButtonColor: '#6b7280'
        }).then(function (hasil) {
            // Ditutup sendiri oleh siswa: berhenti memantau. Kalau popup ini
            // tergantikan popup keputusan, pemantauannya memang sudah berhenti
            // lebih dulu sehingga pemanggilan ini tidak berpengaruh.
            if (hasil && hasil.dismiss) { hentikan(); }
        });

        jadwalkan();
    };
})();
</script>
