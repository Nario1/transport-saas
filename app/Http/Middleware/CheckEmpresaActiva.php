<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckEmpresaActiva
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Si no está logueado, dejar que otros middlewares manejen la redirección
        if (!Auth::check()) {
            return $next($request);
        }

        /** * Tipado para reconocer los métodos de Spatie
         * @var \App\Models\User $user 
         */
        $user = Auth::user();

        // 2. Si es SUPER_ADMIN (tú), dejarlo pasar siempre
        if ($user->hasRole('SUPER_ADMIN')) {
            return $next($request);
        }

        // 3. Verificar si su empresa está activa
        $empresa = $user->empresa;

        if (!$empresa || $empresa->activa == 0) {
            // Expulsión inmediata
            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Su empresa se encuentra suspendida. Contacte con el soporte técnico de TransJunín.');
        }

        return $next($request);
    }
}