@extends('layouts.admin')

@php
    $pageTitle = 'Control de Vueltas';
    $pageSubtitle = 'Registro y monitoreo de recorridos operativos';
@endphp

@section('content')
    <div class="panel">
        
        {{-- 1. INDICADORES --}}
        <div class="stats-row g-3">
            <div class="stat blue">
                <div class="stat-label">Vueltas Totales</div>
                <div class="stat-val">{{ $resumen['total'] }}</div>
                <div class="stat-sub">Servicios registrados para el {{ \Carbon\Carbon::parse($fecha)->format('d/m') }}</div>
                <span class="stat-icon"><i class="fa-solid fa-arrows-rotate"></i></span>
            </div>
            
            <div class="stat green">
                <div class="stat-label">Unidades en Ruta</div>
                <div class="stat-val">{{ $resumen['vehiculos'] }}</div>
                <div class="stat-sub">Vehículos con actividad hoy</div>
                <span class="stat-icon"><i class="fa-solid fa-bus"></i></span>
            </div>

            <div class="stat gold">
                <div class="stat-label">Fuerza Laboral</div>
                <div class="stat-val">{{ $resumen['conductores'] }}</div>
                <div class="stat-sub">Conductores en operación</div>
                <span class="stat-icon"><i class="fa-solid fa-user-tie"></i></span>
            </div>
        </div>

        {{-- 2. ACCIONES Y FILTROS --}}
        <div class="flex-between gap-24">
            <div class="card" style="flex: 1;">
                <form method="GET" action="{{ route('vueltas.index') }}" class="card-body g-filters">
                    <div class="field" style="flex: 1;">
                        <label>Fecha de Operación</label>
                        <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()" style="font-weight: 800; font-size: 15px;">
                    </div>
                    <div class="flex-h">
                        <a href="{{ route('vueltas.en-vivo') }}" class="btn-primary" style="height: 48px; background: var(--green); white-space: nowrap;">
                            <i class="fa-solid fa-satellite-dish"></i> PANEL EN VIVO
                        </a>
                    </div>
                </form>
            </div>
            
            <a href="{{ route('vueltas.create') }}" class="btn-primary" style="padding: 0 32px; height: 80px; border-radius: 20px; font-weight: 800; background: var(--sidebar);">
                <i class="fa-solid fa-plus-circle"></i> REGISTRAR VUELTA
            </a>
        </div>

        {{-- 3. TABLA DE VUELTAS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Historial de Despachos del Día</div>
            </div>
            <div class="tbl-wrap">
                <table class="tbl tbl-modern">
                    <thead>
                        <tr>
                            <th>Unidad / Conductor</th>
                            <th>Ruta Operativa</th>
                            <th>Salida</th>
                            <th>Llegada</th>
                            <th>Duracion</th>
                            <th class="col-status">Estado</th>
                            <th>Geoloc.</th>
                            <th class="col-actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vueltas as $vuelta)
                            <tr>
                                <td>
                                    <span class="text-main">Unidad #{{ $vuelta->vehiculo?->numero_flota }}</span>
                                    <span class="text-sub">{{ $vuelta->conductor?->nombre_completo }} • {{ $vuelta->vehiculo?->placa }}</span>
                                </td>
                                <td>
                                    <span class="text-main">{{ $vuelta->ruta?->nombre ?? 'Sin Ruta' }}</span>
                                    <span class="text-sub">{{ $vuelta->ruta?->origen }} » {{ $vuelta->ruta?->destino }}</span>
                                </td>
                                <td class="mono" style="font-weight: 700; font-size: 14px;">
                                    {{ $vuelta->hora_salida ? \Carbon\Carbon::parse($vuelta->hora_salida)->format('h:i A') : '--:--' }}
                                </td>
                                <td class="mono" style="font-weight: 700; font-size: 14px;">
                                    {{ $vuelta->hora_llegada ? \Carbon\Carbon::parse($vuelta->hora_llegada)->format('h:i A') : '--:--' }}
                                </td>
                                <td>
                                    @if($vuelta->hora_llegada)
                                        @php
                                            $sec = \Carbon\Carbon::parse($vuelta->hora_salida)->diffInSeconds(\Carbon\Carbon::parse($vuelta->hora_llegada));
                                            if ($sec < 60) $dur = "$sec segundos";
                                            elseif ($sec < 3600) $dur = floor($sec/60) . " minutos";
                                            else $dur = floor($sec/3600) . "h " . (floor($sec/60)%60) . "min";
                                        @endphp
                                        <span class="pill gray" style="font-weight: 800; font-family: monospace;">
                                            {{ $dur }}
                                        </span>
                                    @else
                                        <span class="pill green" style="font-size: 10px; font-weight: 800;">EN RUTA</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="pill {{ $vuelta->estado === 'activa' ? 'green' : 'blue' }}" style="font-size: 10px; font-weight: 800;">
                                        {{ strtoupper($vuelta->estado) }}
                                    </span>
                                </td>
                                </td>
                                <td class="col-actions">
                                    <div class="flex-h" style="justify-content: flex-end; gap: 4px;">
                                        @if ($vuelta->estado === 'activa')
                                            <form action="{{ route('vueltas.completar', $vuelta) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="action-icon show-icon" style="background: var(--green-l); color: var(--green); border: none;" title="Terminar">
                                                    <i class="fa-solid fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('vueltas.destroy', $vuelta) }}" method="POST" onsubmit="return confirm('¿Eliminar registro?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon-submit" title="Eliminar">
                                                <i class="fa-solid fa-trash-can action-icon delete-icon"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 60px; color: var(--text3);">
                                    <i class="fa-solid fa-arrows-rotate" style="font-size: 40px; opacity: 0.1; display: block; margin-bottom: 15px;"></i>
                                    No hay vueltas registradas para esta fecha.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($vueltas->hasPages())
                <div class="pagination-wrapper" style="padding:20px; border-top:1px solid var(--border);">
                    {{ $vueltas->appends(['fecha' => $fecha])->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
