<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || $request->user()->role !== $role) {
            return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esta sección');
        }

        return $next($request);
    }
}
