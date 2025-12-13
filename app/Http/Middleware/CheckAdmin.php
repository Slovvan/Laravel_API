<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado y es admin
        if (auth()->check() && auth()->user()->is_admin === 'admin') {
            return $next($request);
        }

        // Si no es admin, rechazar la solicitud con 403
        abort(403, 'seulement pour admins.');
    }
}
