<?php

namespace App\Enums;

enum InstansiLevel: string
{
    case Kabupaten = 'kabupaten';
    case Kecamatan = 'kecamatan';
    case Kelurahan = 'kelurahan';

    public function label(): string
    {
        return match ($this) {
            self::Kabupaten => 'Kabupaten',
            self::Kecamatan => 'Kecamatan',
            self::Kelurahan => 'Kelurahan/Desa',
        };
    }
}
