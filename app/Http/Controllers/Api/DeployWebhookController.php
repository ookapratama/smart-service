<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DeployWebhookController extends Controller
{
    // Runs post-deploy artisan commands over HTTP instead of SSH — this host resets
    // SSH connections from GitHub Actions runners, so the CI/CD workflow curls this
    // route after uploading files via FTP instead. See DEPLOYMENT.md.
    public function run(Request $request)
    {
        $expected = config('services.deploy.webhook_token');
        $provided = $request->header('X-Deploy-Token', '');

        if (! $expected || ! hash_equals($expected, $provided)) {
            return ResponseHelper::error('Forbidden', 403);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            Artisan::call('queue:restart');
        } catch (\Exception $e) {
            report($e);

            return ResponseHelper::error('Deploy command failed', 500);
        }

        return ResponseHelper::success(message: 'Deployed');
    }
}
