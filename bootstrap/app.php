<?php

use App\Helpers\ResponseHelper;
use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureStaffUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.permission' => CheckPermission::class,
            'maintenance' => CheckMaintenanceMode::class,
            'staff' => EnsureStaffUser::class,
        ]);

        $middleware->appendToGroup('web', [
            CheckMaintenanceMode::class,
        ]);

        // Guest area warga (tiket-saya) diarahkan ke login warga, bukan login
        // admin; user login yang menyasar halaman guest diarahkan per role.
        $middleware->redirectGuestsTo(fn ($request) => $request->routeIs('tiket-saya.*')
            ? route('warga.login')
            : route('login'));

        $middleware->redirectUsersTo(fn ($request) => $request->user()?->isWarga()
            ? route('tiket-saya.index')
            : route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            // ValidationException punya renderer bawaan yang benar untuk kedua
            // dunia (redirect + errors bag untuk web, 422 JSON untuk AJAX) —
            // jangan diubah jadi 500 oleh renderer generik di bawah.
            if ($e instanceof ValidationException) {
                return null;
            }

            // Jika request meminta JSON (API), berikan response JSON
            if ($request->is('api/*') || $request->expectsJson()) {
                Log::error($e);

                $code = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

                return ResponseHelper::error(
                    $e->getMessage() ?: 'Internal Server Error',
                    code: $code
                );
            }

            // Untuk Web, biarkan Laravel menangani AuthenticationException agar bisa redirect ke login
            if ($e instanceof AuthenticationException) {
                return null; // Biarkan default handling (redirect ke /login)
            }
        });
    })->create();
