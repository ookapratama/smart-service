<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use App\Services\WilayahSyncService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
});

test('syncProvinces upserts provinces from the wilayah.id response shape', function () {
    Http::fake([
        'wilayah.id/api/provinces.json' => Http::response([
            'data' => [
                ['code' => '11', 'name' => 'Aceh'],
                ['code' => '32', 'name' => 'Jawa Barat'],
            ],
            'meta' => ['administrative_area_level' => 1, 'updated_at' => '2025-07-04'],
        ]),
    ]);

    $count = app(WilayahSyncService::class)->syncProvinces();

    expect($count)->toBe(2);
    $this->assertDatabaseHas('wilayah', ['code' => '32', 'name' => 'Jawa Barat', 'level' => 1, 'parent_code' => null]);
});

test('syncRegencies and syncDistricts build the correct parent chain', function () {
    Http::fake([
        'wilayah.id/api/regencies/32.json' => Http::response([
            'data' => [['code' => '32.04', 'name' => 'Kabupaten Bandung']],
        ]),
        'wilayah.id/api/districts/32.04.json' => Http::response([
            'data' => [['code' => '32.04.37', 'name' => 'Soreang']],
        ]),
    ]);

    Wilayah::create(['code' => '32', 'parent_code' => null, 'name' => 'Jawa Barat', 'level' => 1]);

    $service = app(WilayahSyncService::class);
    $regencyCount = $service->syncRegencies('32');
    $districtCount = $service->syncDistricts('32.04');

    expect($regencyCount)->toBe(1);
    expect($districtCount)->toBe(1);

    $soreang = Wilayah::find('32.04.37');
    expect($soreang->name)->toBe('Soreang');
    expect($soreang->parent->name)->toBe('Kabupaten Bandung');
    expect($soreang->parent->parent->name)->toBe('Jawa Barat');
});

test('sync is idempotent — re-syncing the same data does not duplicate rows', function () {
    Http::fake([
        'wilayah.id/api/provinces.json' => Http::response([
            'data' => [['code' => '11', 'name' => 'Aceh']],
        ]),
    ]);

    $service = app(WilayahSyncService::class);
    $service->syncProvinces();
    $service->syncProvinces();

    expect(Wilayah::where('code', '11')->count())->toBe(1);
});

test('a failed request throws so the sync command can catch and continue', function () {
    Http::fake([
        'wilayah.id/api/provinces.json' => Http::response(null, 500),
    ]);

    expect(fn () => app(WilayahSyncService::class)->syncProvinces())
        ->toThrow(RequestException::class);
});

test('admin can fetch cascading children for provinces, regencies, and districts', function () {
    Wilayah::create(['code' => '32', 'parent_code' => null, 'name' => 'Jawa Barat', 'level' => 1]);
    Wilayah::create(['code' => '32.04', 'parent_code' => '32', 'name' => 'Kabupaten Bandung', 'level' => 2]);
    Wilayah::create(['code' => '32.04.37', 'parent_code' => '32.04', 'name' => 'Soreang', 'level' => 3]);

    $this->actingAs($this->admin)
        ->getJson(route('wilayah.children', 'root'))
        ->assertOk()
        ->assertJson([['code' => '32', 'name' => 'Jawa Barat']]);

    $this->actingAs($this->admin)
        ->getJson(route('wilayah.children', '32'))
        ->assertOk()
        ->assertJson([['code' => '32.04', 'name' => 'Kabupaten Bandung']]);

    $this->actingAs($this->admin)
        ->getJson(route('wilayah.children', '32.04'))
        ->assertOk()
        ->assertJson([['code' => '32.04.37', 'name' => 'Soreang']]);
});
