<?php

use App\Contracts\Services\WhatsAppNotifier;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\Pemohon;
use App\Models\Tiket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

const SURAT_NIK = '7372015555667788';
const SURAT_PHONE = '081298765432';

function suratOtpSession(array $overrides = []): array
{
    return [
        'otp_verified' => array_merge([
            'purpose' => 'persuratan',
            'nik' => SURAT_NIK,
            'phone' => SURAT_PHONE,
            'expires_at' => now()->addMinutes(15)->getTimestamp(),
        ], $overrides),
    ];
}

function suratPayload(JenisSurat $jenisSurat, Kelurahan $kelurahan, array $overrides = []): array
{
    return array_merge([
        'jenis_surat_id' => $jenisSurat->id,
        'nik' => SURAT_NIK,
        'nama' => 'Warga Surat',
        'phone' => SURAT_PHONE,
        'kelurahan_id' => $kelurahan->id,
        'alamat' => 'Jl. Uji Persuratan No. 1',
        'keperluan' => 'Persyaratan melamar pekerjaan',
        'data' => [
            'pekerjaan' => 'Wiraswasta',
            'jenis_permohonan' => 'Baru',
            'pengantar_rt_rw' => UploadedFile::fake()->create('pengantar.pdf', 100, 'application/pdf'),
        ],
    ], $overrides);
}

beforeEach(function () {
    Storage::fake('public');

    $this->kelurahan = Kelurahan::create(['nama' => 'Ujung Baru', 'kode' => 'UJB', 'is_active' => true]);

    $this->jenisSurat = JenisSurat::create([
        'kode' => 'SKTM-T',
        'nama' => 'Surat Keterangan Uji',
        'deskripsi' => 'Jenis surat untuk pengujian.',
        'fields' => [
            ['name' => 'pekerjaan', 'type' => 'text', 'label' => 'Pekerjaan', 'required' => true],
            ['name' => 'jenis_permohonan', 'type' => 'select', 'label' => 'Jenis Permohonan', 'options' => ['Baru', 'Perpanjangan'], 'required' => true],
            ['name' => 'pengantar_rt_rw', 'type' => 'file', 'label' => 'Pengantar RT/RW', 'required' => true],
        ],
        'is_active' => true,
    ]);
});

test('surat landing lists only active jenis surat', function () {
    JenisSurat::create([
        'kode' => 'OFF',
        'nama' => 'Surat Nonaktif',
        'fields' => [],
        'is_active' => false,
    ]);

    $response = $this->get(route('surat.index'));

    $response->assertOk();
    $response->assertSee('Surat Keterangan Uji');
    $response->assertDontSee('Surat Nonaktif');
});

test('full submit with verified otp session creates pemohon, pengajuan, and tiket', function () {
    $response = $this->withSession(suratOtpSession())
        ->post(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan));

    $periode = now()->format('ym');
    $nomor = "SRG-{$periode}-00001";
    $response->assertRedirect(route('surat.sukses', $nomor));

    $tiket = Tiket::where('nomor_tiket', $nomor)->firstOrFail();
    expect($tiket->detail_type)->toBe('pengajuan_surat');
    expect($tiket->statusLogs()->count())->toBe(1);

    $pemohon = Pemohon::where('nik', SURAT_NIK)->firstOrFail();
    expect($tiket->pemohon_id)->toBe($pemohon->id);
    expect($pemohon->phone)->toBe(SURAT_PHONE);
    expect($pemohon->phone_verified_at)->not->toBeNull();

    $pengajuan = $tiket->detail;
    expect($pengajuan->jenis_surat_id)->toBe($this->jenisSurat->id);
    expect($pengajuan->data)->toBe(['pekerjaan' => 'Wiraswasta', 'jenis_permohonan' => 'Baru']);
    expect($pengajuan->media()->count())->toBe(1);
    expect($pengajuan->media()->first()->collection)->toBe('pengantar_rt_rw');

    // Flag OTP dikonsumsi sekali pakai oleh submit.
    $response->assertSessionMissing('otp_verified');
});

