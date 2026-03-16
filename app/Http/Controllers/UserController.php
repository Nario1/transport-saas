<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        $users = User::where('empresa_id', $authUser->empresa_id)
            ->with('roles')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'SUPER_ADMIN')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required'
        ], [
            'email.unique' => 'Este correo ya está registrado en el sistema.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        /** @var User $authUser */
        $authUser = Auth::user();

        $user = User::create([
            'empresa_id' => $authUser->empresa_id,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'activo'     => true
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Usuario ' . $user->name . ' creado exitosamente.');
    }

    public function edit(User $user)
    {
        if ($user->empresa_id !== Auth::user()->empresa_id) abort(403);

        $roles = Role::where('name', '!=', 'SUPER_ADMIN')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza el usuario asegurando el scope de empresa.
     */
    public function update(Request $request, User $user)
    {
        // 1. Seguridad: Verificar que el usuario pertenezca a la misma empresa
        /** @var User $authUser */
        $authUser = Auth::user();
        if ($user->empresa_id !== $authUser->empresa_id) abort(403);

        // 2. Validación
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed', // Nullable para que sea opcional
            'role'     => 'required'
        ], [
            'email.unique' => 'Este correo ya pertenece a otro usuario.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        // 3. Preparar datos para actualizar
        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Solo actualizar password si se escribió algo
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // 4. Sincronizar Roles (Spatie quita los anteriores y pone el nuevo)
        // Evitamos que un Admin normal intente asignarse el rol SUPER_ADMIN mediante request
        if ($request->role !== 'SUPER_ADMIN') {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('users.index')
            ->with('success', "Usuario {$user->name} actualizado correctamente.");
    }

    public function destroy($id)
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        $user = User::where('empresa_id', $authUser->empresa_id)->findOrFail($id);

        if ($user->id === $authUser->id) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if ($user->hasRole('SUPER_ADMIN') && !$authUser->hasRole('SUPER_ADMIN')) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar a un Súper Administrador.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "El usuario {$userName} ha sido eliminado.");
    }
}