<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Importante para que el DocBlock funcione

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login.index');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            /** * ESTA LÍNEA ES VITAL: 
             * Le dice al editor y a Laravel que $user es un modelo User con Roles.
             * @var \App\Models\User $user 
             */
            $user = Auth::user();
            // VALIDACIÓN DE EMPRESA ACTIVA AL INTENTAR ENTRAR
            if (!$user->hasRole('SUPER_ADMIN')) {
                if (!$user->empresa || $user->empresa->activa == 0) {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Acceso denegado: Su empresa se encuentra suspendida. Contacte al administrador.');
                }
            }

            // 1. REDIRECCIÓN PARA GERMAN (DUEÑO DEL SAAS)
            // Si el usuario es el Super Admin Global
            if ($user->hasRole('SUPER_ADMIN')) {
                return redirect()->intended(route('empresas.index'));
            }

            // 2. REDIRECCIÓN DINÁMICA SEGÚN PERMISOS
            // Definimos el orden de prioridad de las rutas
            $rutasPrioridad = [
                'ver dashboard'   => 'dashboard',
                'ver propietarios' => 'propietarios.index',
                'ver vehiculos'    => 'vehiculos.index',
                'ver conductores'  => 'conductores.index',
                'ver vueltas'      => 'vueltas.index',
                'ver tributos'     => 'tributos.index',
            ];

            foreach ($rutasPrioridad as $permiso => $ruta) {
                if ($user->can($permiso)) {
                    return redirect()->intended(route($ruta));
                }
            }

            // 3. FALLBACK: Si no tiene NINGUNO de los anteriores
            // Lo mandamos al dashboard por defecto (donde el middleware se encargará de mostrar el error 403)
            return redirect()->intended(route('dashboard'));
        }

        // Si el login falla, regresamos con error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}