test('submit without otp session flag is rejected and writes nothing', function () {
    $payload = suratPayload($this->jenisSurat, $this->kelurahan, [
        'data' => ['pekerjaan' => 'Wiraswasta', 'jenis_permohonan' => 'Baru'],
    ]);

    $response = $this->postJson(route('surat.store'), $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('otp');
    expect(Tiket::count())->toBe(0);
    expect(Pemohon::count())->toBe(0);
});

test('submit with expired otp session flag is rejected', function () {
    $session = suratOtpSession(['expires_at' => now()->subMinute()->getTimestamp()]);

    $response = $this->withSession($session)
        ->postJson(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('otp');
});

test('submit with otp flag missing or mismatching purpose is rejected', function () {
    // Flag tanpa purpose (mis. sisa flow lama / flow lain) tidak boleh lolos.
    $tanpaPurpose = suratOtpSession();
    unset($tanpaPurpose['otp_verified']['purpose']);

    $this->withSession($tanpaPurpose)
        ->postJson(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan))
        ->assertStatus(422)
        ->assertJsonValidationErrors('otp');

    // Flag ber-purpose lain (mis. login warga di fase depan) juga ditolak.
    $this->withSession(suratOtpSession(['purpose' => 'login']))
        ->postJson(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan))
        ->assertStatus(422)
        ->assertJsonValidationErrors('otp');

    expect(Tiket::count())->toBe(0);
});

test('submit with mismatching nik or phone against otp flag is rejected', function () {
    $response = $this->withSession(suratOtpSession(['phone' => '080000000000']))
        ->postJson(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('otp');
});

test('invalid nik is rejected', function () {
    $payload = suratPayload($this->jenisSurat, $this->kelurahan, ['nik' => 'abc123']);

    // Flag session dibuat cocok dengan nik form agar lolos gerbang OTP dan
    // benar-benar menguji rule validasi digits:16, bukan mismatch flag.
    $this->withSession(suratOtpSession(['nik' => 'abc123']))
        ->post(route('surat.store'), $payload)
        ->assertSessionHasErrors('nik');
});

test('required file field from fields json must be present', function () {
    $payload = suratPayload($this->jenisSurat, $this->kelurahan, [
        'data' => ['pekerjaan' => 'Wiraswasta', 'jenis_permohonan' => 'Baru'],
    ]);

    $this->withSession(suratOtpSession())
        ->post(route('surat.store'), $payload)
        ->assertSessionHasErrors('data.pengantar_rt_rw');
});

test('submit without the optional keperluan succeeds with a fallback description', function () {
    // Regresi: kolom pengajuan_surat.keperluan NOT NULL — tanpa fallback,
    // submit yang mengosongkan field opsional ini gagal dengan QueryException.
    $payload = suratPayload($this->jenisSurat, $this->kelurahan);
    unset($payload['keperluan']);

    $this->withSession(suratOtpSession())
        ->post(route('surat.store'), $payload)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $tiket = Tiket::firstOrFail();
    expect($tiket->detail->keperluan)->toBe('Pengajuan Surat Keterangan Uji via portal 3S.');
    expect($tiket->keterangan)->toBe('Pengajuan Surat Keterangan Uji via portal 3S.');
});

test('wizard happy path: cek nik found, verify without phone, then submit with the prefilled identity', function () {
    $pemohon = Pemohon::create([
        'nik' => SURAT_NIK,
        'name' => 'Warga Lama',
        'phone' => SURAT_PHONE,
        'alamat' => 'Alamat Lama',
        'kelurahan_id' => $this->kelurahan->id,
        'phone_verified_at' => now()->subDays(30),
    ]);

    $spy = new class implements WhatsAppNotifier
    {
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
    };
    app()->instance(WhatsAppNotifier::class, $spy);

    // Step 1: Cek NIK — hanya nik, tanpa phone. Ditemukan → OTP otomatis ke
    // nomor tersimpan, bukan ke nomor client (client tidak pernah tahu nomornya).
    $cekNik = $this->postJson(route('otp.request'), ['nik' => SURAT_NIK]);
    $cekNik->assertOk();
    $cekNik->assertJsonPath('data.found', true);
    expect($spy->sent)->toHaveCount(1);
    expect($spy->sent[0]['phone'])->toBe(SURAT_PHONE);

    $code = $spy->lastCode();

    // Step 2: verifikasi hanya {nik, code} — tanpa phone.
    $verify = $this->postJson(route('otp.verify'), ['nik' => SURAT_NIK, 'code' => $code]);
    $verify->assertOk();
    $verify->assertJsonPath('data.pemohon.name', 'Warga Lama');
    $verify->assertJsonPath('data.pemohon.phone', SURAT_PHONE);
    $verify->assertSessionHas('otp_verified.phone', SURAT_PHONE);
    $verify->assertSessionHas('otp_verified.nik', SURAT_NIK);

    // Step 3: frontend mem-prefill nik+phone dari respons verify (server
    // truth) dan mengunci keduanya sebelum submit. Session (array driver)
    // tetap terjaga otomatis di antara request berurutan dalam satu test —
    // pola yang sama dipakai OtpServiceTest — jadi tidak perlu withSession().
    $response = $this->post(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan, [
        'nama' => 'Warga Lama',
        'alamat' => 'Alamat Lama',
    ]));

    $periode = now()->format('ym');
    $nomor = "SRG-{$periode}-00001";
    $response->assertRedirect(route('surat.sukses', $nomor));

    $tiket = Tiket::where('nomor_tiket', $nomor)->firstOrFail();
    expect($tiket->pemohon_id)->toBe($pemohon->id);

    // Pemohon tidak terduplikasi — NIK yang sama, satu baris.
    expect(Pemohon::count())->toBe(1);
    expect(Pemohon::first()->id)->toBe($pemohon->id);
    expect(Pemohon::first()->phone_verified_at)->not->toBeNull();
});

