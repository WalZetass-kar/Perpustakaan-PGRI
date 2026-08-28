{{--
    Perapian kolom isian yang berlaku di seluruh halaman.

    Disimpan sebagai satu berkas dan di-include dari setiap layout, bukan
    disalin ke masing-masing, supaya tidak ada layout yang tertinggal saat
    aturannya berubah.

    Ditulis sebagai CSS polos karena sasarannya elemen dan pseudo-element
    bawaan browser, yang memang tidak punya padanan kelas Tailwind -- dan
    public/vendor/tailwind/tailwind.min.css di proyek ini adalah berkas statis
    hasil purge yang tidak bisa di-generate ulang.
--}}
<style>
    /* Pegangan resize di pojok kanan bawah textarea. Di beberapa tema ia
       tampak seperti bercak kotor yang menempel di dalam kotak isian. Empat
       textarea di halaman Koleksi Buku sudah lebih dulu dimatikan lewat kelas
       `resize-none`; aturan ini menyamakan sisanya. */
    textarea {
        resize: none;
    }

    /* Tombol panah naik/turun pada kolom angka. Selektor elemen sengaja
       dipakai (bukan kelas) supaya bobotnya rendah: halaman yang kelak
       benar-benar membutuhkan pegangan resize cukup menambahkan kelas
       `resize-y` dan kelas itu tetap menang. */
    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        appearance: none;
        margin: 0;
    }
</style>
