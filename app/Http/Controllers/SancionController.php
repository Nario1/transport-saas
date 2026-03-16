<?php

namespace App\Http\Controllers;

use App\Models\Sancion;
use App\Models\Vehiculo;
use App\Models\Conductor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SancionController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pendientes = Sancion::where('empresa_id', $user->empresa_id)
            ->where('estado', 'pendiente')
            ->with(['vehiculo', 'conductor', 'registrador'])
            ->latest('fecha')
            ->get();

        $pagadas = Sancion::where('empresa_id', $user->empresa_id)
            ->where('estado', 'pagado')
            ->with(['vehiculo', 'conductor', 'cobrador'])
            ->latest('fecha')
            ->paginate(20);

        $resumen = [
            'total_pendiente' => $pendientes->sum('monto'),
            'cantidad_pendiente' => $pendientes->count(),
            'total_cobrado_mes' => Sancion::where('empresa_id', $user->empresa_id)
                ->where('estado', 'pagado')
                ->whereMonth('fecha', now()->month)
                ->sum('monto'),
        ];

        return view('admin.sanciones.index', compact('pendientes', 'pagadas', 'resumen'));
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

        $fechaHoy = today()->toDateString();

        return view('admin.sanciones.create', compact('vehiculos', 'conductores', 'fechaHoy'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'vehiculo_id'  => 'required|exists:vehiculos,id',
            'conductor_id' => 'nullable|exists:conductores,id',
            'fecha'        => 'required|date',
            'motivo'       => 'required|string|max:200',
            'descripcion'  => 'nullable|string|max:500',
            'monto'        => 'required|numeric|min:0|max:9999',
        ], [
            'vehiculo_id.required' => 'El vehículo es obligatorio.',
            'fecha.required'       => 'La fecha es obligatoria.',
            'motivo.required'      => 'El motivo es obligatorio.',
            'monto.required'       => 'El monto es obligatorio.',
        ]);

        $this->verificarVehiculo($data['vehiculo_id'], $user->empresa_id);

        if (!empty($data['conductor_id'])) {
            $this->verificarConductor($data['conductor_id'], $user->empresa_id);
        }

        $data['empresa_id']     = $user->empresa_id;
        $data['registrado_por'] = $user->id;
        $data['estado']         = 'pendiente';

        Sancion::create($data);

        return redirect()->route('sanciones.index')
            ->with('success', 'Sanción registrada correctamente.');
    }

    public function pagar(Sancion $sancion)
    {
        $this->verificarEmpresa($sancion);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = request()->validate([
            'metodo_pago' => 'required|in:efectivo,yape,plin,transferencia',
        ], [
            'metodo_pago.required' => 'El método de pago es obligatorio.',
        ]);

        $sancion->update([
            'estado'      => 'pagado',
            'cobrado_por' => $user->id,
            'cobrado_at'  => now(),
        ]);

        return back()->with('success', "Sanción de {$sancion->vehiculo->placa} marcada como pagada.");
    }

    public function destroy(Sancion $sancion)
    {
        $this->verificarEmpresa($sancion);

        if ($sancion->estado === 'pagado') {
            return back()->with('error', 'No se puede eliminar una sanción que ya fue pagada.');
        }

        $sancion->delete();

        return back()->with('success', 'Sanción eliminada correctamente.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function verificarEmpresa(Sancion $sancion): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_if($sancion->empresa_id !== $user->empresa_id, 403, 'Acceso no autorizado.');
    }

    private function verificarVehiculo(int $vehiculoId, int $empresaId): void
    {
        $existe = Vehiculo::where('id', $vehiculoId)
            ->where('empresa_id', $empresaId)
            ->exists();

        abort_if(!$existe, 403, 'El vehículo no pertenece a tu empresa.');
    }

    private function verificarConductor(int $conductorId, int $empresaId): void
    {
        $existe = Conductor::where('id', $conductorId)
            ->where('empresa_id', $empresaId)
            ->exists();

        abort_if(!$existe, 403, 'El conductor no pertenece a tu empresa.');
    }
}