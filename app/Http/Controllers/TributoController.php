<?php

namespace App\Http\Controllers;

use App\Models\Tributo;
use App\Models\Vehiculo;
use App\Models\Conductor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TributoController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $fecha = request('fecha', today()->toDateString());

        // Tributos del día seleccionado
        $pagados = Tributo::where('empresa_id', $user->empresa_id)
            ->whereDate('fecha', $fecha)
            ->where('estado', 'pagado')
            ->with(['vehiculo', 'conductor', 'cobrador'])
            ->get();

        $pendientes = Tributo::where('empresa_id', $user->empresa_id)
            ->whereDate('fecha', $fecha)
            ->where('estado', 'pendiente')
            ->with(['vehiculo', 'conductor'])
            ->get();

        // Resumen del día
        $resumen = [
            'total_cobrado'   => $pagados->sum('monto'),
            'total_pendiente' => $pendientes->sum('monto'),
            'autos_pagaron'   => $pagados->count(),
            'autos_pendientes'=> $pendientes->count(),
        ];

        // Deuda acumulada por vehículo (todos los pendientes históricos)
        $deudas = Tributo::where('empresa_id', $user->empresa_id)
            ->where('estado', 'pendiente')
            ->with(['vehiculo', 'conductor'])
            ->selectRaw('vehiculo_id, conductor_id, SUM(monto) as total_deuda, COUNT(*) as dias_deuda')
            ->groupBy('vehiculo_id', 'conductor_id')
            ->get();

        return view('admin.tributos.index', compact(
            'pagados', 'pendientes', 'resumen', 'deudas', 'fecha'
        ));
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

        $fechaHoy = today()->toDateString();

        return view('admin.tributos.create', compact('vehiculos', 'fechaHoy'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'vehiculo_id'   => 'required|exists:vehiculos,id',
            'conductor_id'  => 'nullable|exists:conductores,id',
            'fecha'         => 'required|date',
            'monto'         => 'required|numeric|min:0|max:9999',
            'metodo_pago'   => 'required|in:efectivo,yape,plin,transferencia',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'vehiculo_id.required' => 'El vehículo es obligatorio.',
            'fecha.required'       => 'La fecha es obligatoria.',
            'monto.required'       => 'El monto es obligatorio.',
            'metodo_pago.required' => 'El método de pago es obligatorio.',
        ]);

        $this->verificarVehiculo($data['vehiculo_id'], $user->empresa_id);

        Tributo::updateOrCreate(
            [
                'vehiculo_id' => $data['vehiculo_id'],
                'fecha'       => $data['fecha'],
            ],
            [
                'empresa_id'    => $user->empresa_id,
                'conductor_id'  => $data['conductor_id'],
                'monto'         => $data['monto'],
                'metodo_pago'   => $data['metodo_pago'],
                'estado'        => 'pagado',
                'cobrado_por'   => $user->id,
                'cobrado_at'    => now(),
                'observaciones' => $data['observaciones'] ?? null,
            ]
        );

        return redirect()->route('tributos.index')
            ->with('success', 'Tributo registrado correctamente.');
    }

    // Cobro rápido desde el listado (botón cobrar)
    public function cobrar(Tributo $tributo)
    {
        $this->verificarEmpresa($tributo);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $metodo = request()->validate([
            'metodo_pago' => 'required|in:efectivo,yape,plin,transferencia',
        ]);

        $tributo->update([
            'estado'      => 'pagado',
            'metodo_pago' => $metodo['metodo_pago'],
            'cobrado_por' => $user->id,
            'cobrado_at'  => now(),
        ]);

        return back()->with('success', "Tributo de {$tributo->vehiculo->placa} cobrado correctamente.");
    }

    // Generar tributos pendientes del día para todos los vehículos activos
    public function generarDelDia()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $vehiculos = Vehiculo::where('empresa_id', $user->empresa_id)
            ->where('estado', 'activo')
            ->get();

        $montoDiario = $user->empresa->tributo_diario ?? 24.00;
        $generados   = 0;

        foreach ($vehiculos as $vehiculo) {
            $creado = Tributo::firstOrCreate(
                [
                    'vehiculo_id' => $vehiculo->id,
                    'fecha'       => today(),
                ],
                [
                    'empresa_id'   => $user->empresa_id,
                    'conductor_id' => $vehiculo->conductor_id,
                    'monto'        => $montoDiario,
                    'estado'       => 'pendiente',
                ]
            );

            if ($creado->wasRecentlyCreated) {
                $generados++;
            }
        }

        return back()->with('success', "Se generaron {$generados} tributos pendientes para hoy.");
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function verificarEmpresa(Tributo $tributo): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_if($tributo->empresa_id !== $user->empresa_id, 403, 'Acceso no autorizado.');
    }

    private function verificarVehiculo(int $vehiculoId, int $empresaId): void
    {
        $existe = Vehiculo::where('id', $vehiculoId)
            ->where('empresa_id', $empresaId)
            ->exists();

        abort_if(!$existe, 403, 'El vehículo no pertenece a tu empresa.');
    }
}