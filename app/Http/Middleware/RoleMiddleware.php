<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next,$role): Response
    {   
        $roles = $request->session()->get('roles', []);
        if (in_array($role, $roles)) {
            return $next($request);
        }
        return response()->json(['message' => 'you must to be ' . $role], 403);
    }
}
