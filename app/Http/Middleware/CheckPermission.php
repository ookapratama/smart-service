<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $menuSlug): Response
    {
        $actionMap = [
            'index' => 'read',
            'show' => 'read',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
            'health' => 'read',
            'backup' => 'create',
        ];

        // Full route-name map, checked before the suffix map
        $routeNameMap = [
            'products.export.excel' => 'read',
            'products.export.pdf' => 'read',
            'products.import.excel' => 'create',
            'settings.clear-cache' => 'update',
        ];

        $routeName = $request->route()->getName();
        $routeParts = explode('.', $routeName);
        $method = end($routeParts);

        $action = $routeNameMap[$routeName] ?? $actionMap[$method] ?? null;

        // Fail closed: unknown routes are denied instead of defaulting to read
        if ($action === null) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk tindakan ini.');
        }

        if (! $request->user() || ! $request->user()->hasPermission($menuSlug, $action)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk tindakan ini.');
        }

        return $next($request);
    }
}
