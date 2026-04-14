<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin can access everything
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Ensure user has a school assigned
        if (!$user->school_id) {
            abort(403, 'Anda belum ditugaskan ke sekolah manapun.');
        }

        return $next($request);
    }
}
