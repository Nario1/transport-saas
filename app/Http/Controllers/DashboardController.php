<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Conductor;
use App\Models\Vuelta;
use App\Models\Tributo;
use App\Models\Sancion;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user      = Auth::user();
        $empresaId = $user->empresa_id;
        $hoy       = today();

        // ── Indicadores principales ───────────────────────────────
        $autosActivos   = Vehiculo::where('empresa_id', $empresaId)->where('estado', 'activo')->count();
        $totalVehiculos = Vehiculo::where('empresa_id', $empresaId)->count();
        $totalConductores = Conductor::where('empresa_id', $empresaId)->where('estado', 'activo')->count();

        // ── Vueltas ───────────────────────────────────────────────
        $vueltasHoy  = Vuelta::where('empresa_id', $empresaId)->whereDate('fecha', $hoy)->count();
        $vueltasAyer = Vuelta::where('empresa_id', $empresaId)->whereDate('fecha', $hoy->copy()->subDay())->count();

        // ── Tributos del día ──────────────────────────────────────
        $tributosHoy      = Tributo::where('empresa_id', $empresaId)->whereDate('fecha', $hoy);
        $totalCobradoHoy  = (clone $tributosHoy)->where('estado', 'pagado')->sum('monto');
        $pagadosHoy       = (clone $tributosHoy)->where('estado', 'pagado')->count();
        $pendientesHoy    = (clone $tributosHoy)->where('estado', 'pendiente')->count();
        $montoPendienteHoy= (clone $tributosHoy)->where('estado', 'pendiente')->sum('monto');

        // ── Deuda acumulada ───────────────────────────────────────
        $deudaTotal    = Tributo::where('empresa_id', $empresaId)->where('estado', 'pendiente')->sum('monto');
        $autosConDeuda = Tributo::where('empresa_id', $empresaId)->where('estado', 'pendiente')
            ->distinct('vehiculo_id')->count('vehiculo_id');

        // ── Sanciones pendientes ──────────────────────────────────
        $sancionesPendientes = Sancion::where('empresa_id', $empresaId)->where('estado', 'pendiente')->count();
        $montoSanciones      = Sancion::where('empresa_id', $empresaId)->where('estado', 'pendiente')->sum('monto');

        // ── Documentos por vencer (15 días) ──────────────────────
        $limite = $hoy->copy()->addDays(15);
        $docsPorVencer = Vehiculo::where('empresa_id', $empresaId)
            ->where(fn($q) =>
                $q->whereBetween('soat_vence',         [$hoy, $limite])
                  ->orWhereBetween('rev_tecnica_vence', [$hoy, $limite])
            )->count();

        $docsVencidos = Vehiculo::where('empresa_id', $empresaId)
            ->where(fn($q) =>
                $q->where('soat_vence', '<', $hoy)
                  ->orWhere('rev_tecnica_vence', '<', $hoy)
            )->count();

        // ── Tabla operativa del día ───────────────────────────────
        $vehiculosHoy = Vehiculo::where('empresa_id', $empresaId)
            ->where('estado', 'activo')
            ->with([
                'conductor',
                'tributos' => fn($q) => $q->whereDate('fecha', $hoy),
            ])
            ->withCount([
                'vueltas as vueltas_hoy' => fn($q) => $q->whereDate('fecha', $hoy),
            ])
            ->orderBy('numero_flota')
            ->orderBy('placa')
            ->get();

        // ── Ingresos últimos 7 días ───────────────────────────────
        $ultimos7 = collect(range(6, 0))->map(function ($i) use ($hoy, $empresaId) {
            $fecha = $hoy->copy()->subDays($i);
            return [
                'label' => $fecha->locale('es')->isoFormat('dd D'),
                'monto' => Tributo::where('empresa_id', $empresaId)
                    ->whereDate('fecha', $fecha)
                    ->where('estado', 'pagado')
                    ->sum('monto'),
            ];
        });

        // ── Tributos pendientes para cobrar rápido ────────────────
        $pendientesCobrar = Tributo::where('empresa_id', $empresaId)
            ->whereDate('fecha', $hoy)
            ->where('estado', 'pendiente')
            ->with(['vehiculo', 'conductor'])
            ->get();

        // ── Licencias por vencer (30 días) ────────────────────────
        $licenciasPorVencer = Conductor::where('empresa_id', $empresaId)
            ->where('estado', 'activo')
            ->whereBetween('licencia_vence', [$hoy, $hoy->copy()->addDays(30)])
            ->count();

        return view('admin.dashboard.index', compact(
            'autosActivos',
            'totalVehiculos',
            'totalConductores',
            'vueltasHoy',
            'vueltasAyer',
            'totalCobradoHoy',
            'pagadosHoy',
            'pendientesHoy',
            'montoPendienteHoy',
            'deudaTotal',
            'autosConDeuda',
            'sancionesPendientes',
            'montoSanciones',
            'docsPorVencer',
            'docsVencidos',
            'vehiculosHoy',
            'ultimos7',
            'pendientesCobrar',
            'licenciasPorVencer'
        ));
    }
}   