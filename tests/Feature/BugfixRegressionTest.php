<?php

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // super-admin bypasses CheckPermission via User::hasPermission early return
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
});

/**
 * Helper: create a user whose role has ONLY can_read on the given menu slugs.
 */
function createReadOnlyUser(array $menuSlugs): User
{
    $role = Role::firstOrCreate(['slug' => 'read-only-tester'], ['name' => 'Read Only Tester']);

    foreach ($menuSlugs as $index => $slug) {
        $menu = Menu::firstOrCreate(['slug' => $slug], [
            'name' => 'Menu '.$slug,
            'path' => str_replace('.', '/', $slug),
            'order_no' => $index + 1,
        ]);

        $role->menus()->syncWithoutDetaching([
            $menu->id => [
                'can_create' => 0,
                'can_read' => 1,
                'can_update' => 0,
                'can_delete' => 0,
            ],
        ]);
    }

    return User::factory()->create(['role_id' => $role->id]);
}

/*
|--------------------------------------------------------------------------
| Bug A — BaseRequest::failedValidation always returned JSON 422
|--------------------------------------------------------------------------
*/

test('should redirect back with validation errors when role form submitted with invalid data', function () {
    // Regular web form post (no JSON headers): before the fix this returned
    // a 422 JSON response instead of redirecting back with session errors.
    $this->actingAs($this->admin)
        ->from(route('role.create'))
        ->post(route('role.store'), [
            'name' => '',
            'slug' => '',
        ])
        ->assertRedirect(route('role.create'))
        ->assertSessionHasErrors(['name', 'slug']);

    $this->assertDatabaseMissing('roles', ['slug' => '']);
});

test('should return json 422 when role store requested with json headers and invalid data', function () {
    // API behavior must be preserved: JSON clients still get a 422 payload.
    $this->actingAs($this->admin)
        ->postJson(route('role.store'), [
            'name' => '',
            'slug' => '',
        ])
        ->assertStatus(422)
        ->assertJson(['status' => false])
        ->assertJsonStructure(['status', 'message', 'errors' => ['name', 'slug']]);
});

/*
|--------------------------------------------------------------------------
| Bug B — Settings upload accepted any file (now validated via SettingRequest)
|--------------------------------------------------------------------------
*/

test('should reject non-image app logo upload when updating settings', function () {
    Storage::fake('public');

    // Before the fix any file type was accepted; a .php upload must now fail.
    $this->actingAs($this->admin)
        ->from(route('settings.index'))
        ->post(route('settings.update'), [
            'app_logo' => UploadedFile::fake()->create('evil.php', 10),
        ])
        ->assertRedirect(route('settings.index'))
        ->assertSessionHasErrors('app_logo');

    Storage::disk('public')->assertDirectoryEmpty('settings');
});

/*
|--------------------------------------------------------------------------
| Bug C — CheckPermission failed open for unmapped route suffixes
|--------------------------------------------------------------------------
*/

test('should deny products excel import when user has read-only permission', function () {
    // Route suffix "excel" was unmapped and previously defaulted to "read",
    // letting read-only users create data through the import endpoint.
    $user = createReadOnlyUser(['products.index']);

    $this->actingAs($user)
        ->post(route('products.import.excel'), [
            'file' => UploadedFile::fake()->create('products.xlsx', 10),
        ])
        ->assertStatus(403);
});

test('should deny settings cache clear when user has read-only permission', function () {
    // clear-cache mutates state and now requires "update" permission.
    $user = createReadOnlyUser(['settings.index']);

    $this->actingAs($user)
        ->get(route('settings.clear-cache'))
        ->assertStatus(403);
});

test('should allow products excel export when user has read permission', function () {
    // Export is a read action and must remain accessible to read-only users.
    $user = createReadOnlyUser(['products.index']);

    $response = $this->actingAs($user)->get(route('products.export.excel'));

    // The export returns a BinaryFileResponse; the middleware must not block it.
    expect($response->baseResponse->getStatusCode())->not->toBe(403);
});
