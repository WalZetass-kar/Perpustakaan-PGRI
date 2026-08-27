{{-- Baris ringkasan angka di atas tabel.
     $kartu = [['label' => ..., 'nilai' => ..., 'satuan' => ..., 'warna' => 'blue', 'kolom' => 3], ...] --}}
<tr>
    @foreach ($kartu as $k)
        <td colspan="{{ $k['kolom'] }}" class="kpi-head-{{ $k['warna'] }}">{{ $k['label'] }}</td>
    @endforeach
</tr>
<tr>
    @foreach ($kartu as $k)
        <td colspan="{{ $k['kolom'] }}" class="kpi-val-{{ $k['warna'] }} mso-num">{{ number_format($k['nilai'], 0, ',', '.') }} {{ $k['satuan'] }}</td>
    @endforeach
</tr>
