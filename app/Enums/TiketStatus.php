<?php

namespace App\Enums;

enum TiketStatus: string
{
    case Baru = 'baru';
    case Diproses = 'diproses';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Baru => 'Baru',
            self::Diproses => 'Diproses',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
        };
    }

    /**
     * Status lanjutan yang valid dari status saat ini.
     *
     * @return array<self>
     */
    public function transitions(): array
    {
        return match ($this) {
            self::Baru => [self::Diproses, self::Ditolak],
            self::Diproses => [self::Selesai, self::Ditolak],
            self::Selesai, self::Ditolak => [],
        };
    }
}
