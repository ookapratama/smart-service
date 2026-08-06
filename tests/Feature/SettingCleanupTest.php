<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
});

test('should not contain removed setting keys after fresh migrate and seed', function () {
    (new SettingSeeder)->run();

    $deadKeys = ['profile_map_embed', 'profile_kode_wilayah', 'contact_email', 'app_keywords', 'theme_color'];

    expect(DB::table('settings')->whereIn('key', $deadKeys)->count())->toBe(0);
});

test('should group contact and social keys under profil after seeding', function () {
    (new SettingSeeder)->run();

    $groups = DB::table('settings')
        ->whereIn('key', ['contact_phone', 'social_instagram', 'social_facebook', 'social_youtube'])
        ->pluck('group')
        ->unique();

    expect($groups->all())->toBe(['profil']);
});

test('should preserve admin edited value when seeder runs again', function () {
    // db:seed berjalan pada setiap deploy — nilai hasil edit admin tidak boleh
    // direset ke default seeder (regression untuk bug clobber updateOrInsert).
    (new SettingSeeder)->run();

    DB::table('settings')->where('key', 'hero_title')->update(['value' => 'Judul Editan Admin']);

    (new SettingSeeder)->run();

    expect(DB::table('settings')->where('key', 'hero_title')->value('value'))
        ->toBe('Judul Editan Admin');
});

test('should not persist unknown keys when settings updated with extra input', function () {
    $this->actingAs($this->admin)
        ->post(route('settings.update'), [
            'app_name' => 'Nama Baru',
            'evil_key' => 'nilai liar',
        ])
        ->assertRedirect();

    expect(DB::table('settings')->where('key', 'evil_key')->exists())->toBeFalse();
    expect(DB::table('settings')->where('key', 'app_name')->value('value'))->toBe('Nama Baru');
});

test('should reject invalid wa_driver value', function () {
    $this->actingAs($this->admin)
        ->from(route('settings.index'))
        ->post(route('settings.update'), ['wa_driver' => 'driver-liar'])
        ->assertRedirect(route('settings.index'))
        ->assertSessionHasErrors('wa_driver');
});

test('should accept whatsapp_web_js as wa_driver value', function () {
    $this->actingAs($this->admin)
        ->post(route('settings.update'), ['wa_driver' => 'whatsapp_web_js'])
        ->assertRedirect();

    expect(DB::table('settings')->where('key', 'wa_driver')->value('value'))->toBe('whatsapp_web_js');
});

test('should reject get request to clear-cache route', function () {
    $this->actingAs($this->admin)
        ->get('admin/settings/clear-cache')
        ->assertStatus(405);
});

test('should clear cache via post request', function () {
    $this->actingAs($this->admin)
        ->post(route('settings.clear-cache'))
        ->assertRedirect()
        ->assertSessionHas('success');
});

test('should render landing home page after hardcoded siteInfo removal', function () {
    (new SettingSeeder)->run();

    $this->get(route('home'))->assertOk();
});

test('should render settings page without removed fields', function () {
    (new SettingSeeder)->run();

    $this->actingAs($this->admin)
        ->get(route('settings.index'))
        ->assertOk()
        ->assertDontSee('name="theme_color"', false)
        ->assertDontSee('name="contact_email"', false);
});
