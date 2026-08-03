<?php

use App\Models\JenisSurat;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\JenisSuratSeeder;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);
    $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
});

test('should persist normalized fields definition from builder form', function () {
    $this->actingAs($this->admin)->post(route('jenis-surat.store'), [
        'kode' => 'SKX',
        'nama' => 'Surat Uji Builder',
        'is_active' => '1',
        'fields' => [
            // options dikirim sebagai textarea (satu opsi per baris) seperti form asli
            ['name' => 'jenis_permohonan', 'label' => 'Jenis Permohonan', 'type' => 'select', 'required' => '1', 'options' => "Baru\nPerpanjangan\n"],
            ['name' => 'lampiran_ktp', 'label' => 'Scan KTP', 'type' => 'file', 'required' => '1'],
            ['name' => 'catatan', 'label' => 'Catatan', 'type' => 'textarea'],
        ],
    ])->assertRedirect(route('jenis-surat.index'));

    $jenis = JenisSurat::where('kode', 'SKX')->first();

    expect($jenis->fields)->toBe([
        ['name' => 'jenis_permohonan', 'label' => 'Jenis Permohonan', 'type' => 'select', 'required' => true, 'options' => ['Baru', 'Perpanjangan']],
        ['name' => 'lampiran_ktp', 'label' => 'Scan KTP', 'type' => 'file', 'required' => true],
        ['name' => 'catatan', 'label' => 'Catatan', 'type' => 'textarea', 'required' => false],
    ]);
});

test('should reject duplicate field names', function () {
    $this->actingAs($this->admin)->postJson(route('jenis-surat.store'), [
        'kode' => 'SKX',
        'nama' => 'Surat Uji',
        'fields' => [
            ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'text'],
            ['name' => 'alamat', 'label' => 'Alamat Lagi', 'type' => 'text'],
        ],
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['fields.0.name', 'fields.1.name']);
});

test('should reject invalid field name format', function () {
    $this->actingAs($this->admin)->postJson(route('jenis-surat.store'), [
        'kode' => 'SKX',
        'nama' => 'Surat Uji',
        'fields' => [
            ['name' => '1abc', 'label' => 'Salah', 'type' => 'text'],
        ],
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['fields.0.name']);
});

test('should reject select field without options', function () {
    $this->actingAs($this->admin)->postJson(route('jenis-surat.store'), [
        'kode' => 'SKX',
        'nama' => 'Surat Uji',
        'fields' => [
            ['name' => 'pilihan', 'label' => 'Pilihan', 'type' => 'select', 'options' => ''],
        ],
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['fields.0.options']);
});

test('should reject unknown field type', function () {
    $this->actingAs($this->admin)->postJson(route('jenis-surat.store'), [
        'kode' => 'SKX',
        'nama' => 'Surat Uji',
        'fields' => [
            ['name' => 'aneh', 'label' => 'Aneh', 'type' => 'checkbox'],
        ],
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['fields.0.type']);
});

test('should preserve admin edited fields when seeder runs again', function () {
    (new JenisSuratSeeder)->run();

    $skd = JenisSurat::where('kode', 'SKD')->first();
    $skd->update(['fields' => [['name' => 'field_editan', 'label' => 'Editan Admin', 'type' => 'text', 'required' => true]]]);

    (new JenisSuratSeeder)->run();

    expect($skd->fresh()->fields)->toBe([
        ['name' => 'field_editan', 'label' => 'Editan Admin', 'type' => 'text', 'required' => true],
    ]);
});

test('should seed pengantar file field for jenis with wajib pengantar flag', function () {
    (new JenisSuratSeeder)->run();

    $fieldNames = collect(JenisSurat::where('kode', 'SKD')->first()->fields)->pluck('name');

    expect($fieldNames)->toContain('pengantar_rt_rw');
});

test('should add pengantar field only once when data migration runs twice', function () {
    $jenis = JenisSurat::create([
        'kode' => 'SKL',
        'nama' => 'Surat Lama Prod',
        'fields' => [['name' => 'alamat', 'label' => 'Alamat', 'type' => 'text', 'required' => true]],
        'wajib_pengantar_rt_rw' => true,
        'is_active' => true,
    ]);

    $migration = include database_path('migrations/2026_08_03_000300_add_pengantar_field_to_jenis_surat.php');
    $migration->up();
    $migration->up();

    $pengantarCount = collect($jenis->fresh()->fields)
        ->filter(fn ($field) => $field['name'] === 'pengantar_rt_rw')
        ->count();

    expect($pengantarCount)->toBe(1);
});

test('should render field builder on create and edit pages', function () {
    (new JenisSuratSeeder)->run();
    $skd = JenisSurat::where('kode', 'SKD')->first();

    $this->actingAs($this->admin)
        ->get(route('jenis-surat.create'))
        ->assertOk()
        ->assertSee('Tambah Field')
        ->assertDontSee('name="wajib_pengantar_rt_rw"', false);

    $this->actingAs($this->admin)
        ->get(route('jenis-surat.edit', $skd->id))
        ->assertOk()
        ->assertSee('fields[0][name]', false)
        ->assertSee('alamat_domisili');
});
