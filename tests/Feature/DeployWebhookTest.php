<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

// Marker file yang membuat webhook memakai `migrate` biasa alih-alih `migrate:fresh`
// (lihat DeployWebhookController::SCHEMA_REBUILD_MARKER).
const SCHEMA_REBUILD_MARKER = 'deploy/.schema-rebuilt-2026-07-28';

beforeEach(function () {
    config(['services.deploy.webhook_token' => 'test-token']);
    Storage::fake('local');
});

test('should clear caches first, stop at failing command, and name it in the 500', function () {
    // Regression untuk insiden prod: db:seed gagal TIDAK boleh meninggalkan
    // route cache basi — config/route/view:clear harus sudah jalan SEBELUM
    // command yang gagal, dan route:cache tidak boleh dieksekusi setelahnya.
    Storage::disk('local')->put(SCHEMA_REBUILD_MARKER, 'done'); // pakai migrate biasa

    Artisan::shouldReceive('call')->once()->ordered()->with('config:clear', []);
    Artisan::shouldReceive('call')->once()->ordered()->with('route:clear', []);
    Artisan::shouldReceive('call')->once()->ordered()->with('view:clear', []);
    Artisan::shouldReceive('call')->once()->ordered()->with('migrate', ['--force' => true]);
    Artisan::shouldReceive('call')->once()->ordered()->with('db:seed', ['--force' => true])
        ->andThrow(new RuntimeException('Seeder blew up'));
    Artisan::shouldNotReceive('call')->with('config:cache', []);
    Artisan::shouldNotReceive('call')->with('route:cache', []);
    Artisan::shouldReceive('output')->andReturn('');

    $this->postJson('/api/deploy/webhook', [], ['X-Deploy-Token' => 'test-token'])
        ->assertStatus(500)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Deploy command failed: db:seed')
        ->assertJsonPath('data.db:seed', 'FAILED: Seeder blew up');
});

test('should return 200 with per-command results when every command succeeds', function () {
    Storage::disk('local')->put(SCHEMA_REBUILD_MARKER, 'done');

    Artisan::shouldReceive('output')->andReturn('');
    Artisan::shouldNotReceive('call')->with('migrate:fresh', Mockery::any());
    Artisan::shouldReceive('call')->andReturn(0);

    $this->postJson('/api/deploy/webhook', [], ['X-Deploy-Token' => 'test-token'])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Deployed')
        ->assertJsonPath('data.migrate', 'OK')
        ->assertJsonPath('data.route:cache', 'OK')
        ->assertJsonPath('data.queue:restart', 'OK');
});

test('should run migrate:fresh and write the marker when marker file is absent', function () {
    Artisan::shouldReceive('call')->once()->with('migrate:fresh', ['--force' => true])->andReturn(0);
    Artisan::shouldNotReceive('call')->with('migrate', ['--force' => true]);
    Artisan::shouldReceive('call')->andReturn(0);
    Artisan::shouldReceive('output')->andReturn('');

    $this->postJson('/api/deploy/webhook', [], ['X-Deploy-Token' => 'test-token'])
        ->assertOk()
        ->assertJsonPath('data.migrate:fresh', 'OK');

    Storage::disk('local')->assertExists(SCHEMA_REBUILD_MARKER);
});

test('should reject wrong token with 403 without running any command', function () {
    Artisan::shouldReceive('call')->never();

    $this->postJson('/api/deploy/webhook', [], ['X-Deploy-Token' => 'wrong-token'])
        ->assertStatus(403)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden');
});

test('should reject missing token header with 403 without running any command', function () {
    Artisan::shouldReceive('call')->never();

    $this->postJson('/api/deploy/webhook')
        ->assertStatus(403)
        ->assertJsonPath('message', 'Forbidden');
});
