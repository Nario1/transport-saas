<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropietarioController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $propietarios = Propietario::where('empresa_id', $user->empresa_id)
            ->withCount('vehiculos')
            ->orderBy('nombre')
            ->paginate(20);

        return view('admin.propietarios.index', compact('propietarios'));
    }

    public function create()
    {
        return view('admin.propietarios.create');
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Validación con mensajes personalizados
        $data = $request->validate([
            'nombre'       => 'required|string|max:120',
            'apellidos'    => 'required|string|max:120', // Requerido según tu lógica anterior
            'dni'          => 'nullable|string|max:8',
            'telefono'     => 'nullable|string|max:15',
            'direccion'    => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:120',
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'email.email'        => 'El formato del correo no es válido.',
        ]);

        try {
            // 2. Asignación de datos automáticos
            $data['empresa_id'] = $user->empresa_id;
            $data['activo']     = true;

            // 3. Creación
            $propietario = Propietario::create($data);

            return redirect()->route('propietarios.index')
                ->with('success', 'Propietario "' . $propietario->nombre . '" registrado con éxito.');

        } catch (\Exception $e) {
            // Regresa con el error específico y mantiene lo que el usuario escribió
            return back()->withInput()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function edit(Propietario $propietario)
    {
        $this->verificarEmpresa($propietario);
        return view('admin.propietarios.edit', compact('propietario'));
    }

    public function update(Request $request, Propietario $propietario)
    {
        $this->verificarEmpresa($propietario);

        $data = $request->validate([
            'nombre'       => 'required|string|max:120',
            'apellidos'    => 'required|string|max:120',
            'dni'          => 'nullable|string|max:8',
            'telefono'     => 'nullable|string|max:15',
            'activo'       => 'boolean',
        ]);

        try {
            $propietario->update($data);
            return redirect()->route('propietarios.index')
                ->with('success', 'Datos actualizados correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Propietario $propietario)
    {
        $this->verificarEmpresa($propietario);

        if ($propietario->vehiculos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: tiene vehículos asignados.');
        }

        $propietario->delete();
        return redirect()->route('propietarios.index')
            ->with('success', 'Propietario eliminado.');
    }

    private function verificarEmpresa(Propietario $propietario): void
    {
        abort_if($propietario->empresa_id !== Auth::user()->empresa_id, 403, 'No autorizado.');
    }
}