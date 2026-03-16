<?php

namespace App\Http\Controllers;

use App\Models\Vuelta;
use App\Models\Vehiculo;
use App\Models\Conductor;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VueltaController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $fecha = request('fecha', today()->toDateString());

        $vueltas = Vuelta::where('empresa_id', $user->empresa_id)
            ->whereDate('fecha', $fecha)
            ->with(['vehiculo', 'conductor', 'ruta'])
            ->orderBy('numero_vuelta')
            ->orderBy('created_at')
            ->get();

        // Resumen del día
        $resumen = [
            'total'      => $vueltas->count(),
            'vehiculos'  => $vueltas->pluck('vehiculo_id')->unique()->count(),
            'conductores'=> $vueltas->pluck('conductor_id')->unique()->count(),
        ];

        return view('admin.vueltas.index', compact('vueltas', 'fecha', 'resumen'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $vehiculos = Vehiculo::where('empresa_id', $user->empresa_id)
            ->where('estado', 'activo')
            ->with('conductor')
            ->orderBy('numero_flota')
            ->orderBy('placa')
            ->get();

        $conductores = Conductor::where('empresa_id', $user->empresa_id)
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        $rutas = Ruta::where('empresa_id', $user->empresa_id)
            ->where('estado', 'activa')
            ->orderBy('nombre')
            ->get();

        $fechaHoy = today()->toDateString();

        return view('admin.vueltas.create', compact('vehiculos', 'conductores', 'rutas', 'fechaHoy'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'vehiculo_id'   => 'required|exists:vehiculos,id',
            'conductor_id'  => 'nullable|exists:conductores,id',
            'ruta_id'       => 'nullable|exists:rutas,id',
            'fecha'         => 'required|date',
            'numero_vuelta' => 'required|integer|min:1|max:99',
            'hora_salida'   => 'nullable|date_format:H:i',
            'hora_llegada'  => 'nullable|date_format:H:i',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'vehiculo_id.required'   => 'El vehículo es obligatorio.',
            'fecha.required'         => 'La fecha es obligatoria.',
            'numero_vuelta.required' => 'El número de vuelta es obligatorio.',
        ]);

        // Verificar que el vehículo pertenece a la empresa
        $this->verificarVehiculo($data['vehiculo_id'], $user->empresa_id);

        // Verificar que no exista ya esa vuelta para ese vehículo en ese día
        $existe = Vuelta::where('vehiculo_id', $data['vehiculo_id'])
            ->whereDate('fecha', $data['fecha'])
            ->where('numero_vuelta', $data['numero_vuelta'])
            ->exists();

        if ($existe) {
            return back()->withInput()
                ->with('error', "Ya existe la vuelta #{$data['numero_vuelta']} para este vehículo en esta fecha.");
        }

        $data['empresa_id'] = $user->empresa_id;
        $data['created_by'] = $user->id;

        Vuelta::create($data);

        return redirect()->route('vueltas.index')
            ->with('success', 'Vuelta registrada correctamente.');
    }

    public function destroy(Vuelta $vuelta)
    {
        $this->verificarEmpresa($vuelta);

        $vuelta->delete();

        return back()->with('success', 'Vuelta eliminada correctamente.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function verificarEmpresa(Vuelta $vuelta): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_if($vuelta->empresa_id !== $user->empresa_id, 403, 'Acceso no autorizado.');
    }

    private function verificarVehiculo(int $vehiculoId, int $empresaId): void
    {
        $existe = Vehiculo::where('id', $vehiculoId)
            ->where('empresa_id', $empresaId)
            ->exists();

        abort_if(!$existe, 403, 'El vehículo no pertenece a tu empresa.');
    }
}