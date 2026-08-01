<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Galeri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GaleriAgendaPublicAndCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_galeri_public_catalog_and_detail_render_successfully(): void
    {
        $galeri = Galeri::create([
            'judul' => 'Galeri Foto Tes Pelayanan',
            'slug' => 'galeri-foto-tes-pelayanan',
            'kategori' => 'Pelayanan Publik',
            'keterangan' => 'Keterangan galeri tes',
            'tipe' => 'foto',
            'is_published' => true,
        ]);

        $response = $this->get(route('galeri.public.index'));
        $response->assertStatus(200);
        $response->assertSee('Galeri Foto Tes Pelayanan');

        $detailResponse = $this->get(route('galeri.public.show', $galeri->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Galeri Foto Tes Pelayanan');
    }

    public function test_agenda_public_catalog_and_detail_render_successfully(): void
    {
        $agenda = Agenda::create([
            'judul' => 'Rapat Musrenbang Tes',
            'slug' => 'rapat-musrenbang-tes',
            'kategori' => 'Rapat & Musyawarah',
            'penyelenggara' => 'Kecamatan Soreang',
            'lokasi' => 'Aula Soreang',
            'mulai_at' => now()->addDays(2),
            'ringkasan' => 'Ringkasan agenda tes',
            'deskripsi' => 'Deskripsi agenda tes',
            'is_published' => true,
        ]);

        $response = $this->get(route('agenda.public.index'));
        $response->assertStatus(200);
        $response->assertSee('Rapat Musrenbang Tes');

        $detailResponse = $this->get(route('agenda.public.show', $agenda->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Rapat Musrenbang Tes');
    }

    public function test_admin_can_crud_galeri(): void
    {
        $admin = User::whereHas('role', function ($q) {
            $q->where('slug', 'super-admin');
        })->first() ?? User::factory()->create();

        $this->actingAs($admin);

        // Index
        $this->get(route('galeri.index'))->assertStatus(200);

        // Create page
        $this->get(route('galeri.create'))->assertStatus(200);

        // Store
        $storeResponse = $this->post(route('galeri.store'), [
            'judul' => 'Galeri Baru via Admin',
            'kategori' => 'Kegiatan Kecamatan',
            'keterangan' => 'Deskripsi galeri baru',
            'tipe' => 'foto',
            'is_published' => 1,
        ]);

        $storeResponse->assertRedirect(route('galeri.index'));
        $this->assertDatabaseHas('galeri', [
            'judul' => 'Galeri Baru via Admin',
        ]);

        $galeri = Galeri::where('judul', 'Galeri Baru via Admin')->first();

        // Edit page & update
        $this->get(route('galeri.edit', $galeri->id))->assertStatus(200);

        $updateResponse = $this->put(route('galeri.update', $galeri->id), [
            'judul' => 'Galeri Terbarui via Admin',
            'kategori' => 'Kegiatan Kecamatan',
            'keterangan' => 'Deskripsi galeri terbarui',
            'tipe' => 'foto',
            'is_published' => 1,
        ]);

        $updateResponse->assertRedirect(route('galeri.index'));
        $this->assertDatabaseHas('galeri', [
            'id' => $galeri->id,
            'judul' => 'Galeri Terbarui via Admin',
        ]);

        // Delete
        $deleteResponse = $this->delete(route('galeri.destroy', $galeri->id));
        $deleteResponse->assertRedirect(route('galeri.index'));
        $this->assertDatabaseMissing('galeri', ['id' => $galeri->id]);
    }

    public function test_admin_can_crud_agenda(): void
    {
        $admin = User::whereHas('role', function ($q) {
            $q->where('slug', 'super-admin');
        })->first() ?? User::factory()->create();

        $this->actingAs($admin);

        // Index
        $this->get(route('agenda.index'))->assertStatus(200);

        // Create page
        $this->get(route('agenda.create'))->assertStatus(200);

        // Store
        $storeResponse = $this->post(route('agenda.store'), [
            'judul' => 'Agenda Baru via Admin',
            'kategori' => 'Rapat & Musyawarah',
            'penyelenggara' => 'Kecamatan Soreang',
            'lokasi' => 'Aula Utama',
            'mulai_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'ringkasan' => 'Ringkasan tes',
            'deskripsi' => 'Deskripsi tes',
            'is_published' => 1,
        ]);

        $storeResponse->assertRedirect(route('agenda.index'));
        $this->assertDatabaseHas('agenda', [
            'judul' => 'Agenda Baru via Admin',
        ]);

        $agenda = Agenda::where('judul', 'Agenda Baru via Admin')->first();

        // Edit page & update
        $this->get(route('agenda.edit', $agenda->id))->assertStatus(200);

        $updateResponse = $this->put(route('agenda.update', $agenda->id), [
            'judul' => 'Agenda Terbarui via Admin',
            'kategori' => 'Rapat & Musyawarah',
            'penyelenggara' => 'Kecamatan Soreang',
            'lokasi' => 'Aula Utama Terbarui',
            'mulai_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'ringkasan' => 'Ringkasan terbarui',
            'deskripsi' => 'Deskripsi terbarui',
            'is_published' => 1,
        ]);

        $updateResponse->assertRedirect(route('agenda.index'));
        $this->assertDatabaseHas('agenda', [
            'id' => $agenda->id,
            'judul' => 'Agenda Terbarui via Admin',
        ]);

        // Delete
        $deleteResponse = $this->delete(route('agenda.destroy', $agenda->id));
        $deleteResponse->assertRedirect(route('agenda.index'));
        $this->assertDatabaseMissing('agenda', ['id' => $agenda->id]);
    }
}
