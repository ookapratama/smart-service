{{-- Template khusus SKD (Surat Keterangan Domisili) — contoh mekanisme template per-jenis ({kode}.blade.php). --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Domisili - {{ $nomorSurat ?? 'Draft' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #000; margin: 24px 36px; }
        table { border-collapse: collapse; }
        p { line-height: 1.6; text-align: justify; }
    </style>
</head>
<body>

    @include('surat.templates.partials.kop')

    <div style="text-align: center; margin-top: 24px;">
        <div style="font-size: 14px; font-weight: bold; text-decoration: underline; text-transform: uppercase;">
            Surat Keterangan Domisili
        </div>
        <div style="font-size: 12px;">Nomor: {{ $nomorSurat ?? '............................' }}</div>
    </div>

    <p style="margin-top: 24px;">
        Yang bertanda tangan di bawah ini, Camat Soreang {{ $profil['kota'] ?? 'Kota Parepare' }},
        dengan ini menerangkan bahwa:
    </p>

    <table style="width: 100%; margin-left: 24px;">
        <tr>
            <td style="width: 180px; padding: 2px 0;">Nama</td>
            <td style="width: 12px;">:</td>
            <td style="font-weight: bold;">{{ $pemohon->name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">NIK</td>
            <td>:</td>
            <td>{{ $pemohon->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Alamat Domisili</td>
            <td>:</td>
            <td>{{ $pengajuan->data['alamat_domisili'] ?? ($pemohon->alamat ?? '-') }}@if ($pemohon?->kelurahan), Kelurahan {{ $pemohon->kelurahan->nama }}@endif</td>
        </tr>
    </table>

    <p style="margin-top: 16px;">
        Berdasarkan data yang ada, nama tersebut di atas benar berdomisili dan bertempat tinggal di alamat tersebut,
        wilayah {{ $profil['kecamatan'] ?? 'Kecamatan Soreang' }}, {{ $profil['kota'] ?? 'Kota Parepare' }}@if (!empty($pengajuan->data['sejak_tahun'])),
        sejak tahun {{ $pengajuan->data['sejak_tahun'] }}@endif.
    </p>

    <p>
        Surat keterangan domisili ini diterbitkan untuk keperluan:
        <strong>{{ $pengajuan->data['keperluan_domisili'] ?? $pengajuan->keperluan }}</strong>.
    </p>

    <p>
        Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
    </p>

    <table style="width: 100%; margin-top: 32px;">
        <tr>
            <td style="width: 55%;"></td>
            <td style="text-align: center;">
                <div>Parepare, {{ $tanggal }}</div>
                <div>a.n. Camat Soreang</div>
                <div style="height: 70px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">(..............................................)</div>
                <div>NIP. ..............................................</div>
            </td>
        </tr>
    </table>

</body>
</html>
