{{-- Kolom tanda tangan di kaki laporan. Lebar tiap kolomnya berbeda antar
     laporan karena jumlah kolom tabelnya berbeda, jadi dioper dari pemanggil:
     $awal = kolom kosong pembuka (0 bila tidak ada), lalu $kiri/$tengah/$kanan. --}}
<table>
    <tr><td colspan="{{ $kolom }}"></td></tr>
    <tr><td colspan="{{ $kolom }}"></td></tr>
    <tr>
        @if ($awal > 0)
            <td colspan="{{ $awal }}"></td>
        @endif
        <td colspan="{{ $kiri }}" class="text-center" style="font-size: 9.5pt; vertical-align: top;">
            Petugas Administrasi Perpustakaan,<br/><br/><br/><br/>
            <strong><u>{{ $identitas['petugas'] }}</u></strong><br/>
            Admin Sirkulasi
        </td>
        <td colspan="{{ $tengah }}"></td>
        <td colspan="{{ $kanan }}" class="text-center" style="font-size: 9.5pt; vertical-align: top;">
            {{ $identitas['kota'] ? $identitas['kota'] . ', ' : '' }}{{ date('d F Y') }}<br/>
            Mengetahui,<br/>
            <strong>Kepala Perpustakaan</strong><br/><br/><br/><br/>
            <strong><u>{{ $identitas['kepala'] }}</u></strong><br/>
            NIP. {{ $identitas['nip_kepala'] }}
        </td>
    </tr>
</table>
