<?php

namespace App\Console\Commands;

use App\Jobs\SyncWilayahJob;
use App\Models\Wilayah;
use App\Services\WilayahSyncService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Sleep;

class SyncWilayah extends Command
{
    /**
     * @var string
     */
    protected $signature = 'wilayah:sync';

    /**
     * @var string
     */
    protected $description = 'Sinkronkan data provinsi, kabupaten/kota, dan kecamatan dari wilayah.id';

    public function handle(WilayahSyncService $service): int
    {
        $failed = [];

        Cache::forever(SyncWilayahJob::CACHE_STATUS, 'running');

        $this->info('Menyinkronkan data provinsi...');
        $provinceCount = $service->syncProvinces();
        $this->info("{$provinceCount} provinsi tersimpan.");

        $provinces = Wilayah::provinsi()->pluck('code');
        $regencyTotal = 0;

        $this->info('Menyinkronkan data kabupaten/kota...');
        $bar = $this->output->createProgressBar($provinces->count());
        $bar->start();

        foreach ($provinces as $provinceCode) {
            try {
                $regencyTotal += $service->syncRegencies($provinceCode);
            } catch (ConnectionException|RequestException $e) {
                $failed[] = "regencies/{$provinceCode}";
            }
            Sleep::for(100)->milliseconds();
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info("{$regencyTotal} kabupaten/kota tersimpan.");

        $regencies = Wilayah::kabupatenKota()->pluck('code');
        $districtTotal = 0;

        $this->info('Menyinkronkan data kecamatan (proses ini bisa memakan waktu beberapa menit)...');
        $bar = $this->output->createProgressBar($regencies->count());
        $bar->start();

        foreach ($regencies as $regencyCode) {
            try {
                $districtTotal += $service->syncDistricts($regencyCode);
            } catch (ConnectionException|RequestException $e) {
                $failed[] = "districts/{$regencyCode}";
            }
            Sleep::for(100)->milliseconds();
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info("{$districtTotal} kecamatan tersimpan.");

        if (! empty($failed)) {
            $this->warn(count($failed).' permintaan gagal setelah percobaan ulang. Jalankan lagi `php artisan wilayah:sync` untuk melengkapi data yang terlewat (sinkronisasi ini aman diulang):');
            foreach ($failed as $path) {
                $this->line("  - {$path}");
            }
        }

        $this->info('Sinkronisasi wilayah selesai.');

        Cache::forever(SyncWilayahJob::CACHE_STATUS, 'idle');
        Cache::forever(SyncWilayahJob::CACHE_LAST_SYNCED_AT, now()->toDateTimeString());
        Cache::forever(SyncWilayahJob::CACHE_LAST_RESULT, [
            'provinces' => $provinceCount,
            'regencies' => $regencyTotal,
            'districts' => $districtTotal,
            'failed' => $failed,
        ]);

        return self::SUCCESS;
    }
}
