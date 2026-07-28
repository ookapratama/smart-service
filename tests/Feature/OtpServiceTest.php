<?php

use App\Contracts\Services\WhatsAppNotifier;
use App\Models\Pemohon;
use App\Services\OtpService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

const OTP_PURPOSE = 'persuratan';
const OTP_NIK = '7372011234567890';
const OTP_PHONE = '081234567890';

class SpyWhatsAppNotifier implements WhatsAppNotifier
{
    /** @var array<int, array{phone: string, message: string}> */
    public array $sent = [];

    public function send(string $phone, string $message): bool
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
    RateLimiter::clear('otp-req:nik:'.OTP_NIK);
    RateLimiter::clear('otp-req:ip:127.0.0.1');

    $this->waSpy = new SpyWhatsAppNotifier;
    app()->instance(WhatsAppNotifier::class, $this->waSpy);
    $this->otp = app(OtpService::class);
});

test('generate and verify happy path invalidates the code after success', function () {
    $result = $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');

    expect($result['ok'])->toBeTrue();
    expect($result['masked_phone'])->toBe('0812******90');
    expect($this->waSpy->sent)->toHaveCount(1);
    expect($this->waSpy->sent[0]['phone'])->toBe(OTP_PHONE);

    $code = $this->waSpy->lastCode();
    $verify = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, $code);

    expect($verify['ok'])->toBeTrue();
    expect($verify['phone'])->toBe(OTP_PHONE);

    // Kode sekali pakai: verifikasi ulang dengan kode sama harus gagal.
    $again = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, $code);
    expect($again['ok'])->toBeFalse();
    expect($again['reason'])->toBe('expired');
});

test('wrong code five times locks the otp and burns the real code', function () {
    $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');
    $code = $this->waSpy->lastCode();
    $wrongCode = $code === '000000' ? '111111' : '000000';

    for ($i = 1; $i <= 4; $i++) {
        $result = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, $wrongCode);
        expect($result['ok'])->toBeFalse();
        expect($result['reason'])->toBe('invalid');
        expect($result['attempts_left'])->toBe(5 - $i);
    }

    $fifth = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, $wrongCode);
    expect($fifth['reason'])->toBe('locked');

    // Kode asli ikut hangus setelah terkunci.
    $afterLock = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, $code);
    expect($afterLock['ok'])->toBeFalse();
    expect($afterLock['reason'])->toBe('expired');
});

test('expired code fails verification', function () {
    $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');
    $code = $this->waSpy->lastCode();

    $this->travel(6)->minutes();

    $result = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, $code);
    expect($result['ok'])->toBeFalse();
    expect($result['reason'])->toBe('expired');
});

test('resend cooldown of 60 seconds is enforced per phone', function () {
    $first = $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');
    expect($first['ok'])->toBeTrue();

    $second = $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');
    expect($second['ok'])->toBeFalse();
    expect($second['reason'])->toBe('cooldown');

    $this->travel(61)->seconds();

    $third = $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');
    expect($third['ok'])->toBeTrue();
    expect($this->waSpy->sent)->toHaveCount(2);
});

test('otp goes to the stored verified phone when the submitted phone differs', function () {
    Pemohon::create([
        'nik' => OTP_NIK,
        'name' => 'Warga Lama',
        'phone' => '089999999999',
        'phone_verified_at' => now(),
    ]);

    $result = $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');

    expect($result['ok'])->toBeTrue();
    expect($this->waSpy->sent[0]['phone'])->toBe('089999999999');
    expect($result['masked_phone'])->toBe('0899******99');
});

test('verify endpoint stamps the session flag with purpose persuratan', function () {
    $this->postJson(route('otp.request'), ['nik' => OTP_NIK, 'phone' => OTP_PHONE])->assertOk();

    $response = $this->postJson(route('otp.verify'), [
        'nik' => OTP_NIK,
        'phone' => OTP_PHONE,
        'code' => $this->waSpy->lastCode(),
    ]);

    $response->assertOk();
    $response->assertSessionHas('otp_verified.purpose', 'persuratan');
    $response->assertSessionHas('otp_verified.nik', OTP_NIK);
});

test('otp goes to the submitted phone when the stored phone was never verified', function () {
    Pemohon::create([
        'nik' => OTP_NIK,
        'name' => 'Warga Guest',
        'phone' => '089999999999',
        'phone_verified_at' => null,
    ]);

    $result = $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');

    expect($result['ok'])->toBeTrue();
    expect($this->waSpy->sent[0]['phone'])->toBe(OTP_PHONE);
});

test('verifyCode with null phone succeeds only when the request was flagged as not requiring it', function () {
    // Jalur "NIK ditemukan" (wizard Cek NIK): client tidak pernah diberi
    // tahu nomor pemohon, jadi hanya {nik, code} yang dikirim balik. Caller
    // (OtpController) menandai ini eksplisit lewat requirePhoneAtVerify:false.
    $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1', requirePhoneAtVerify: false);
    $code = $this->waSpy->lastCode();

    $result = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, null, $code);

    expect($result['ok'])->toBeTrue();
    expect($result['phone'])->toBe(OTP_PHONE);
});

test('verifyCode with null phone is rejected when the request required a client-typed phone', function () {
    // Regresi: jalur "NIK belum terdaftar" (form manual, requirePhoneAtVerify
    // default true) — sebelum perbaikan, phone:null di verifyCode() SELALU
    // melewati pengecekan pasangan nomor apa pun asal requestnya, sehingga
    // menghapus faktor "harus tahu nomor yang diketik sendiri" untuk jalur
    // ini juga. Mengosongkan phone di sini TIDAK BOLEH jadi jalan pintas.
    $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');
    $code = $this->waSpy->lastCode();

    $result = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, null, $code);

    expect($result['ok'])->toBeFalse();
    expect($result['reason'])->toBe('phone_required');
});

test('verifyCode with a wrong phone is still rejected when phone is provided', function () {
    // Regresi: jalur "NIK belum terdaftar" (form manual) masih mengirim
    // phone dan pengecekan pasangan nomor harus tetap berlaku.
    $this->otp->requestCode(OTP_PURPOSE, OTP_NIK, OTP_PHONE, '127.0.0.1');
    $code = $this->waSpy->lastCode();

    $result = $this->otp->verifyCode(OTP_PURPOSE, OTP_NIK, '089900000000', $code);

    expect($result['ok'])->toBeFalse();
    expect($result['reason'])->toBe('invalid');
});
