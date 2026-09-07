<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('api')->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = auth('api')->user();
        if (!$user->is_active) {
            $user->token()?->revoke();
            return response()->json(['error' => 'Account disabled'], 403);
        }
        return $next($request);
    }
}
