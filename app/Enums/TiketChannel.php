<?php

namespace App\Enums;

enum TiketChannel: string
{
    case Web = 'web';
    case Whatsapp = 'whatsapp';
    case Qr = 'qr';
    case Mobile = 'mobile';
    case Walkin = 'walkin';

    public function label(): string
    {
        return match ($this) {
            self::Web => 'Website',
            self::Whatsapp => 'WhatsApp',
            self::Qr => 'QR Code',
            self::Mobile => 'Aplikasi Mobile',
            self::Walkin => 'Datang Langsung',
        };
    }
}
