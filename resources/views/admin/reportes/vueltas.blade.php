@extends('layouts.admin')

@section('back_url', route('reportes.index'))

@php
    $pageTitle = 'Productividad de Vueltas';
    $pageSubtitle = 'Análisis de frecuencias y recorridos operativos';
@endphp

@section('content')
<div style="display: grid; gap: 24px;">

    {{-- 1. FILTROS --}}
    <div class="card no-print">
        <form action="{{ route('reportes.vueltas') }}" method="GET" class="card-body g-filters">
            <div class="field">
                <label>Desde:</label>
                <input type="date" name="desde" value="{{ $desde->toDateString() }}">
            </div>
            <div class="field">
                <label>Hasta:</label>
                <input type="date" name="hasta" value="{{ $hasta->toDateString() }}">
            </div>
            <div class="field" style="border-left: 1px solid var(--border); padding-left: 20px;">
                <label>Día Específico:</label>
                <input type="date" onchange="if(this.value){ document.getElementsByName('desde')[0].value=this.value; document.getElementsByName('hasta')[0].value=this.value; this.form.submit(); }">
            </div>
            <div class="flex-h" style="gap: 10px; margin-top: auto;">
                <button type="submit" class="btn-primary" style="height: 48px; padding: 0 25px;">📊 ANALIZAR</button>
                <button type="button" onclick="window.print()" class="btn-secondary" style="height: 48px; border-radius: 12px; width: 48px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-print"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- 2. HISTORIAL DETALLADO --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                Detalle General de Vueltas Realizadas
                <small style="color: var(--text3); font-weight: 400; font-size: 13px;">({{ $desde->format('d/m/Y') }} - {{ $hasta->format('d/m/Y') }})</small>
            </div>
        </div>
        <div class="tbl-wrap">
            <table class="tbl tbl-modern">
                <thead>
                    <tr>
                        <th>Fecha / Operación</th>
                        <th>Vehículo</th>
                        <th>Conductor</th>
                        <th>Ruta Operativa</th>
                        <th>Duración</th>
                        <th style="text-align: center;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detalle as $reg)
                        <tr>
                            <td>
                                <div style="font-weight: 700;">{{ $reg->fecha->format('d/m/Y') }}</div>
                                <div class="mono" style="font-size: 11px; color: var(--text2);">
                                    <i class="fa-solid fa-clock"></i> 
                                    {{ $reg->hora_salida ? \Carbon\Carbon::parse($reg->hora_salida)->format('h:i A') : '--:--' }}
                                    ➔
                                    {{ $reg->hora_llegada ? \Carbon\Carbon::parse($reg->hora_llegada)->format('h:i A') : '--:--' }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 800; color: var(--accent);">#{{ $reg->vehiculo?->numero_flota }}</div>
                                <div class="mono" style="font-size: 10px;">{{ $reg->vehiculo?->placa }}</div>
                            </td>
                            <td><div style="font-weight: 600;">{{ $reg->conductor?->nombre_completo ?? '---' }}</div></td>
                            <td>
                                <div style="font-size: 13px;">{{ $reg->ruta?->nombre ?? 'Sin Ruta' }}</div>
                                <div style="font-size: 10px; color: var(--text3);">{{ $reg->ruta?->origen }} - {{ $reg->ruta?->destino }}</div>
                            </td>
                            <td>
                                @if($reg->hora_llegada)
                                    @php
                                        $sec = \Carbon\Carbon::parse($reg->hora_salida)->diffInSeconds(\Carbon\Carbon::parse($reg->hora_llegada));
                                        if ($sec < 60) $dur = "$sec segundos";
                                        elseif ($sec < 3600) $dur = floor($sec/60) . " minutos";
                                        else $dur = floor($sec/3600) . "h " . (floor($sec/60)%60) . "min";
                                    @endphp
                                    <span class="mono" style="font-weight: 700;">{{ $dur }}</span>
                                @else
                                    <span class="pill green" style="font-size: 10px;">EN RUTA</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="pill {{ $reg->estado === 'activa' ? 'green' : ($reg->estado === 'completada' ? 'blue' : 'red') }}">
                                    {{ strtoupper($reg->estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center; padding: 40px;">No hay registros detallados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
