<?php

namespace App\Jobs;

use App\Services\WilayahSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

/**
 * Menjalankan WilayahSyncService::syncAll() di background (queue) supaya
 * tombol "Sinkronkan Data Wilayah" di halaman admin tidak menahan request
 * HTTP selama proses berjalan (bisa memakan waktu belasan menit hingga
 * lebih dari satu jam, tergantung latensi wilayah.id).
 */
class SyncWilayahJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const CACHE_STATUS = 'wilayah_sync_status';

    public const CACHE_LAST_SYNCED_AT = 'wilayah_sync_last_synced_at';

    public const CACHE_LAST_RESULT = 'wilayah_sync_last_result';

    /**
     * Batas waktu eksekusi job (detik). Diabaikan di Windows (pcntl tidak
     * tersedia) tapi relevan saat di-deploy ke server Linux.
     */
    public int $timeout = 7200;

    public function handle(WilayahSyncService $service): void
    {
        Cache::forever(self::CACHE_STATUS, 'running');

        $result = $service->syncAll();

        Cache::forever(self::CACHE_STATUS, 'idle');
        Cache::forever(self::CACHE_LAST_SYNCED_AT, now()->toDateTimeString());
        Cache::forever(self::CACHE_LAST_RESULT, $result);
    }

    public function failed(\Throwable $e): void
    {
        Cache::forever(self::CACHE_STATUS, 'failed');
    }
}
