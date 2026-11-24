<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || !$user->canAccessAdminPanel()) {
            return redirect('/login')->with('error', 'Akses ditolak.');
        }
        if ($user->isAdmin()) {
            $version = $user->adminMfaVersion();
            if (($version || config('auth.admin_mfa_required'))
                && (!$version || $request->session()->get('admin_mfa_version') !== $version)) {
                return redirect()->route('admin-mfa.show');
            }
        }

        return $next($request);
    }
}
