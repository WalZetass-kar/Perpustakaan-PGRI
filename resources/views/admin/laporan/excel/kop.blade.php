{{-- Kop laporan: identitas perpustakaan + judul laporan.
     $kolom = lebar tabel dalam kolom, $judul = judul laporannya. --}}
<tr>
    <td colspan="{{ $kolom }}" class="banner-top">{{ strtoupper($identitas['nama_perpustakaan']) }}</td>
</tr>
<tr>
    <td colspan="{{ $kolom }}" class="banner-sub">{{ strtoupper($identitas['nama_sekolah']) }} &bull; NPSN: {{ $identitas['npsn'] }} &bull; {{ $identitas['alamat'] }}</td>
</tr>
<tr>
    <td colspan="{{ $kolom }}" class="banner-ribbon"></td>
</tr>
<tr>
    <td colspan="{{ $kolom }}" class="banner-title">
        {!! $judul !!}
    </td>
</tr>
<tr><td colspan="{{ $kolom }}"></td></tr>
