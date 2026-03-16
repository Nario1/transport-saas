<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Conductor;
use App\Models\Propietario;
use App\Models\Ruta;
use App\Models\Tributo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehiculoController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $vehiculos = Vehiculo::where('empresa_id', $user->empresa_id)
            ->with([
                'conductor',
                'propietario',
                'rutas' => fn($q) => $q->where('vehiculo_rutas.activo', true),
            ])
            ->orderBy('numero_flota')
            ->orderBy('placa')
            ->paginate(20);

        return view('admin.vehiculos.index', compact('vehiculos'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        [$conductores, $propietarios, $rutas] = $this->datosFormulario($user->empresa_id);

        return view('admin.vehiculos.create', compact('conductores', 'propietarios', 'rutas'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $this->validar($request);

        $this->verificarRelaciones($data, $user->empresa_id);

        $data['empresa_id'] = $user->empresa_id;

        $vehiculo = Vehiculo::create($data);

        // Asignar rutas seleccionadas
        if ($request->filled('rutas')) {
            $vehiculo->rutas()->sync(
                collect($request->rutas)->mapWithKeys(fn($id) => [
                    $id => ['activo' => true, 'fecha_asignacion' => today()]
                ])
            );
        }

        // Crear tributo pendiente de hoy si el vehículo es activo
        if ($vehiculo->estado === 'activo') {
            Tributo::firstOrCreate(
                ['vehiculo_id' => $vehiculo->id, 'fecha' => today()],
                [
                    'empresa_id'   => $vehiculo->empresa_id,
                    'conductor_id' => $vehiculo->conductor_id,
                    'monto'        => $user->empresa->tributo_diario ?? 24.00,
                    'estado'       => 'pendiente',
                ]
            );
        }

        return redirect()->route('vehiculos.index')
            ->with('success', "Vehículo {$vehiculo->placa_form} registrado correctamente.");
    }

    public function show(Vehiculo $vehiculo)
    {
        $this->verificarEmpresa($vehiculo);

        $vehiculo->load([
            'conductor',
            'propietario',
            'rutas'     => fn($q) => $q->where('vehiculo_rutas.activo', true),
            'vueltas'   => fn($q) => $q->latest('fecha')->limit(10),
            'tributos'  => fn($q) => $q->latest('fecha')->limit(10),
            'sanciones' => fn($q) => $q->latest('fecha')->limit(5),
        ]);

        return view('admin.vehiculos.show', compact('vehiculo'));
    }

    public function edit(Vehiculo $vehiculo)
    {
        $this->verificarEmpresa($vehiculo);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        [$conductores, $propietarios, $rutas] = $this->datosFormulario($user->empresa_id);

        $rutasAsignadas = $vehiculo->rutas()
            ->where('vehiculo_rutas.activo', true)
            ->pluck('rutas.id')
            ->toArray();

        return view('admin.vehiculos.edit', compact(
            'vehiculo', 'conductores', 'propietarios', 'rutas', 'rutasAsignadas'
        ));
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $this->verificarEmpresa($vehiculo);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $this->validar($request, $vehiculo->id);

        $this->verificarRelaciones($data, $user->empresa_id);

        $vehiculo->update($data);

        // Sincronizar rutas
        if ($request->has('rutas')) {
            $vehiculo->rutas()->sync(
                collect($request->rutas)->mapWithKeys(fn($id) => [
                    $id => ['activo' => true, 'fecha_asignacion' => today()]
                ])
            );
        } else {
            // Si no mandaron rutas, desactivar todas
            $idsActuales = $vehiculo->rutas()->pluck('rutas.id')->toArray();
            foreach ($idsActuales as $rutaId) {
                $vehiculo->rutas()->updateExistingPivot($rutaId, ['activo' => false]);
            }
        }

        return redirect()->route('vehiculos.index')
            ->with('success', "Vehículo {$vehiculo->placa_form} actualizado correctamente.");
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $this->verificarEmpresa($vehiculo);

        if ($vehiculo->vueltas()->count() > 0 || $vehiculo->tributos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un vehículo que tiene vueltas o tributos registrados.');
        }

        $vehiculo->rutas()->detach();
        $vehiculo->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function validar(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            'placa'              => "required|string|max:8|unique:vehiculos,placa,{$ignorarId}",
            'numero_flota'       => 'nullable|integer|min:1|max:9999',
            'marca'              => 'nullable|string|max:60',
            'modelo'             => 'nullable|string|max:60',
            'color'              => 'nullable|string|max:40',
            'anio'               => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'numero_motor'       => 'nullable|string|max:30',
            'numero_chasis'      => 'nullable|string|max:30',
            'propietario_id'     => 'nullable|exists:propietarios,id',
            'conductor_id'       => 'nullable|exists:conductores,id',
            'soat_vence'         => 'nullable|date',
            'rev_tecnica_vence'  => 'nullable|date',
            'tarjeta_prop_vence' => 'nullable|date',
            'estado'             => 'required|in:activo,inactivo,sin_salir,mantenimiento',
            'notas'              => 'nullable|string|max:500',
            'rutas'              => 'nullable|array',
            'rutas.*'            => 'exists:rutas,id',
        ], [
            'placa.required'  => 'La placa es obligatoria.',
            'placa.unique'    => 'Esta placa ya está registrada.',
            'estado.required' => 'El estado es obligatorio.',
        ]);
    }

    private function datosFormulario(int $empresaId): array
    {
        $conductores = Conductor::where('empresa_id', $empresaId)
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        $propietarios = Propietario::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $rutas = Ruta::where('empresa_id', $empresaId)
            ->where('estado', 'activa')
            ->orderBy('nombre')
            ->get();

        return [$conductores, $propietarios, $rutas];
    }

    private function verificarEmpresa(Vehiculo $vehiculo): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_if($vehiculo->empresa_id !== $user->empresa_id, 403, 'Acceso no autorizado.');
    }

    private function verificarRelaciones(array $data, int $empresaId): void
    {
        if (!empty($data['conductor_id'])) {
            $existe = Conductor::where('id', $data['conductor_id'])
                ->where('empresa_id', $empresaId)
                ->exists();
            abort_if(!$existe, 403, 'El conductor no pertenece a tu empresa.');
        }

        if (!empty($data['propietario_id'])) {
            $existe = Propietario::where('id', $data['propietario_id'])
                ->where('empresa_id', $empresaId)
                ->exists();
            abort_if(!$existe, 403, 'El propietario no pertenece a tu empresa.');
        }
    }
}