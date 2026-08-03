<?php

use App\Contracts\Services\WhatsAppNotifier;
use App\Http\Controllers\Landing\OtpController;
use App\Models\JenisSurat;
use App\Models\KategoriPengaduan;
use App\Models\Pemohon;
use App\Models\Pengaduan;
use App\Models\PengajuanSurat;
use App\Models\Role;
use App\Models\Tiket;
use App\Models\User;
use App\Services\OtpService;
use App\Services\SuratService;
use App\Services\TiketService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class BugfixSpyNotifier implements WhatsAppNotifier
{
    public array $sent = [];

    public function send(string $phone, string $message, array $options = []): bool
    {
        $this->sent[] = ['phone' => $phone, 'message' => $message];

        return true;
    }

    public function lastMessage(): string
    {
        return end($this->sent)['message'] ?? '';
    }
}

function bugfixTiketSurat(string $status = 'diproses'): Tiket
{
    $pemohon = Pemohon::firstOrCreate(
        ['nik' => '7372015555666677'],
        ['name' => 'Warga Bugfix Surat', 'phone' => '081200005555']
    );

    $jenis = JenisSurat::firstOrCreate(
        ['kode' => 'SKD-BF'],
        ['nama' => 'Surat Bugfix', 'fields' => [], 'is_active' => true]
    );

    $pengajuan = PengajuanSurat::create([
        'jenis_surat_id' => $jenis->id,
        'keperluan' => 'Keperluan bugfix',
        'data' => [],
    ]);

    return $pengajuan->tiket()->create([
        'nomor_tiket' => 'BFS-2608-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Bugfix Surat',
        'status' => $status,
        'channel' => 'web',
    ]);
}

function bugfixTiketAduan(string $status = 'diproses'): Tiket
{
    $pemohon = Pemohon::firstOrCreate(
        ['nik' => '7372014444333322'],
        ['name' => 'Warga Bugfix Aduan', 'phone' => '081200006666']
    );

    $kategori = KategoriPengaduan::firstOrCreate(['nama' => 'Kategori Bugfix']);

    $pengaduan = Pengaduan::create([
        'kategori_pengaduan_id' => $kategori->id,
        'deskripsi' => 'Deskripsi aduan bugfix',
        'lokasi' => 'Jl. Bugfix No. 3',
    ]);

    return $pengaduan->tiket()->create([
        'nomor_tiket' => 'BFA-2608-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
        'pemohon_id' => $pemohon->id,
        'judul' => 'Tiket Bugfix Aduan',
        'status' => $status,
        'channel' => 'web',
    ]);
}

beforeEach(function () {
    Storage::fake(SuratService::PDF_DISK);
    Cache::flush();
    RateLimiter::clear('otp-req:nik:7371018888990011');
    RateLimiter::clear('otp-req:ip:127.0.0.1');

    $this->spy = new BugfixSpyNotifier;
    app()->instance(WhatsAppNotifier::class, $this->spy);
});

test('should store 400 character keperluan without truncation', function () {
    $keperluan = str_repeat('a', 400);
    $tiket = bugfixTiketSurat();

    $tiket->detail->update(['keperluan' => $keperluan]);

    expect(strlen($tiket->detail->fresh()->keperluan))->toBe(400);
});

test('should mention pengajuan surat and unduh link when surat ticket selesai', function () {
    $tiket = bugfixTiketSurat('diproses');

    app(TiketService::class)->updateStatus($tiket->id, 'selesai');

    $pesan = $this->spy->lastMessage();
    expect($pesan)->toContain('tiket pengajuan surat #'.$tiket->nomor_tiket)
        ->toContain(route('surat.unduh', $tiket->nomor_tiket))
        ->toContain('nik='.$tiket->pemohon->nik)
        ->not->toContain('tiket pengaduan #');
});

test('should mention pengaduan without unduh link when pengaduan ticket selesai', function () {
    $tiket = bugfixTiketAduan('diproses');

    app(TiketService::class)->updateStatus($tiket->id, 'selesai');

    $pesan = $this->spy->lastMessage();
    expect($pesan)->toContain('tiket pengaduan #'.$tiket->nomor_tiket)
        ->not->toContain('surat/unduh');
});

test('should return remaining seconds instead of full cooldown on repeated otp request', function () {
    $service = app(OtpService::class);

    $service->requestCode('persuratan', '7371018888990011', '081200007777', '127.0.0.1');

    $this->travel(20)->seconds();

    $result = $service->requestCode('persuratan', '7371018888990011', '081200007777', '127.0.0.1');

    expect($result['ok'])->toBeFalse()
        ->and($result['reason'])->toBe('cooldown')
        ->and($result['retry_after'])->toBeGreaterThanOrEqual(35)
        ->and($result['retry_after'])->toBeLessThanOrEqual(45);
});

test('should expose verified nik to wizard when otp session flag still valid', function () {
    $jenis = JenisSurat::firstOrCreate(
        ['kode' => 'SKD-BF'],
        ['nama' => 'Surat Bugfix', 'fields' => [], 'is_active' => true]
    );

    $this->withSession([
        OtpController::SESSION_KEY => [
            'purpose' => OtpController::PURPOSE_PERSURATAN,
            'nik' => '7371018888990011',
            'phone' => '081200007777',
            'expires_at' => now()->addSeconds(OtpService::SESSION_TTL)->getTimestamp(),
        ],
    ])->get(route('surat.create', $jenis))
        ->assertOk()
        ->assertSee('var verifiedNik = "7371018888990011"', false);
});

test('should not expose verified nik when otp session flag expired', function () {
    $jenis = JenisSurat::firstOrCreate(
        ['kode' => 'SKD-BF'],
        ['nama' => 'Surat Bugfix', 'fields' => [], 'is_active' => true]
    );

    $this->withSession([
        OtpController::SESSION_KEY => [
            'purpose' => OtpController::PURPOSE_PERSURATAN,
            'nik' => '7371018888990011',
            'phone' => '081200007777',
            'expires_at' => now()->subMinute()->getTimestamp(),
        ],
    ])->get(route('surat.create', $jenis))
        ->assertOk()
        ->assertSee('var verifiedNik = null', false);
});

test('should render admin tiket show for both detail types', function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)->get(route('tiket.show', bugfixTiketSurat()->id))->assertOk();
    $this->actingAs($admin)->get(route('tiket.show', bugfixTiketAduan()->id))->assertOk();
});
