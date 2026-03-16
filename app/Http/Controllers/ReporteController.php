<?php

namespace App\Http\Controllers;

use App\Models\Tributo;
use App\Models\Vuelta;
use App\Models\Sancion;
use App\Models\Vehiculo;
use App\Models\Conductor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user      = Auth::user();
        $empresaId = $user->empresa_id;
        $hoy       = today();

        // Resumen general para la página de reportes
        $resumen = [
            'tributos_mes'    => Tributo::where('empresa_id', $empresaId)
                ->where('estado', 'pagado')
                ->whereMonth('fecha', $hoy->month)
                ->whereYear('fecha', $hoy->year)
                ->sum('monto'),
            'vueltas_mes'     => Vuelta::where('empresa_id', $empresaId)
                ->whereMonth('fecha', $hoy->month)
                ->whereYear('fecha', $hoy->year)
                ->count(),
            'sanciones_mes'   => Sancion::where('empresa_id', $empresaId)
                ->whereMonth('fecha', $hoy->month)
                ->whereYear('fecha', $hoy->year)
                ->sum('monto'),
            'deuda_total'     => Tributo::where('empresa_id', $empresaId)
                ->where('estado', 'pendiente')
                ->sum('monto'),
        ];

        return view('admin.reportes.index', compact('resumen'));
    }

    // ── Reporte de Tributos ───────────────────────────────────────
    public function tributos(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        [$desde, $hasta] = $this->rango($request);

        // Resumen por día
        $porDia = Tributo::where('empresa_id', $user->empresa_id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->selectRaw("
                fecha,
                COUNT(*) as total_autos,
                SUM(CASE WHEN estado = 'pagado'   THEN 1 ELSE 0 END) as pagados,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'pagado'   THEN monto ELSE 0 END) as total_cobrado,
                SUM(CASE WHEN estado = 'pendiente' THEN monto ELSE 0 END) as monto_pendiente
            ")
            ->groupBy('fecha')
            ->orderByDesc('fecha')
            ->get();

        // Resumen por método de pago
        $porMetodo = Tributo::where('empresa_id', $user->empresa_id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->where('estado', 'pagado')
            ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->get();

        // Vehículos con más deuda
        $conDeuda = Tributo::where('empresa_id', $user->empresa_id)
            ->where('estado', 'pendiente')
            ->with(['vehiculo', 'conductor'])
            ->selectRaw('vehiculo_id, conductor_id, SUM(monto) as total_deuda, COUNT(*) as dias_deuda')
            ->groupBy('vehiculo_id', 'conductor_id')
            ->orderByDesc('total_deuda')
            ->limit(10)
            ->get();

        $totales = [
            'cobrado'   => $porDia->sum('total_cobrado'),
            'pendiente' => $porDia->sum('monto_pendiente'),
            'dias'      => $porDia->count(),
        ];

        return view('admin.reportes.tributos', compact(
            'porDia', 'porMetodo', 'conDeuda', 'totales', 'desde', 'hasta'
        ));
    }

    // ── Reporte de Vueltas ────────────────────────────────────────
    public function vueltas(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        [$desde, $hasta] = $this->rango($request);

        // Vueltas por día
        $porDia = Vuelta::where('empresa_id', $user->empresa_id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->selectRaw('fecha, COUNT(*) as total_vueltas, COUNT(DISTINCT vehiculo_id) as vehiculos')
            ->groupBy('fecha')
            ->orderByDesc('fecha')
            ->get();

        // Vueltas por vehículo en el rango
        $porVehiculo = Vuelta::where('empresa_id', $user->empresa_id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->with(['vehiculo', 'conductor'])
            ->selectRaw('vehiculo_id, conductor_id, COUNT(*) as total_vueltas')
            ->groupBy('vehiculo_id', 'conductor_id')
            ->orderByDesc('total_vueltas')
            ->get();

        // Vueltas por ruta
        $porRuta = Vuelta::where('empresa_id', $user->empresa_id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->whereNotNull('ruta_id')
            ->with('ruta')
            ->selectRaw('ruta_id, COUNT(*) as total_vueltas')
            ->groupBy('ruta_id')
            ->orderByDesc('total_vueltas')
            ->get();

        $totales = [
            'vueltas'   => $porDia->sum('total_vueltas'),
            'vehiculos' => $porVehiculo->count(),
            'dias'      => $porDia->count(),
        ];

        return view('admin.reportes.vueltas', compact(
            'porDia', 'porVehiculo', 'porRuta', 'totales', 'desde', 'hasta'
        ));
    }

    // ── Reporte de Sanciones ──────────────────────────────────────
    public function sanciones(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        [$desde, $hasta] = $this->rango($request);

        $sanciones = Sancion::where('empresa_id', $user->empresa_id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->with(['vehiculo', 'conductor', 'registrador'])
            ->orderByDesc('fecha')
            ->get();

        // Resumen por estado
        $porEstado = [
            'pendiente' => $sanciones->where('estado', 'pendiente')->sum('monto'),
            'pagado'    => $sanciones->where('estado', 'pagado')->sum('monto'),
            'cantidad_pendiente' => $sanciones->where('estado', 'pendiente')->count(),
            'cantidad_pagada'    => $sanciones->where('estado', 'pagado')->count(),
        ];

        // Conductores con más sanciones
        $porConductor = $sanciones->groupBy('conductor_id')->map(fn($s) => [
            'conductor'  => $s->first()->conductor,
            'cantidad'   => $s->count(),
            'total'      => $s->sum('monto'),
        ])->sortByDesc('cantidad')->take(10);

        return view('admin.reportes.sanciones', compact(
            'sanciones', 'porEstado', 'porConductor', 'desde', 'hasta'
        ));
    }

    // ── Reporte de Documentos ─────────────────────────────────────
    public function documentos()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $hoy    = today();
        $limite = $hoy->copy()->addDays(30);

        // Documentos vencidos
        $vencidos = Vehiculo::where('empresa_id', $user->empresa_id)
            ->where(fn($q) =>
                $q->where('soat_vence', '<', $hoy)
                  ->orWhere('rev_tecnica_vence', '<', $hoy)
            )
            ->with('conductor')
            ->orderBy('soat_vence')
            ->get();

        // Documentos por vencer en 30 días
        $porVencer = Vehiculo::where('empresa_id', $user->empresa_id)
            ->where(fn($q) =>
                $q->whereBetween('soat_vence',         [$hoy, $limite])
                  ->orWhereBetween('rev_tecnica_vence', [$hoy, $limite])
            )
            ->with('conductor')
            ->orderBy('soat_vence')
            ->get();

        // Licencias de conductores por vencer
        $licencias = Conductor::where('empresa_id', $user->empresa_id)
            ->where('estado', 'activo')
            ->where(fn($q) =>
                $q->where('licencia_vence', '<', $hoy)
                  ->orWhereBetween('licencia_vence', [$hoy, $limite])
            )
            ->orderBy('licencia_vence')
            ->get();

        return view('admin.reportes.documentos', compact(
            'vencidos', 'porVencer', 'licencias', 'hoy', 'limite'
        ));
    }

    // ── Reporte de Deuda por Vehículo ─────────────────────────────
    public function deudas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $deudas = Tributo::where('empresa_id', $user->empresa_id)
            ->where('estado', 'pendiente')
            ->with(['vehiculo.conductor', 'vehiculo.propietario'])
            ->selectRaw('vehiculo_id, SUM(monto) as total_deuda, COUNT(*) as dias_deuda, MIN(fecha) as desde_fecha')
            ->groupBy('vehiculo_id')
            ->orderByDesc('total_deuda')
            ->get();

        $totalDeuda = $deudas->sum('total_deuda');

        return view('admin.reportes.deudas', compact('deudas', 'totalDeuda'));
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function rango(Request $request): array
    {
        $desde = $request->filled('desde')
            ? \Carbon\Carbon::parse($request->input('desde'))->startOfDay()
            : today()->startOfMonth();

        $hasta = $request->filled('hasta')
            ? \Carbon\Carbon::parse($request->input('hasta'))->endOfDay()
            : today()->endOfDay();

        // Asegurar que desde no sea mayor que hasta
        if ($desde->gt($hasta)) {
            $desde = $hasta->copy()->startOfMonth();
        }

        return [$desde, $hasta];
    }
}