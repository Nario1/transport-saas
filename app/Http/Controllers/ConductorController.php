<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConductorController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $conductores = Conductor::where('empresa_id', $user->empresa_id)
            ->with(['propietario', 'vehiculos'])
            ->withCount('vehiculos')
            ->orderBy('nombre')
            ->paginate(20);

        return view('admin.conductores.index', compact('conductores'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $propietarios = Propietario::where('empresa_id', $user->empresa_id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.conductores.create', compact('propietarios'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'nombre'         => 'required|string|max:120',
            'apellidos'      => 'nullable|string|max:120',
            'dni'            => 'nullable|string|max:8',
            'telefono'       => 'nullable|string|max:15',
            'email'          => 'nullable|email|max:120',
            'direccion'      => 'nullable|string|max:255',
            'propietario_id' => 'nullable|exists:propietarios,id',
            'tipo_licencia'  => 'required|string|max:10',
            'licencia_vence' => 'nullable|date',
            'estado'         => 'required|in:activo,suspendido,inactivo',
            'notas'          => 'nullable|string|max:500',
        ], [
            'nombre.required'        => 'El nombre es obligatorio.',
            'tipo_licencia.required' => 'El tipo de licencia es obligatorio.',
            'estado.required'        => 'El estado es obligatorio.',
            'email.email'            => 'El correo no tiene un formato válido.',
        ]);

        // Verificar que el propietario_id pertenezca a la misma empresa
        if (!empty($data['propietario_id'])) {
            $this->verificarPropietario($data['propietario_id'], $user->empresa_id);
        }

        $data['empresa_id'] = $user->empresa_id;

        Conductor::create($data);

        return redirect()->route('conductores.index')
            ->with('success', 'Conductor registrado correctamente.');
    }

    public function show(Conductor $conductor)
    {
        $this->verificarEmpresa($conductor);

        $conductor->load([
            'propietario',
            'vehiculos',
            'vueltas'  => fn($q) => $q->latest('fecha')->limit(10),
            'tributos' => fn($q) => $q->latest('fecha')->limit(10),
            'sanciones'=> fn($q) => $q->latest('fecha')->limit(5),
        ]);

        return view('admin.conductores.show', compact('conductor'));
    }

    public function edit(Conductor $conductor)
    {
        $this->verificarEmpresa($conductor);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $propietarios = Propietario::where('empresa_id', $user->empresa_id)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.conductores.edit', compact('conductor', 'propietarios'));
    }

    public function update(Request $request, Conductor $conductor)
    {
        $this->verificarEmpresa($conductor);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'nombre'         => 'required|string|max:120',
            'apellidos'      => 'nullable|string|max:120',
            'dni'            => 'nullable|string|max:8',
            'telefono'       => 'nullable|string|max:15',
            'email'          => 'nullable|email|max:120',
            'direccion'      => 'nullable|string|max:255',
            'propietario_id' => 'nullable|exists:propietarios,id',
            'tipo_licencia'  => 'required|string|max:10',
            'licencia_vence' => 'nullable|date',
            'estado'         => 'required|in:activo,suspendido,inactivo',
            'notas'          => 'nullable|string|max:500',
        ]);

        if (!empty($data['propietario_id'])) {
            $this->verificarPropietario($data['propietario_id'], $user->empresa_id);
        }

        $conductor->update($data);

        return redirect()->route('conductores.index')
            ->with('success', 'Conductor actualizado correctamente.');
    }

    public function destroy(Conductor $conductor)
    {
        $this->verificarEmpresa($conductor);

        if ($conductor->vehiculos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un conductor que tiene vehículos asignados.');
        }

        $conductor->delete();

        return redirect()->route('conductores.index')
            ->with('success', 'Conductor eliminado correctamente.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function verificarEmpresa(Conductor $conductor): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_if($conductor->empresa_id !== $user->empresa_id, 403, 'Acceso no autorizado.');
    }

    private function verificarPropietario(int $propietarioId, int $empresaId): void
    {
        $existe = Propietario::where('id', $propietarioId)
            ->where('empresa_id', $empresaId)
            ->exists();

        abort_if(!$existe, 403, 'El propietario no pertenece a tu empresa.');
    }
}