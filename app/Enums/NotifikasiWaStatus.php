<?php

namespace App\Enums;

enum NotifikasiWaStatus: string
{
    case Pending = 'pending';
    case Terkirim = 'terkirim';
    case Gagal = 'gagal';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Terkirim => 'Terkirim',
            self::Gagal => 'Gagal',
        };
    }
}
