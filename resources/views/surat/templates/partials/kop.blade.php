{{-- Kop surat resmi kecamatan — data dari settings profile_* (dompdf-safe: tabel + CSS inline sederhana) --}}
<table style="width: 100%; border-bottom: 3px double #000; padding-bottom: 8px;">
    <tr>
        <td style="text-align: center;">
            <div style="font-size: 14px; font-weight: bold; letter-spacing: 1px;">PEMERINTAH {{ strtoupper($profil['kota'] ?? 'KOTA PAREPARE') }}</div>
            <div style="font-size: 18px; font-weight: bold; letter-spacing: 2px;">{{ strtoupper($profil['kecamatan'] ?? 'KECAMATAN SOREANG') }}</div>
            <div style="font-size: 10px; margin-top: 4px;">{{ $profil['alamat'] ?? '' }}</div>
            <div style="font-size: 10px;">
                @if (!empty($profil['telepon'])) Telp. {{ $profil['telepon'] }} @endif
                @if (!empty($profil['email'])) &mdash; Email: {{ $profil['email'] }} @endif
            </div>
        </td>
    </tr>
</table>
