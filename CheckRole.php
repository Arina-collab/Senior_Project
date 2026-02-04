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
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // If user is not logged in OR their role is not in the allowed list
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            return redirect('/dashboard')->with('msg', 'Unauthorized access.');
        }
        return $next($request);
    }
}
