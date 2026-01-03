<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }
        
        // Check if the request is for member routes
        if ($request->is('member/*') || $request->is('chat/*')) {
            return route('member.login.form');
        }
        
        return route('login');
    }
}
