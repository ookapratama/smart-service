<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Services\FileUploadService;
use App\Services\SettingService;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $service,
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Display settings page
     */
    public function index()
    {
        $groupedSettings = $this->service->getAllGrouped();

        return view('pages.settings.index', compact('groupedSettings'));
    }

    /**
     * Update settings
     */
    public function update(SettingRequest $request)
    {
        $data = $request->except(['_token', 'app_logo', 'app_favicon', 'hero_bg', 'hero_image', 'service_image']);

        // Handle image uploads
        if ($request->hasFile('app_logo')) {
            $media = $this->fileUploadService->replace($this->service->get('app_logo'), $request->file('app_logo'), 'settings', 'public');
            $data['app_logo'] = $media->path;
        }

        if ($request->hasFile('app_favicon')) {
            $media = $this->fileUploadService->replace($this->service->get('app_favicon'), $request->file('app_favicon'), 'settings', 'public');
            $data['app_favicon'] = $media->path;
        }

        if ($request->hasFile('hero_bg')) {
            $media = $this->fileUploadService->replace($this->service->get('hero_bg'), $request->file('hero_bg'), 'settings', 'public');
            $data['hero_bg'] = $media->path;
        }

        if ($request->hasFile('hero_image')) {
            $media = $this->fileUploadService->replace($this->service->get('hero_image'), $request->file('hero_image'), 'settings', 'public');
            $data['hero_image'] = $media->path;
        }

        if ($request->hasFile('service_image')) {
            $media = $this->fileUploadService->replace($this->service->get('service_image'), $request->file('service_image'), 'settings', 'public');
            $data['service_image'] = $media->path;
        }

        $this->service->updateMany($data);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    /**
     * Clear settings cache
     */
    public function clearCache()
    {
        $this->service->clearCache();

        return redirect()->back()->with('success', 'Cache pengaturan berhasil dibersihkan!');
    }
}
