<?php

use App\Jobs\SyncWilayahJob;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
});

test('admin can trigger a wilayah sync and it dispatches the job to the queue', function () {
    Queue::fake();
    Cache::forget(SyncWilayahJob::CACHE_STATUS);

    $this->actingAs($this->admin)
        ->post(route('instansi.wilayah-sync'))
        ->assertRedirect()
        ->assertSessionHas('success');

    Queue::assertPushed(SyncWilayahJob::class);
});

test('sync button does not dispatch a second job while one is already running', function () {
    Queue::fake();
    Cache::forever(SyncWilayahJob::CACHE_STATUS, 'running');

    $this->actingAs($this->admin)
        ->post(route('instansi.wilayah-sync'))
        ->assertRedirect()
        ->assertSessionHas('error');

    Queue::assertNotPushed(SyncWilayahJob::class);
});

test('instansi index shows the last sync status and result', function () {
    Cache::forever(SyncWilayahJob::CACHE_STATUS, 'idle');
    Cache::forever(SyncWilayahJob::CACHE_LAST_SYNCED_AT, '2026-07-25 21:00:00');
    Cache::forever(SyncWilayahJob::CACHE_LAST_RESULT, [
        'provinces' => 38,
        'regencies' => 514,
        'districts' => 7285,
        'failed' => [],
    ]);

    $this->actingAs($this->admin)
        ->get(route('instansi.index'))
        ->assertOk()
        ->assertSee('Sinkronkan Data Wilayah')
        ->assertSee('25 Juli 2026');
});

test('instansi index shows a running indicator and disables the button mid-sync', function () {
    Cache::forever(SyncWilayahJob::CACHE_STATUS, 'running');

    $response = $this->actingAs($this->admin)->get(route('instansi.index'));

    $response->assertOk();
    $response->assertSee('Sedang Menyinkron');
    $response->assertSee('disabled', false);
});
