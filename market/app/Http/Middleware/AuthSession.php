<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('user')) {
            return redirect('/registro')->with('error', 'Debes iniciar sesión');
        }

        return $next($request);
    }
}
