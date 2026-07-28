<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DeployWebhookController extends Controller
{
    /**
     * One-time marker for the 2026-07-28 single-kecamatan schema rebuild. Several
     * migrations were edited in place (same filenames) rather than added as new
     * files, so a plain `migrate` on a server that already ran the old versions
     * would think them already applied and silently skip the schema changes. This
     * marker makes the webhook run `migrate:fresh` exactly once to force the
     * rebuild, then fall back to the normal safe incremental `migrate` forever
     * after — never `migrate:fresh` again, since that would wipe real data on
     * every future deploy. Safe to delete this constant, the marker-check branch
     * below, and this docblock once the first post-redesign deploy has confirmed
     * successful.
     */
    private const SCHEMA_REBUILD_MARKER = 'deploy/.schema-rebuilt-2026-07-28';

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
            if (! Storage::disk('local')->exists(self::SCHEMA_REBUILD_MARKER)) {
                Artisan::call('migrate:fresh', ['--force' => true]);
                Storage::disk('local')->put(self::SCHEMA_REBUILD_MARKER, now()->toDateTimeString());
            } else {
                Artisan::call('migrate', ['--force' => true]);
            }

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
