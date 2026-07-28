<?php

use App\Helpers\ResponseHelper;
use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckPermission;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
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
        ]);

        $middleware->appendToGroup('web', [
            CheckMaintenanceMode::class,
        ]);

        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
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
