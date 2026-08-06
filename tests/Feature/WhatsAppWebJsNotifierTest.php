<?php

use App\Contracts\Services\WhatsAppNotifier;
use App\Services\Wa\WhatsAppWebJsNotifier;
use Illuminate\Support\Facades\Http;

test('whatsapp web js notifier sends post request to node microservice', function () {
    Http::fake([
        'http://127.0.0.1:3000/send-message' => Http::response(['status' => true, 'message' => 'Sent'], 200),
    ]);

    $notifier = new WhatsAppWebJsNotifier();
    $result = $notifier->send('08123456789', 'Pesan Uji Coba');

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'http://127.0.0.1:3000/send-message' &&
               $request['phone'] === '628123456789' &&
               $request['message'] === 'Pesan Uji Coba';
    });
});

test('whatsapp web js notifier sendWithButtons formats structured message text', function () {
    Http::fake([
        '*' => Http::response(['status' => true], 200),
    ]);

    $notifier = new WhatsAppWebJsNotifier();
    $res = $notifier->sendWithButtons(
        '08123456789',
        'Judul Tiket',
        'Deskripsi tiket selesai.',
        ['Cek Status', 'https://example.com/status'],
        'Pemerintah Kecamatan Soreang'
    );

    expect($res['status'])->toBeTrue();
    expect($res['message'])->toContain('*Judul Tiket*');
    expect($res['message'])->toContain('🔗 *Cek Status*');
    expect($res['message'])->toContain('_Pemerintah Kecamatan Soreang_');
});

test('whatsapp_web_js driver resolves via app service provider', function () {
    $_ENV['WA_DRIVER'] = 'whatsapp_web_js';
    $_SERVER['WA_DRIVER'] = 'whatsapp_web_js';
    putenv('WA_DRIVER=whatsapp_web_js');
    config(['services.whatsapp_web_js.url' => 'http://127.0.0.1:3000/send-message']);

    $notifier = app(WhatsAppNotifier::class);
    expect($notifier)->toBeInstanceOf(WhatsAppWebJsNotifier::class);

    $_ENV['WA_DRIVER'] = 'log';
    $_SERVER['WA_DRIVER'] = 'log';
    putenv('WA_DRIVER=log');
});
