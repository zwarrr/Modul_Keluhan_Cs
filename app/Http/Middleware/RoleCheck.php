<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = auth()->user();

        // Check if user has the required role
        if ($user->role !== $role) {
            // If trying to access admin area but not admin
            if ($role === 'admin') {
                abort(403, 'Akses ditolak. Anda tidak memiliki hak akses admin.');
            }
            
            // If specific role required but user doesn't have it
            abort(403, 'Akses ditolak. Anda tidak memiliki role yang diperlukan.');
        }

        return $next($request);
    }
}
