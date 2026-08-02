<?php

use App\Contracts\Services\WhatsAppNotifier;
use App\Http\Controllers\Landing\OtpController;
use App\Models\Pemohon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Endpoint-level tests untuk wizard "Cek NIK" (POST /otp/request reused
 * dengan phone opsional) dan verifikasi tanpa phone (POST /otp/verify).
 * Lihat juga tests/Feature/OtpServiceTest.php (unit level verifyCode) dan
 * tests/Feature/PengajuanSuratPublicTest.php (full happy path end-to-end).
 */
class WizardSpyWhatsAppNotifier implements WhatsAppNotifier
{
    /** @var array<int, array{phone: string, message: string}> */
    public array $sent = [];

    public function send(string $phone, string $message, array $options = []): bool
    {
        $this->sent[] = ['phone' => $phone, 'message' => $message];

        return true;
    }

    public function lastCode(): string
    {
        preg_match('/\d{6}/', end($this->sent)['message'], $matches);

        return $matches[0];
    }
}

beforeEach(function () {
    Cache::flush();
    RateLimiter::clear('nik-check:ip:127.0.0.1');

    $this->waSpy = new WizardSpyWhatsAppNotifier;
    app()->instance(WhatsAppNotifier::class, $this->waSpy);
});

test('cek nik with no matching pemohon and no phone reports not found and sends no otp', function () {
    $nik = '7372019999990001';
    RateLimiter::clear("otp-req:nik:{$nik}");
    RateLimiter::clear('otp-req:ip:127.0.0.1');

    $response = $this->postJson(route('otp.request'), ['nik' => $nik]);

    $response->assertOk();
    $response->assertJsonPath('data.found', false);

    expect($this->waSpy->sent)->toBeEmpty();
    // Limiter pengiriman OTP (OtpService) sama sekali tidak tersentuh —
    // karena tidak ada OTP yang dikirim untuk probe murni ini.
    expect(RateLimiter::attempts("otp-req:nik:{$nik}"))->toBe(0);
    expect(RateLimiter::attempts('otp-req:ip:127.0.0.1'))->toBe(0);

    // Tidak ada cache kode OTP yang tertulis untuk nik ini: verifikasi apa
    // pun kodenya harus gagal sebagai "expired", bukan "invalid".
    $verify = $this->postJson(route('otp.verify'), ['nik' => $nik, 'code' => '123456']);
    $verify->assertStatus(422);
    $verify->assertSee('kedaluwarsa');
});

test('cek nik with a matching pemohon reports found and sends otp to the phone on file', function () {
    $pemohon = Pemohon::create([
        'nik' => '7372019999990002',
        'name' => 'Warga Terdaftar',
        'phone' => '081211112222',
    ]);

    $response = $this->postJson(route('otp.request'), ['nik' => $pemohon->nik]);

    $response->assertOk();
    $response->assertJsonPath('data.found', true);
    $response->assertJsonStructure(['data' => ['found', 'phone_masked']]);

    expect($this->waSpy->sent)->toHaveCount(1);
    expect($this->waSpy->sent[0]['phone'])->toBe('081211112222');
});

test('cek nik reports a clear error when a matching pemohon has no phone on file', function () {
    $pemohon = Pemohon::create([
        'nik' => '7372019999990003',
        'name' => 'Warga Tanpa Kontak',
        'phone' => null,
    ]);

    $response = $this->postJson(route('otp.request'), ['nik' => $pemohon->nik]);

    $response->assertStatus(422);
    expect($this->waSpy->sent)->toBeEmpty();
});

test('nik check ip throttle rejects the request past the limit', function () {
    for ($i = 1; $i <= OtpController::NIK_CHECK_MAX_PER_IP; $i++) {
        $nik = str_pad((string) (7372010000000000 + $i), 16, '0', STR_PAD_LEFT);
        $this->postJson(route('otp.request'), ['nik' => $nik])->assertOk();
    }

    $response = $this->postJson(route('otp.request'), ['nik' => '7372019999990099']);

    $response->assertStatus(429);
    $response->assertJsonPath('success', false);
});