test('wizard happy path: cek nik not found falls back to the manual identity + bottom otp flow', function () {
    $spy = new class implements WhatsAppNotifier
    {
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
    };
    app()->instance(WhatsAppNotifier::class, $spy);

    $cekNik = $this->postJson(route('otp.request'), ['nik' => SURAT_NIK]);
    $cekNik->assertOk();
    $cekNik->assertJsonPath('data.found', false);
    expect($spy->sent)->toBeEmpty();

    // Alur lama tetap: form manual mengetik phone sendiri lalu minta+verifikasi OTP.
    $this->postJson(route('otp.request'), ['nik' => SURAT_NIK, 'phone' => SURAT_PHONE])
        ->assertOk()
        ->assertJsonPath('data.found', false);

    $code = $spy->lastCode();

    $verify = $this->postJson(route('otp.verify'), ['nik' => SURAT_NIK, 'phone' => SURAT_PHONE, 'code' => $code]);
    $verify->assertOk();
    $verify->assertJsonMissingPath('data.pemohon');
    $verify->assertSessionHas('otp_verified.phone', SURAT_PHONE);

    // Session (array driver) persisten otomatis antar request berurutan.
    $response = $this->post(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan));

    $periode = now()->format('ym');
    $nomor = "SRG-{$periode}-00001";
    $response->assertRedirect(route('surat.sukses', $nomor));

    $pemohon = Pemohon::where('nik', SURAT_NIK)->firstOrFail();
    expect($pemohon->phone)->toBe(SURAT_PHONE);
    expect(Tiket::count())->toBe(1);
});

test('second submit with the same nik reuses the same pemohon', function () {
    $this->withSession(suratOtpSession())
        ->post(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan));

    $this->withSession(suratOtpSession())
        ->post(route('surat.store'), suratPayload($this->jenisSurat, $this->kelurahan, [
            'nama' => 'Warga Surat Diperbarui',
        ]));

    expect(Pemohon::count())->toBe(1);
    expect(Pemohon::first()->name)->toBe('Warga Surat Diperbarui');
    expect(Tiket::count())->toBe(2);

    $periode = now()->format('ym');
    expect(Tiket::orderByDesc('id')->first()->nomor_tiket)->toBe("SRG-{$periode}-00002");
});
