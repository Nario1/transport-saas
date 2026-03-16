<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmpresaController; 
use App\Http\Controllers\PropietarioController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\TributoController;
use App\Http\Controllers\SancionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;

// 1. PÚBLICAS E INVITADOS
Route::get('/', function () { return view('welcome'); });

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// 2. RUTAS PROTEGIDAS POR AUTH
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- PANEL MAESTRO (Exclusivo para German / SUPER_ADMIN) ---
    Route::middleware('role:SUPER_ADMIN')->prefix('superadmin')->group(function () {
        
        // Dashboard General (Resumen del SaaS)
        Route::get('/dashboard', [EmpresaController::class, 'dashboard'])->name('superadmin.dashboard');

        // Gestión de Empresas (Dentro de superadmin/empresas/)
        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::patch('/empresas/{empresa}/toggle', [EmpresaController::class, 'toggleStatus'])->name('empresas.toggle');
        
        // Rutas adicionales de empresas (Edit, Update, Destroy)
        Route::get('/empresas/{empresa}/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
        Route::put('/empresas/{empresa}', [EmpresaController::class, 'update'])->name('empresas.update');
        Route::delete('/empresas/{empresa}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');
    });

    // --- MÓDULOS OPERATIVOS (Para Dueños de Empresa y Personal) ---

    // 1. DASHBOARD: Ahora con su propio permiso independiente
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:ver dashboard');
    

    // 2. GESTIÓN DE PERSONAL
    Route::middleware('permission:gestionar usuarios')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // 3. GESTIÓN DE ROLES
    Route::middleware('permission:gestionar roles')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // 4. PROPIETARIOS
    Route::middleware('permission:ver propietarios')->group(function () {
        Route::get('/propietarios', [PropietarioController::class, 'index'])->name('propietarios.index');
        Route::get('/propietarios/create', [PropietarioController::class, 'create'])->name('propietarios.create');
        Route::post('/propietarios/store', [PropietarioController::class, 'store'])->name('propietarios.store');
        Route::get('/propietarios/{propietario}/edit', [PropietarioController::class, 'edit'])->name('propietarios.edit');
        Route::put('/propietarios/{propietario}', [PropietarioController::class, 'update'])->name('propietarios.update');
        Route::delete('/propietarios/{propietario}', [PropietarioController::class, 'destroy'])->name('propietarios.destroy');
    });

    // 5. VEHÍCULOS
    Route::middleware('permission:ver vehiculos')->group(function () {
        Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
        Route::get('/vehiculos/create', [VehiculoController::class, 'create'])->name('vehiculos.create');
        Route::post('/vehiculos/store', [VehiculoController::class, 'store'])->name('vehiculos.store');
        Route::get('/vehiculos/{vehiculo}/edit', [VehiculoController::class, 'edit'])->name('vehiculos.edit');
        Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show'])->name('vehiculos.show');
        Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
        Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');
    });

    // 6. CONDUCTORES
    Route::middleware('permission:ver conductores')->group(function () {

        Route::get('/conductores', [ConductorController::class, 'index'])->name('conductores.index');
        Route::get('/conductores/create', [ConductorController::class, 'create'])->name('conductores.create');
        Route::post('/conductores/store', [ConductorController::class, 'store'])->name('conductores.store');
        Route::get('/conductores/{conductor}/edit', [ConductorController::class, 'edit'])->name('conductores.edit');
        Route::put('/conductores/{conductor}', [ConductorController::class, 'update'])->name('conductores.update');
        Route::delete('/conductores/{conductor}', [ConductorController::class, 'destroy'])->name('conductores.destroy');

    });

    // 7. RUTAS
    Route::middleware('permission:ver rutas')->group(function () {

        Route::get('/rutas', [RutaController::class, 'index'])->name('rutas.index');
        Route::get('/rutas/create', [RutaController::class, 'create'])->name('rutas.create');
        Route::post('/rutas/store', [RutaController::class, 'store'])->name('rutas.store');
        Route::get('/rutas/{ruta}/edit', [RutaController::class, 'edit'])->name('rutas.edit');
        Route::get('/rutas/{ruta}', [RutaController::class, 'show'])->name('rutas.show');
        Route::put('/rutas/{ruta}', [RutaController::class, 'update'])->name('rutas.update');
        Route::delete('/rutas/{ruta}', [RutaController::class, 'destroy'])->name('rutas.destroy');

        // Paraderos de la ruta
        Route::post('/rutas/{ruta}/paraderos', [RutaController::class, 'storeParadero'])->name('rutas.paraderos.store');
        Route::delete('/rutas/{ruta}/paraderos/{paradero}', [RutaController::class, 'destroyParadero'])->name('rutas.paraderos.destroy');

    });

    // 8. VUELTAS
    Route::get('/vueltas', function () { return view('admin.vueltas.index'); })
        ->name('vueltas.index')
        ->middleware('permission:ver vueltas');

    // 9. TRIBUTOS
    Route::middleware('permission:ver tributos')->group(function () {

        Route::get('/tributos', [TributoController::class, 'index'])->name('tributos.index');
        Route::get('/tributos/registrar', [TributoController::class, 'create'])->name('tributos.create');
        Route::post('/tributos', [TributoController::class, 'store'])->name('tributos.store');
        Route::post('/tributos/{tributo}/cobrar', [TributoController::class, 'cobrar'])->name('tributos.cobrar');
        Route::post('/tributos/generar-dia', [TributoController::class, 'generarDelDia'])->name('tributos.generar');

    });

    // 10. SANCIONES
    Route::middleware('permission:ver sanciones')->group(function () {
        Route::get('/sanciones', [SancionController::class, 'index'])->name('sanciones.index');
        Route::get('/sanciones/crear', [SancionController::class, 'create'])->name('sanciones.create');
        Route::post('/sanciones', [SancionController::class, 'store'])->name('sanciones.store');
        Route::post('/sanciones/{sancion}/pagar', [SancionController::class, 'pagar'])->name('sanciones.pagar');
        Route::delete('/sanciones/{sancion}', [SancionController::class, 'destroy'])->name('sanciones.destroy');
    });

    // 11. REPORTES
    Route::middleware('permission:ver reportes')->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/tributos', [ReporteController::class, 'tributos'])->name('reportes.tributos');
        Route::get('/reportes/vueltas', [ReporteController::class, 'vueltas'])->name('reportes.vueltas');
        Route::get('/reportes/sanciones', [ReporteController::class, 'sanciones'])->name('reportes.sanciones');
        Route::get('/reportes/documentos', [ReporteController::class, 'documentos'])->name('reportes.documentos');
        Route::get('/reportes/deudas', [ReporteController::class, 'deudas'])->name('reportes.deudas');
    });

    //ROL DE CONDUCTOR
    Route::middleware(['auth', 'role:Conductor'])->prefix('conductor')->name('conductor.')->group(function () {
    
    // El Home del conductor
        Route::get('/dashboard', [DashboardController::class, 'indexConductor'])->name('index');
            
        // Sus pagos
        Route::get('/pagos', [TributoController::class, 'panelConductor'])->name('pagos.index');
        
        // El procesamiento de Mercado Pago
        Route::post('/pagar/{tributo}', [TributoController::class, 'procesarPago'])->name('pagar');
    });
});