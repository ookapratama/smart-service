<?php

namespace App\Services\Wa;

use App\Contracts\Services\WhatsAppNotifier;
use Illuminate\Support\Facades\Log;

/**
 * Driver dev/test: tulis pesan WA ke log alih-alih mengirim sungguhan.
 */
class LogWhatsAppNotifier implements WhatsAppNotifier
{
    public const LOG_PREFIX = '[WA-LOG]';

    public function send(string $phone, string $message, array $options = []): bool
    {
        Log::info(sprintf('%s to=%s message=%s options=%s', self::LOG_PREFIX, $phone, $message, json_encode($options)));

        return true;
    }
}
