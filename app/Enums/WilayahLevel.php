<?php

namespace App\Enums;

/**
 * Level administratif mengikuti kode wilayah.id: 1=provinsi ... 4=desa/kelurahan.
 * Sinkronisasi saat ini hanya sampai level 3 (kecamatan) — lihat WilayahSyncService.
 */
enum WilayahLevel: int
{
    case Provinsi = 1;
    case KabupatenKota = 2;
    case Kecamatan = 3;
    case Desa = 4;

    public function label(): string
    {
        return match ($this) {
            self::Provinsi => 'Provinsi',
            self::KabupatenKota => 'Kabupaten/Kota',
            self::Kecamatan => 'Kecamatan',
            self::Desa => 'Desa/Kelurahan',
        };
    }
}
