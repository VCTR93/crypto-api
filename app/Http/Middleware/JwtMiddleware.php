<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
     // implementación de la lógica de validación del token JWT
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->bearerToken()){
           return response()->json(['error' => 'Token no encontrado'], 401);
        }
        
        return $next($request);
    }
}
