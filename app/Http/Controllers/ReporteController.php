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
                SUM(CASE WHEN estado = 'pagado'    THEN 1 ELSE 0 END) as pagados,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'exonerado' THEN 1 ELSE 0 END) as exonerados,
                SUM(CASE WHEN estado = 'pagado'    THEN monto ELSE 0 END) as total_cobrado,
                SUM(CASE WHEN estado = 'pendiente' THEN monto ELSE 0 END) as monto_pendiente
            ")
            ->groupBy('fecha')
            ->orderByDesc('fecha')
            ->get();

        // Detalle de todos los registros en el rango (para la tabla detallada)
        $detalle = Tributo::where('empresa_id', $user->empresa_id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->whereIn('estado', ['pagado', 'exonerado']) // Solo los procesados
            ->with(['vehiculo', 'conductor', 'cobrador.roles'])
            ->orderByDesc('fecha')
            ->orderByDesc('cobrado_at')
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
            'cobrado'    => $porDia->sum('total_cobrado'),
            'pendiente'  => $porDia->sum('monto_pendiente'),
            'exonerados' => $porDia->sum('exonerados'),
            'dias'       => $porDia->count(),
        ];

        return view('admin.reportes.tributos', compact(
            'porDia', 'porMetodo', 'conDeuda', 'detalle', 'totales', 'desde', 'hasta'
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

        // Detalle individual de vueltas
        $detalle = Vuelta::where('empresa_id', $user->empresa_id)
            ->whereBetween('fecha', [$desde, $hasta])
            ->with(['vehiculo', 'conductor', 'ruta'])
            ->orderByDesc('fecha')
            ->orderByDesc('hora_salida')
            ->get();

        $totales = [
            'vueltas'   => $porDia->sum('total_vueltas'),
            'vehiculos' => $porVehiculo->count(),
            'dias'      => $porDia->count(),
        ];

        return view('admin.reportes.vueltas', compact(
            'porDia', 'porVehiculo', 'porRuta', 'detalle', 'totales', 'desde', 'hasta'
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
    public function documentos(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $hoy = today();
        
        $tipoFiltro = $request->input('tipo_filtro', 'todo');
        $docFiltro = $request->input('documento_tipo', 'todos');
        $selectedMes = $request->input('mes', $hoy->format('Y-m'));
        $search = $request->input('search');

        // Determinar Rango de Fechas
        if ($tipoFiltro === 'todo') {
            $desde = $hoy->copy()->subYears(10);
            $hasta = $hoy->copy()->addYears(10);
        } elseif ($tipoFiltro === 'mes') {
            $desde = \Carbon\Carbon::parse($selectedMes . '-01')->startOfMonth();
            $hasta = $desde->copy()->endOfMonth();
        } elseif ($tipoFiltro === 'urgente') {
            $desde = $hoy->copy()->subYears(5); // Incluir vencidos antiguos
            $hasta = $hoy->copy()->addDays(7);
        } elseif ($tipoFiltro === 'proximos_15') {
            $desde = $hoy->copy();
            $hasta = $hoy->copy()->addDays(15);
        } elseif ($tipoFiltro === 'proximos_30') {
            $desde = $hoy->copy();
            $hasta = $hoy->copy()->addDays(30);
        } elseif ($tipoFiltro === 'vencidos') {
            $desde = $hoy->copy()->subYears(10);
            $hasta = $hoy->copy()->subDay();
        } else {
            $desde = $hoy->copy()->startOfMonth();
            $hasta = $hoy->copy()->endOfMonth();
        }

        // Query Base para Vehículos
        $vQuery = Vehiculo::where('empresa_id', $user->empresa_id)->with('conductor');

        // Query Base para Conductores
        $cQuery = Conductor::where('empresa_id', $user->empresa_id)->where('estado', 'activo');

        if ($search) {
            $vQuery->where(fn($q) => 
                $q->where('placa', 'like', "%$search%")
                  ->orWhere('numero_flota', 'like', "%$search%")
                  ->orWhereHas('conductor', fn($sq) => $sq->where('nombre', 'like', "%$search%"))
            );
            $cQuery->where('nombre', 'like', "%$search%");
        }

        $vencimientos = $vQuery->get();
        $licenciasData = $cQuery->get();

        $alertas = collect();

        foreach ($vencimientos as $v) {
            if (in_array($docFiltro, ['todos', 'soat']) && $v->soat_vence >= $desde->toDateString() && $v->soat_vence <= $hasta->toDateString()) {
                $alertas->push((object)[
                    'entidad'   => "Unidad #{$v->numero_flota} ({$v->placa})",
                    'conductor' => $v->conductor->nombre ?? 'Sin Conductor',
                    'documento' => 'SOAT',
                    'fecha'     => \Carbon\Carbon::parse($v->soat_vence),
                ]);
            }
            if (in_array($docFiltro, ['todos', 'revision']) && $v->rev_tecnica_vence >= $desde->toDateString() && $v->rev_tecnica_vence <= $hasta->toDateString()) {
                $alertas->push((object)[
                    'entidad'   => "Unidad #{$v->numero_flota} ({$v->placa})",
                    'conductor' => $v->conductor->nombre ?? 'Sin Conductor',
                    'documento' => 'Revisión Técnica',
                    'fecha'     => \Carbon\Carbon::parse($v->rev_tecnica_vence),
                ]);
            }
            if (in_array($docFiltro, ['todos', 'tarjeta']) && $v->tarjeta_prop_vence >= $desde->toDateString() && $v->tarjeta_prop_vence <= $hasta->toDateString()) {
                $alertas->push((object)[
                    'entidad'   => "Unidad #{$v->numero_flota} ({$v->placa})",
                    'conductor' => $v->conductor->nombre ?? 'Sin Conductor',
                    'documento' => 'Tarjeta de Propiedad',
                    'fecha'     => \Carbon\Carbon::parse($v->tarjeta_prop_vence),
                ]);
            }
        }

        foreach ($licenciasData as $l) {
            if (in_array($docFiltro, ['todos', 'licencia']) && $l->licencia_vence >= $desde->toDateString() && $l->licencia_vence <= $hasta->toDateString()) {
                $alertas->push((object)[
                    'entidad'   => $l->nombre_completo,
                    'conductor' => $l->nombre_completo,
                    'documento' => 'Licencia de Conducir',
                    'fecha'     => \Carbon\Carbon::parse($l->licencia_vence),
                ]);
            }
        }

        $alertas = $alertas->sortBy('fecha')->values();

        // Agrupación para resumen estadístico
        $resumen = [
            'criticos' => $alertas->filter(fn($a) => $hoy->diffInDays($a->fecha, false) <= 7)->count(),
            'mes_actual' => $alertas->filter(fn($a) => $a->fecha->isCurrentMonth())->count(),
            'vencidos' => $alertas->filter(fn($a) => $a->fecha->isPast() && !$a->fecha->isToday())->count(),
        ];

        return view('admin.reportes.documentos', compact(
            'alertas', 'desde', 'hasta', 'hoy', 'selectedMes', 'tipoFiltro', 'docFiltro', 'search', 'resumen'
        ));
    }

    // ── Reporte de Deuda por Vehículo ─────────────────────────────
    public function deudas(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        [$desde, $hasta] = $this->rango($request);

        $deudas = Tributo::where('empresa_id', $user->empresa_id)
            ->where('estado', 'pendiente')
            ->whereBetween('fecha', [$desde, $hasta])
            ->with(['vehiculo.conductor', 'vehiculo.propietario'])
            ->selectRaw('vehiculo_id, SUM(monto) as total_deuda, COUNT(*) as dias_deuda, MIN(fecha) as desde_fecha')
            ->groupBy('vehiculo_id')
            ->orderByDesc('total_deuda')
            ->get();

        $totalDeuda = $deudas->sum('total_deuda');

        return view('admin.reportes.deudas', compact('deudas', 'totalDeuda', 'desde', 'hasta'));
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function rango(Request $request): array
    {
        $desde = $request->filled('desde')
            ? \Carbon\Carbon::parse($request->input('desde'))->startOfDay()
            : today()->startOfDay();

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