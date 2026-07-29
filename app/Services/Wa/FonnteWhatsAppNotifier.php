<?php

namespace App\Services\Wa;

use App\Contracts\Services\WhatsAppNotifier;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Driver Fonnte WhatsApp Gateway: mengirim pesan via HTTP API Fonnte.
 */
class FonnteWhatsAppNotifier implements WhatsAppNotifier
{
    public function send(string $phone, string $message): bool
    {
        $token = Setting::getByKey('wa_gateway_token')
            ?: Setting::getByKey('fonnte_token')
            ?: config('services.fonnte.token')
            ?: env('FONNTE_TOKEN');

        if (empty($token)) {
            Log::error('[WA-FONNTE] Token Fonnte belum dikonfigurasi di settings / environment.');
            return false;
        }

        $apiUrl = Setting::getByKey('wa_gateway_url')
            ?: config('services.fonnte.url', 'https://api.fonnte.com/send');

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post($apiUrl, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                if (is_array($resData) && isset($resData['status']) && $resData['status'] === false) {
                    Log::error('[WA-FONNTE] Fonnte API error response', ['res' => $resData, 'to' => $phone]);
                    return false;
                }
                Log::info('[WA-FONNTE] Message sent successfully', ['to' => $phone]);
                return true;
            }

            Log::error('[WA-FONNTE] HTTP request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'to' => $phone,
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('[WA-FONNTE] Exception when sending WA: ' . $e->getMessage(), ['to' => $phone]);
            return false;
        }
    }
}
