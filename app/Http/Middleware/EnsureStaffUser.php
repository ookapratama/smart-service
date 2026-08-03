<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menjaga area /admin untuk staf: role warga diarahkan ke Tiket Saya.
 * Deny-list (bukan allow-list) supaya Gate::before super-admin dan role
 * staf lain/masa depan tidak perlu didaftarkan satu per satu.
 */
class EnsureStaffUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isWarga()) {
            return redirect()->route('tiket-saya.index');
        }

        return $next($request);
    }
}
