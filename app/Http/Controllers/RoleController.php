<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Role::withCount('users');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user?->email !== 'superadmin@transjunin.com') {
            $query->where('name', '!=', 'SUPER_ADMIN');
        }

        $roles = $query->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allPermissions = \Spatie\Permission\Models\Permission::pluck('name');
        $permissions = Permission::where('name', '!=', 'gestionar empresas')->get();
        return view('admin.roles.create', compact('allPermissions', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Este nombre de rol ya existe.',
        ]);

        try {
            // Creamos el rol (convertimos a mayúsculas para mantener consistencia)
            $role = Role::create([
                'name' => strtoupper($request->name),
                'guard_name' => 'web'
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return redirect()->route('roles.index')
                ->with('success', 'El rol "' . $role->name . '" ha sido creado correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    // Muestra el formulario de edición
    public function edit($id)
    {
        $role = \Spatie\Permission\Models\Role::findOrFail($id);
        
        // Obtenemos todos los permisos para llenar los checkboxes
        $allPermissions = \Spatie\Permission\Models\Permission::pluck('name');

        return view('admin.roles.edit', compact('role', 'allPermissions'));
    }

    // Procesa la actualización
    public function update(Request $request, $id)
    {
        $role = \Spatie\Permission\Models\Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
        ]);

        // Actualizar nombre (solo si no es SUPER_ADMIN para evitar bloqueos)
        if ($role->name !== 'SUPER_ADMIN') {
            $role->name = strtoupper($request->name);
            $role->save();
        }

        // Sincronizar permisos (Spatie se encarga de quitar los anteriores y poner los nuevos)
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')
            ->with('success', 'El rol "' . $role->name . '" se actualizó correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = \Spatie\Permission\Models\Role::findOrFail($id);

        // 1. Prohibir eliminar el SUPER_ADMIN
        if ($role->name === 'SUPER_ADMIN') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol maestro (SUPER_ADMIN) no puede ser eliminado.');
        }

        // 2. Verificar si el rol tiene usuarios asignados
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar un rol que ya tiene usuarios vinculados.');
        }

        // 3. Eliminar el rol (Spatie se encarga de limpiar las tablas intermedias)
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'El rol ha sido eliminado correctamente.');
    }
}
