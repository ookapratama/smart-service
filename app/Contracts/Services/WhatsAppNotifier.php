<?php

namespace App\Contracts\Services;

interface WhatsAppNotifier
{
    /**
     * Kirim pesan WhatsApp ke nomor tujuan.
     * Return true jika pesan diterima driver/gateway (bukan jaminan delivered).
     */
    public function send(string $phone, string $message): bool;
}
