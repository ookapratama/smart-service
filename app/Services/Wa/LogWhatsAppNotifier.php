<?php

namespace App\Services\Wa;

use App\Contracts\Services\WhatsAppNotifier;
use Illuminate\Support\Facades\Log;

/**
 * Driver dev/test: tulis pesan WA ke log alih-alih mengirim sungguhan.
 * Driver HTTP gateway (Fonnte/Watzap/sejenis) menyusul tanpa mengubah call site.
 */
class LogWhatsAppNotifier implements WhatsAppNotifier
{
    public const LOG_PREFIX = '[WA-LOG]';

    public function send(string $phone, string $message): bool
    {
        Log::info(sprintf('%s to=%s message=%s', self::LOG_PREFIX, $phone, $message));

        return true;
    }
}
