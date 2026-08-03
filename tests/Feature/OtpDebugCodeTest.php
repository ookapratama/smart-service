<?php

use App\Contracts\Services\WhatsAppNotifier;
use App\Services\Wa\LogWhatsAppNotifier;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class OtpDebugSpyNotifier implements WhatsAppNotifier
{
    public array $sent = [];

    public function send(string $phone, string $message, array $options = []): bool
    {
        $this->sent[] = ['phone' => $phone, 'message' => $message];

        return true;
    }
}

beforeEach(function () {
    Cache::flush();
    RateLimiter::clear('otp-req:nik:7371019999990001');
    RateLimiter::clear('otp-req:ip:127.0.0.1');
    RateLimiter::clear('nik-check:ip:127.0.0.1');
});

test('should include verifiable debug_code when app debug on and log driver bound', function () {
    config(['app.debug' => true]);
    app()->instance(WhatsAppNotifier::class, new LogWhatsAppNotifier);

    $response = $this->postJson(route('otp.request'), [
        'nik' => '7371019999990001',
        'phone' => '081200001111',
    ])->assertOk();

    $code = $response->json('data.debug_code');
    expect($code)->toMatch('/^\d{6}$/');

    // Kode dari debug_code harus benar-benar kode yang tersimpan.
    $this->postJson(route('otp.verify'), [
        'nik' => '7371019999990001',
        'phone' => '081200001111',
        'code' => $code,
    ])->assertOk();
});

test('should not include debug_code when app debug off even with log driver', function () {
    config(['app.debug' => false]);
    app()->instance(WhatsAppNotifier::class, new LogWhatsAppNotifier);

    $this->postJson(route('otp.request'), [
        'nik' => '7371019999990001',
        'phone' => '081200001111',
    ])->assertOk()
        ->assertJsonMissingPath('data.debug_code');
});

test('should not include debug_code when notifier is not the log driver', function () {
    config(['app.debug' => true]);
    app()->instance(WhatsAppNotifier::class, new OtpDebugSpyNotifier);

    $this->postJson(route('otp.request'), [
        'nik' => '7371019999990001',
        'phone' => '081200001111',
    ])->assertOk()
        ->assertJsonMissingPath('data.debug_code');
});
