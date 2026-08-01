<?php

namespace App\Contracts\Services;

interface WhatsAppNotifier
{
    /**
     * Kirim pesan WhatsApp ke nomor tujuan dengan opsi tambahan (seperti tombol interactive button Fonnte).
     * Return true jika pesan diterima driver/gateway.
     */
    public function send(string $phone, string $message, array $options = []): bool;
}
