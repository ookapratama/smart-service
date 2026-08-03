<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        // OPcache may still hold bytecode of the files FTP just overwrote; without
        // this reset, config:cache/route:cache below would rebuild from STALE code.
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        $needsFresh = ! Storage::disk('local')->exists(self::SCHEMA_REBUILD_MARKER);

        // Clear compiled caches FIRST so a failed deploy fails open (uncached but
        // correct) instead of keeping a stale route/config table. Deliberately not
        // cache:clear — that would wipe live OTP codes/cooldowns in the app cache.
        $commands = [
            ['config:clear', []],
            ['route:clear', []],
            ['view:clear', []],
            [$needsFresh ? 'migrate:fresh' : 'migrate', ['--force' => true]],
            ['db:seed', ['--force' => true]],
            ['config:cache', []],
            ['route:cache', []],
            ['view:cache', []],
            ['queue:restart', []],
        ];

        $results = [];

        // Per-command try/catch: report exactly which command failed instead of a
        // generic 500, and stop there so the response shows what already ran.
        foreach ($commands as [$command, $arguments]) {
            try {
                Artisan::call($command, $arguments);
                $results[$command] = Str::limit(trim(Artisan::output()), 1000) ?: 'OK';

                if ($command === 'migrate:fresh') {
                    Storage::disk('local')->put(self::SCHEMA_REBUILD_MARKER, now()->toDateTimeString());
                }
            } catch (\Throwable $e) {
                report($e);
                $results[$command] = 'FAILED: '.$e->getMessage();

                return ResponseHelper::error("Deploy command failed: {$command}", 500, $results);
            }
        }

        return ResponseHelper::success($results, 'Deployed');
    }
}
