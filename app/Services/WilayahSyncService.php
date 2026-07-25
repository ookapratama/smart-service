<?php

namespace App\Services;

use App\Jobs\SyncWilayahJob;
use App\Models\Wilayah;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

/**
 * Sinkronisasi data referensi wilayah administratif dari wilayah.id.
 * Hanya sampai level 3 (kecamatan) — lihat App\Enums\WilayahLevel.
 * Dipanggil oleh perintah `php artisan wilayah:sync`, idempoten (upsert by code).
 */
class WilayahSyncService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.wilayah_id.base_url'), '/');
    }

    public function syncProvinces(): int
    {
        return $this->upsertLevel($this->fetch('provinces.json'), null, 1);
    }

    public function syncRegencies(string $provinceCode): int
    {
        return $this->upsertLevel($this->fetch("regencies/{$provinceCode}.json"), $provinceCode, 2);
    }

    public function syncDistricts(string $regencyCode): int
    {
        return $this->upsertLevel($this->fetch("districts/{$regencyCode}.json"), $regencyCode, 3);
    }

    /**
     * Sinkronisasi penuh provinsi -> kabupaten/kota -> kecamatan, tanpa progress bar
     * (dipakai oleh SyncWilayahJob). Logikanya sama dengan yang dijalankan
     * `php artisan wilayah:sync`, hanya tanpa output console.
     *
     * @return array{provinces: int, regencies: int, districts: int, failed: array<int, string>}
     */
    public function syncAll(): array
    {
        $failed = [];

        $provinces = $this->syncProvinces();

        $regencyTotal = 0;
        foreach (Wilayah::provinsi()->pluck('code') as $provinceCode) {
            try {
                $regencyTotal += $this->syncRegencies($provinceCode);
            } catch (ConnectionException|RequestException $e) {
                $failed[] = "regencies/{$provinceCode}";
            }
            Sleep::for(100)->milliseconds();
        }

        $districtTotal = 0;
        foreach (Wilayah::kabupatenKota()->pluck('code') as $regencyCode) {
            try {
                $districtTotal += $this->syncDistricts($regencyCode);
            } catch (ConnectionException|RequestException $e) {
                $failed[] = "districts/{$regencyCode}";
            }
            Sleep::for(100)->milliseconds();
        }

        return [
            'provinces' => $provinces,
            'regencies' => $regencyTotal,
            'districts' => $districtTotal,
            'failed' => $failed,
        ];
    }

    /**
     * Status sinkronisasi terakhir, dibaca dari cache yang ditulis SyncWilayahJob.
     *
     * @return array{status: string, last_synced_at: ?string, last_result: ?array}
     */
    public function currentStatus(): array
    {
        return [
            'status' => Cache::get(SyncWilayahJob::CACHE_STATUS, 'idle'),
            'last_synced_at' => Cache::get(SyncWilayahJob::CACHE_LAST_SYNCED_AT),
            'last_result' => Cache::get(SyncWilayahJob::CACHE_LAST_RESULT),
        ];
    }

    /**
     * @return array<int, array{code: string, name: string}>
     */
    protected function fetch(string $path): array
    {
        $response = Http::timeout(15)
            ->retry(2, 500)
            ->get("{$this->baseUrl}/{$path}")
            ->throw();

        return $response->json('data') ?? [];
    }

    /**
     * @param  array<int, array{code: string, name: string}>  $items
     */
    protected function upsertLevel(array $items, ?string $parentCode, int $level): int
    {
        Wilayah::upsert(
            array_map(fn (array $item) => [
                'code' => $item['code'],
                'parent_code' => $parentCode,
                'name' => $item['name'],
                'level' => $level,
            ], $items),
            ['code'],
            ['name', 'level', 'parent_code']
        );

        return count($items);
    }
}
