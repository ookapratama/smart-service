<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected $cacheKey = 'app_settings';

    /**
     * Get all settings grouped by group name
     */
    public function getAllGrouped()
    {
        return Setting::all()->groupBy('group');
    }

    /**
     * Get setting value by key (with caching)
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAllCached();
        return $settings[$key] ?? $default;
    }

    /**
     * Get all settings from cache or DB
     */
    public function getAllCached()
    {
        try {
            return Cache::rememberForever($this->cacheKey, fn () => $this->loadFromDatabase());
        } catch (\Throwable $e) {
            // Cache store unavailable (e.g. database cache before first migrate)
            return $this->loadFromDatabase();
        }
    }

    /**
     * Load settings directly from DB, safe to call before migrations exist
     */
    protected function loadFromDatabase(): array
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return [];
            }
            return Setting::pluck('value', 'key')->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Update settings
     */
    public function updateMany(array $data)
    {
        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        
        $this->clearCache();
    }

    /**
     * Clear settings cache
     */
    public function clearCache()
    {
        Cache::forget($this->cacheKey);
    }
}
