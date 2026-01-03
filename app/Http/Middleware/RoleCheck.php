<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role - dapat berupa single role atau multiple roles separated by comma (e.g., "admin,cs")
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Parse multiple roles if separated by comma
        $allowedRoles = array_map('trim', explode(',', $role));
        
        // Determine which guard to use based on requested role
        $guard = 'web'; // default
        if (in_array('member', $allowedRoles)) {
            $guard = 'member';
        } elseif (in_array('admin', $allowedRoles)) {
            $guard = 'admin';
        } elseif (in_array('cs', $allowedRoles)) {
            $guard = 'cs';
        }
        
        // Check if user is authenticated with the appropriate guard
        if (!auth($guard)->check()) {
            // Determine login route based on guard
            $loginRoute = match($guard) {
                'member' => '/member/login',
                'admin' => '/admin/login',
                'cs' => '/login',
                default => '/login'
            };
            return redirect($loginRoute)->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = auth($guard)->user();

        // Check if user has any of the required roles
        // For members, the guard itself determines the role (no role column needed)
        if ($guard === 'member') {
            // Members authenticated via 'member' guard automatically have 'member' role
            if (!in_array('member', $allowedRoles)) {
                abort(403, 'Akses ditolak. Anda tidak memiliki role yang diperlukan.');
            }
        } else {
            // For User model (admin, cs), check the role column
            // STRICT: User harus memiliki salah satu role yang diizinkan
            if (!isset($user->role) || !in_array($user->role, $allowedRoles)) {
                abort(403, 'Akses ditolak. Role "' . ($user->role ?? 'unknown') . '" tidak diizinkan untuk mengakses halaman ini.');
            }
        }

        return $next($request);
    }
}
