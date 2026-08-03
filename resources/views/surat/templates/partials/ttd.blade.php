{{-- Blok tanda tangan — penandatangan dari settings (ttd_jabatan/ttd_nama/ttd_nip). --}}
@php
    $kotaTtd = preg_replace('/^(Kota|Kabupaten)\s+/i', '', $profil['kota'] ?? 'Parepare');
    $jabatan = $penandatangan['jabatan'] ?? 'Camat Soreang';
    $namaTtd = $penandatangan['nama'] ?? null;
    $nipTtd = $penandatangan['nip'] ?? null;
@endphp
<table style="width: 100%; margin-top: 32px;">
    <tr>
        <td style="width: 55%;"></td>
        <td style="text-align: center;">
            <div>{{ $kotaTtd }}, {{ $tanggal }}</div>
            <div>{{ $jabatan }}</div>
            <div style="height: 70px;"></div>
            <div style="font-weight: bold; text-decoration: underline;">
                {{ $namaTtd ?: '(..............................................)' }}
            </div>
            <div>NIP. {{ $nipTtd ?: '..............................................' }}</div>
        </td>
    </tr>
</table>
