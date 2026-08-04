<?php

namespace App\Services\Wa;

use App\Contracts\Services\WhatsAppNotifier;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Driver WhatsApp Web JS Gateway: mengirim pesan via Node.js microservice (whatsapp-web.js API).
 */
class WhatsAppWebJsNotifier implements WhatsAppNotifier
{
    /**
     * Format nomor HP Indonesia ke format standar (contoh: 08123456789 -> 628123456789)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            return '62' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    public function send(string $phone, string $message, array $options = []): bool
    {
        $apiUrl = Setting::getByKey('wa_gateway_url')
            ?: config('services.whatsapp_web_js.url')
            ?: env('WA_WEB_JS_URL', 'http://127.0.0.1:3000/send-message');

        $secret = Setting::getByKey('wa_gateway_token')
            ?: config('services.whatsapp_web_js.secret')
            ?: env('WA_WEB_JS_SECRET');

        $targetPhone = $this->formatPhoneNumber($phone);

        if (empty($targetPhone)) {
            Log::error('[WA-WEB-JS] Nomor telepon tujuan tidak valid.', ['phone' => $phone]);
            return false;
        }

        $payload = array_merge([
            'phone' => $targetPhone,
            'number' => $targetPhone,
            'message' => $message,
            'options' => $options,
        ], array_filter([
            'title' => $options['title'] ?? null,
            'footer' => $options['footer'] ?? null,
            'buttons' => $options['buttons'] ?? null,
        ]));

        try {
            $request = Http::timeout(10)->asJson();

            if (!empty($secret)) {
                $request = $request->withHeaders([
                    'X-API-Key' => trim((string) $secret, "'\" "),
                    'Authorization' => 'Bearer ' . trim((string) $secret, "'\" "),
                ]);
            }

            $response = $request->post($apiUrl, $payload);

            if ($response->successful()) {
                $resData = $response->json();
                if (is_array($resData) && isset($resData['status']) && $resData['status'] === false) {
                    Log::error('[WA-WEB-JS] WhatsApp Web JS API error response', ['res' => $resData, 'to' => $targetPhone]);
                    return false;
                }
                Log::info('[WA-WEB-JS] Message sent successfully via whatsapp-web.js', ['to' => $targetPhone]);
                return true;
            }

            Log::error('[WA-WEB-JS] HTTP request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'to' => $targetPhone,
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('[WA-WEB-JS] Exception when sending WA: ' . $e->getMessage(), ['to' => $targetPhone]);
            return false;
        }
    }

    /**
     * Kirim pesan ke WhatsApp Web JS dengan format title, description, buttons/links/copy text, dan footer.
     *
     * @param string $target Nomor WA tujuan
     * @param string $title Judul utama pesan
     * @param string $description Detail isi pesan
     * @param array $buttons Array pilihan (Link URL atau Copy Text)
     * @param string $footer Text footer
     * @return array API Response JSON
     */
    public function sendWithButtons(
        string $target,
        string $title,
        string $description,
        array $buttons = [],
        string $footer = 'Pemerintah Kecamatan Soreang'
    ): array {
        $formattedMessage = "*{$title}*\n\n{$description}";

        $nativeButtons = [];

        if (!empty($buttons)) {
            $formattedMessage .= "\n";
            if (count($buttons) === 2 && isset($buttons[0]) && isset($buttons[1]) && !is_array($buttons[0])) {
                $label = (string) $buttons[0];
                $action = (string) $buttons[1];
                $nativeButtons[] = ['body' => $label];
                if (str_starts_with($action, 'http://') || str_starts_with($action, 'https://')) {
                    $formattedMessage .= "\n🔗 *{$label}*:\n{$action}";
                } else {
                    $formattedMessage .= "\n📋 *{$label}*:\n```{$action}```";
                }
            } else {
                foreach ($buttons as $key => $val) {
                    if (is_array($val)) {
                        $label = $val['label'] ?? $val['text'] ?? $val['displayText'] ?? (string) $key;
                        $action = $val['url'] ?? $val['copy'] ?? $val['message'] ?? $val['action'] ?? '';
                        $nativeButtons[] = ['body' => $label];
                        $isCopy = !empty($val['copy']) || (!str_starts_with($action, 'http://') && !str_starts_with($action, 'https://'));
                        if ($isCopy && !empty($action)) {
                            $formattedMessage .= "\n📋 *{$label}*:\n```{$action}```";
                        } else {
                            $formattedMessage .= "\n🔗 *{$label}*:\n{$action}";
                        }
                    } elseif (is_string($key) && is_string($val)) {
                        $nativeButtons[] = ['body' => $key];
                        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
                            $formattedMessage .= "\n🔗 *{$key}*:\n{$val}";
                        } else {
                            $formattedMessage .= "\n📋 *{$key}*:\n```{$val}```";
                        }
                    }
                }
            }
        }

        if (!empty($footer)) {
            $formattedMessage .= "\n\n_{$footer}_";
        }

        $options = [
            'title' => $title,
            'footer' => $footer,
            'buttons' => $nativeButtons,
        ];

        $sent = $this->send($target, $formattedMessage, $options);

        return [
            'status' => $sent,
            'target' => $target,
            'message' => $formattedMessage,
        ];
    }
}
