<?php

use App\Contracts\Services\WhatsAppNotifier;
use App\Models\Pemohon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class WargaLoginSpyNotifier implements WhatsAppNotifier
{
    public array $sent = [];

    public function send(string $phone, string $message, array $options = []): bool
    {
        $this->sent[] = ['phone' => $phone, 'message' => $message];

        return true;
    }

    public function lastCode(): string
    {
        preg_match('/\d{6}/', end($this->sent)['message'] ?? '', $m);

        return $m[0] ?? '';
    }
}

function wargaRole(): Role
{
    return Role::firstOrCreate(['slug' => 'warga'], ['name' => 'Warga']);
}

function buatPemohonLogin(string $nik, ?User $user = null): Pemohon
{
    return Pemohon::firstOrCreate(
        ['nik' => $nik],
        [
            'name' => 'Warga Login '.substr($nik, -4),
            'phone' => '0812'.substr($nik, -8),
            'user_id' => $user?->id,
        ]
    );
}

beforeEach(function () {
    Cache::flush();
    foreach (['7371012222330001', '7371012222330002', '7371012222330003', '7371019999999999'] as $nik) {
        RateLimiter::clear("otp-req:nik:{$nik}");
    }
    RateLimiter::clear('otp-req:ip:127.0.0.1');

    $this->spy = new WargaLoginSpyNotifier;
    app()->instance(WhatsAppNotifier::class, $this->spy);
});

test('should login existing linked warga user via nik and otp', function () {
    $user = User::factory()->create(['role_id' => wargaRole()->id, 'password' => null]);
    buatPemohonLogin('7371012222330001', $user);

    $this->postJson(route('warga.login.otp'), ['nik' => '7371012222330001'])->assertOk();

    expect($this->spy->sent)->toHaveCount(1);

    $response = $this->postJson(route('warga.login.verify'), [
        'nik' => '7371012222330001',
        'code' => $this->spy->lastCode(),
    ])->assertOk()
        ->assertJsonPath('data.redirect', route('tiket-saya.index'));

    $this->assertAuthenticatedAs($user);
    $response->assertCookie(Auth::guard()->getRecallerName());
});

test('should provision warga user on first successful otp login', function () {
    wargaRole();
    $pemohon = buatPemohonLogin('7371012222330002');

    expect($pemohon->user_id)->toBeNull();

    $this->postJson(route('warga.login.otp'), ['nik' => '7371012222330002'])->assertOk();
    $this->postJson(route('warga.login.verify'), [
        'nik' => '7371012222330002',
        'code' => $this->spy->lastCode(),
    ])->assertOk();

    $pemohon->refresh();
    expect($pemohon->user_id)->not->toBeNull()
        ->and($pemohon->phone_verified_at)->not->toBeNull();

    $user = $pemohon->user;
    expect($user->role->slug)->toBe('warga')
        ->and($user->password)->toBeNull()
        ->and($user->email)->toBeNull();

    $this->assertAuthenticatedAs($user);
});

test('should return identical response for registered and unknown nik', function () {
    wargaRole();
    buatPemohonLogin('7371012222330003');

    $known = $this->postJson(route('warga.login.otp'), ['nik' => '7371012222330003']);
    $unknown = $this->postJson(route('warga.login.otp'), ['nik' => '7371019999999999']);

    // Anti-enumeration (§4): status pendaftaran NIK tidak boleh terbaca dari
    // respons — byte-identical, dan NIK tak dikenal tidak memicu pengiriman.
    expect($unknown->getContent())->toBe($known->getContent())
        ->and($unknown->status())->toBe($known->status())
        ->and($this->spy->sent)->toHaveCount(1);
});

test('should reject wrong code and unknown nik with the same generic message', function () {
    wargaRole();
    buatPemohonLogin('7371012222330003');

    $this->postJson(route('warga.login.otp'), ['nik' => '7371012222330003'])->assertOk();

    $wrongCode = $this->postJson(route('warga.login.verify'), [
        'nik' => '7371012222330003',
        'code' => '000000',
    ])->assertStatus(422);

    $unknownNik = $this->postJson(route('warga.login.verify'), [
        'nik' => '7371019999999999',
        'code' => '123456',
    ])->assertStatus(422);

    expect($wrongCode->json('message'))->toBe($unknownNik->json('message'));
    $this->assertGuest();
});

test('should redirect warga away from admin area', function () {
    $user = User::factory()->create(['role_id' => wargaRole()->id, 'password' => null]);
    buatPemohonLogin('7371012222330001', $user);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('tiket-saya.index'));
});

test('should keep super admin access to admin dashboard', function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
});

test('should redirect logged in warga from masuk page to tiket saya', function () {
    $user = User::factory()->create(['role_id' => wargaRole()->id, 'password' => null]);
    buatPemohonLogin('7371012222330001', $user);

    $this->actingAs($user)
        ->get(route('warga.login'))
        ->assertRedirect(route('tiket-saya.index'));
});

test('should redirect warga to landing page after logout', function () {
    $user = User::factory()->create(['role_id' => wargaRole()->id, 'password' => null]);
    buatPemohonLogin('7371012222330001', $user);

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
});