test('verify without phone succeeds for a code requested via the nik-only path and returns pemohon data', function () {
    $pemohon = Pemohon::create([
        'nik' => '7372019999990004',
        'name' => 'Warga Prefill',
        'phone' => '081233334444',
        'alamat' => 'Jl. Contoh No. 1',
    ]);

    $this->postJson(route('otp.request'), ['nik' => $pemohon->nik])->assertOk();
    $code = $this->waSpy->lastCode();

    $response = $this->postJson(route('otp.verify'), ['nik' => $pemohon->nik, 'code' => $code]);

    $response->assertOk();
    $response->assertJsonPath('data.pemohon.name', 'Warga Prefill');
    $response->assertJsonPath('data.pemohon.phone', '081233334444');
    $response->assertJsonPath('data.pemohon.alamat', 'Jl. Contoh No. 1');

    // Flag session menyimpan nomor server-verified (bukan self-asserted client).
    $response->assertSessionHas('otp_verified.phone', '081233334444');
    $response->assertSessionHas('otp_verified.nik', $pemohon->nik);
});

test('verify without phone and without a matching pemohon omits the pemohon block', function () {
    $nik = '7372019999990005';
    $phone = '081255556666';

    $this->postJson(route('otp.request'), ['nik' => $nik, 'phone' => $phone])->assertOk();
    $code = $this->waSpy->lastCode();

    $response = $this->postJson(route('otp.verify'), ['nik' => $nik, 'phone' => $phone, 'code' => $code]);

    $response->assertOk();
    $response->assertJsonMissingPath('data.pemohon');
});

test('verify without phone is rejected for the not-found path even with a valid code', function () {
    // Highest-risk regression: the not-found path relies on the client
    // reproducing the phone number IT typed at request time. Omitting
    // `phone` on verify must never be a free pass for this path, only for
    // the wizard "found" path where the client was never told the number.
    $nik = '7372019999990007';
    $phone = '081266667777';

    $this->postJson(route('otp.request'), ['nik' => $nik, 'phone' => $phone])->assertOk();
    $code = $this->waSpy->lastCode();

    $omitted = $this->postJson(route('otp.verify'), ['nik' => $nik, 'code' => $code]);
    $omitted->assertStatus(422);
    $omitted->assertSee('Nomor WhatsApp wajib');

    // The same code, with the correct phone reproduced, still verifies.
    $withPhone = $this->postJson(route('otp.verify'), ['nik' => $nik, 'phone' => $phone, 'code' => $code]);
    $withPhone->assertOk();
});

test('cek nik found ignores a client-supplied phone and always targets the phone on file', function () {
    // Adversarial: NIK is not secret (S3_MVP_DESIGN.md §3). An attacker who
    // knows a victim's NIK must not be able to smuggle their own `phone`
    // into a "found" request and redirect the OTP to themselves — that
    // would let them complete verification and harvest the victim's
    // name/phone/alamat/kelurahan from the verify response.
    $pemohon = Pemohon::create([
        'nik' => '7372019999990006',
        'name' => 'Warga Asli',
        'phone' => '081200000000',
    ]);

    $response = $this->postJson(route('otp.request'), [
        'nik' => $pemohon->nik,
        'phone' => '089999999999', // attacker-controlled, must be ignored
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.found', true);
    expect($this->waSpy->sent)->toHaveCount(1);
    expect($this->waSpy->sent[0]['phone'])->toBe('081200000000');

    $code = $this->waSpy->lastCode();

    // The attacker doesn't know the real phone, so a verify attempt that
    // asserts their own phone must fail even with the correct code.
    $withAttackerPhone = $this->postJson(route('otp.verify'), [
        'nik' => $pemohon->nik,
        'phone' => '089999999999',
        'code' => $code,
    ]);
    $withAttackerPhone->assertStatus(422);

    // Only the phone-less verify (the real wizard flow) succeeds and, per
    // design, reveals PII solely because the OTP was proven against the
    // number on file, not the attacker's.
    $verify = $this->postJson(route('otp.verify'), ['nik' => $pemohon->nik, 'code' => $code]);
    $verify->assertOk();
    $verify->assertJsonPath('data.pemohon.phone', '081200000000');
});
