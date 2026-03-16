<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // Registro de alias de Spatie
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // ACTIVACIÓN DEL CANDADO DE EMPRESA
        // Se añade al grupo 'web' para que proteja todas las rutas del sistema
        $middleware->web(append: [
            \App\Http\Middleware\CheckEmpresaActiva::class,
        ]);

        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            
            // Manejo de accesos no autorizados (Spatie)
            if ($e instanceof UnauthorizedException) {
                if (Auth::check()) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return redirect()->route('login')
                    ->with('error', 'Acceso denegado. Se ha cerrado la sesión por motivos de seguridad.');
            }

            // Manejo del error 419 (Token expirado/CSRF)
            if ($response->getStatusCode() === 419) {
                return redirect()->route('login')
                    ->with('info', 'Tu sesión ha expirado por inactividad.');
            }

            return $response;
        });

    })->create();