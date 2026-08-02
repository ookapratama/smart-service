<?php

namespace App\Services\Wa;

use App\Contracts\Services\WhatsAppNotifier;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Driver Fonnte WhatsApp Gateway: mengirim pesan & interactive buttons via HTTP API Fonnte.
 */
class FonnteWhatsAppNotifier implements WhatsAppNotifier
{
    public function send(string $phone, string $message, array $options = []): bool
    {
        $token = Setting::getByKey('wa_gateway_token')
            ?: Setting::getByKey('fonnte_token')
            ?: config('services.fonnte.token')
            ?: env('FONNTE_TOKEN');

        $token = trim((string) $token, "'\" ");

        if (empty($token)) {
            Log::error('[WA-FONNTE] Token Fonnte belum dikonfigurasi di settings / environment.');
            return false;
        }

        $apiUrl = Setting::getByKey('wa_gateway_url')
            ?: config('services.fonnte.url', 'https://api.fonnte.com/send');

        $payload = [
            'target' => $phone,
            'message' => $message,
            'countryCode' => '62',
        ];

        if (!empty($options['buttonJSON'])) {
            $payload['buttonJSON'] = is_array($options['buttonJSON']) ? json_encode($options['buttonJSON']) : $options['buttonJSON'];
        } elseif (!empty($options['button'])) {
            $payload['button'] = $options['button'];
        }

        if (!empty($options['footer'])) {
            $payload['footer'] = $options['footer'];
        }

        if (!empty($options['filename'])) {
            $payload['filename'] = $options['filename'];
        }

        if (!empty($options['url'])) {
            $payload['url'] = $options['url'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post($apiUrl, $payload);

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

    /**
     * Kirim pesan ke Fonnte API dengan format title, description, footer, dan buttonJSON.
     *
     * @param string $target Nomor WA tujuan (mis. 08123456789)
     * @param string $title Judul utama pesan
     * @param string $description Detail isi pesan (message body)
     * @param array $buttons Array tombol (contoh: ['Cek Status', 'http://127.0.0.1:8000/cek-status'] atau array berstruktur)
     * @param string $footer Text footer (Default: "Pemerintah Kecamatan Soreang")
     * @return array API Response JSON dari Fonnte
     */
    public function sendWithButtons(
        string $target,
        string $title,
        string $description,
        array $buttons = [],
        string $footer = 'Pemerintah Kecamatan Soreang'
    ): array {
        $token = Setting::getByKey('wa_gateway_token')
            ?: Setting::getByKey('fonnte_token')
            ?: config('services.fonnte.token')
            ?: env('FONNTE_TOKEN');

        $token = trim((string) $token, "'\" ");

        if (empty($token)) {
            Log::error('[WA-FONNTE] Token Fonnte belum dikonfigurasi di settings / environment.');
            return [
                'status' => false,
                'detail' => 'Token Fonnte belum dikonfigurasi.',
            ];
        }

        $apiUrl = Setting::getByKey('wa_gateway_url')
            ?: config('services.fonnte.url', 'https://api.fonnte.com/send');

        // Formatted Text (Judul + Detail)
        $formattedMessage = "*{$title}*\n\n{$description}";

        // Format buttonJSON sesuai dokumentasi Fonnte API
        $buttonConfig = [];

        // Kasus: ['Cek Status', 'http://link-cek-status']
        if (count($buttons) === 2 && isset($buttons[0]) && isset($buttons[1]) && !is_array($buttons[0])) {
            $label = (string) $buttons[0];
            $actionMsg = (string) $buttons[1];
            $id = 'btn_' . Str::slug($label, '_');

            $buttonConfig[] = [
                'id' => $id,
                'message' => $actionMsg,
                'displayText' => $label,
            ];
        } else {
            foreach ($buttons as $key => $val) {
                if (is_array($val)) {
                    $label = $val['label'] ?? $val['text'] ?? $val['displayText'] ?? (string) $key;
                    $id = $val['id'] ?? 'btn_' . Str::slug($label, '_');
                    $msg = $val['message'] ?? $val['url'] ?? $val['copy'] ?? $val['action'] ?? '';

                    $item = [
                        'id' => $id,
                        'message' => $msg,
                    ];
                    if (!empty($label)) {
                        $item['displayText'] = $label;
                    }
                    $buttonConfig[] = $item;
                } elseif (is_string($key) && is_string($val)) {
                    $label = $key;
                    $id = 'btn_' . Str::slug($label, '_');
                    $buttonConfig[] = [
                        'id' => $id,
                        'message' => $val,
                        'displayText' => $label,
                    ];
                }
            }
        }

        $payload = [
            'target' => $target,
            'message' => $formattedMessage,
            'footer' => $footer,
            'countryCode' => '62',
        ];

        if (!empty($buttonConfig)) {
            $payload['buttonJSON'] = json_encode(['buttons' => $buttonConfig]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post($apiUrl, $payload);

            $result = $response->json();
            return is_array($result) ? $result : [
                'status' => $response->successful(),
                'body' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('[WA-FONNTE] Exception in sendWithButtons: ' . $e->getMessage(), ['target' => $target]);
            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
