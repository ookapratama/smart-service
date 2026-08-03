{{-- Template surat generik (fallback semua jenis tanpa template khusus). dompdf: CSS terbatas — tabel & inline style saja. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $jenisSurat->nama }} - {{ $nomorSurat ?? 'Draft' }}</title>
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
            {{ $jenisSurat->nama }}
        </div>
        <div style="font-size: 12px;">Nomor: {{ $nomorSurat ?? '............................' }}</div>
    </div>

    <p style="margin-top: 24px;">
        Yang bertanda tangan di bawah ini, {{ $penandatangan['jabatan'] ?? 'Camat Soreang' }}
        {{ $profil['kota'] ?? 'Kota Parepare' }}, dengan ini menerangkan bahwa:
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
            <td style="padding: 2px 0;">Alamat</td>
            <td>:</td>
            <td>{{ $pemohon->alamat ?? '-' }}@if ($pemohon?->kelurahan), Kelurahan {{ $pemohon->kelurahan->nama }}@endif</td>
        </tr>
        @foreach ($pengajuan->data ?? [] as $fieldName => $fieldValue)
            @php
                $fieldLabel = collect($jenisSurat->fields ?? [])->firstWhere('name', $fieldName)['label']
                    ?? \Illuminate\Support\Str::headline($fieldName);
            @endphp
            <tr>
                <td style="padding: 2px 0;">{{ $fieldLabel }}</td>
                <td>:</td>
                <td>{{ is_array($fieldValue) ? implode(', ', $fieldValue) : $fieldValue }}</td>
            </tr>
        @endforeach
    </table>

    <p style="margin-top: 16px;">
        Surat keterangan ini diterbitkan atas permohonan yang bersangkutan untuk keperluan:
        <strong>{{ $pengajuan->keperluan }}</strong>.
    </p>

    <p>
        Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
    </p>

    @include('surat.templates.partials.ttd')

</body>
</html>
