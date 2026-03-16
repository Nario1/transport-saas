@extends('layouts.admin')

@php
    $pageTitle = 'Panel de Control';
    $pageSubtitle = 'Resumen operativo de Empresa B - ' . today()->format('d/m/Y');
@endphp

@section('content')
    <div style="display: grid; gap: 25px;">

        {{-- 1. INDICADORES PRINCIPALES (TARJETAS) --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
            <div class="card" style="padding: 20px; border-left: 5px solid #10b981;">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase; font-weight: 700;">Recaudación
                    Hoy</div>
                <div style="font-size: 24px; font-weight: 800; color: #10b981;">S/ {{ number_format($totalCobradoHoy, 2) }}
                </div>
                <div style="font-size: 12px; color: var(--text3);">{{ $pagadosHoy }} pagos recibidos</div>
            </div>

            <div class="card" style="padding: 20px; border-left: 5px solid #ef4444;">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase; font-weight: 700;">Deuda
                    Acumulada</div>
                <div style="font-size: 24px; font-weight: 800; color: #ef4444;">S/ {{ number_format($deudaTotal, 2) }}</div>
                <div style="font-size: 12px; color: var(--text3);">{{ $autosConDeuda }} unidades pendientes</div>
            </div>

            <div class="card" style="padding: 20px; border-left: 5px solid var(--accent);">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase; font-weight: 700;">Flota Activa
                </div>
                <div style="font-size: 24px; font-weight: 800;">{{ $autosActivos }} / {{ $totalVehiculos }}</div>
                <div style="font-size: 12px; color: var(--text3);">{{ $totalConductores }} conductores hoy</div>
            </div>

            <div class="card" style="padding: 20px; border-left: 5px solid var(--gold);">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase; font-weight: 700;">Vueltas Hoy
                </div>
                <div style="font-size: 24px; font-weight: 800;">{{ $vueltasHoy }}</div>
                <div style="font-size: 12px; color: var(--text3);">Ayer: {{ $vueltasAyer }}</div>
            </div>
        </div>

        {{-- 2. GRÁFICO DE INGRESOS Y ALERTAS --}}
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 25px;">

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Ingresos de los últimos 7 días</div>
                </div>
                <div class="card-body">
                    <div
                        style="display: flex; align-items: flex-end; justify-content: space-between; height: 200px; padding-top: 20px;">
                        @foreach ($ultimos7 as $dia)
                            @php
                                $maxMonto = $ultimos7->max('monto') ?: 1;
                                $altura = ($dia['monto'] / $maxMonto) * 150;
                            @endphp
                            <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                <div style="font-size: 10px; font-weight: 700; margin-bottom: 5px;">
                                    S/{{ number_format($dia['monto'], 0) }}</div>
                                <div
                                    style="width: 70%; background: var(--accent); height: {{ $altura }}px; border-radius: 4px 4px 0 0; min-height: 2px;">
                                </div>
                                <div
                                    style="font-size: 10px; color: var(--text3); margin-top: 8px; text-transform: uppercase;">
                                    {{ $dia['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="display: grid; gap: 15px;">
                <div class="card" style="background: #fff7ed; border: 1px solid #fed7aa;">
                    <div class="card-body" style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 24px;">📄</div>
                        <div>
                            <div style="font-weight: 800; color: #9a3412;">Documentos Vencidos</div>
                            <div style="font-size: 20px; font-weight: 900;">{{ $docsVencidos }}</div>
                        </div>
                    </div>
                </div>
                <div class="card" style="background: #fef2f2; border: 1px solid #fecaca;">
                    <div class="card-body" style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 24px;">🚨</div>
                        <div>
                            <div style="font-weight: 800; color: #991b1b;">Sanciones Pendientes</div>
                            <div style="font-size: 20px; font-weight: 900;">{{ $sancionesPendientes }}</div>
                        </div>
                    </div>
                </div>
                <div class="card" style="background: #f0f9ff; border: 1px solid #bae6fd;">
                    <div class="card-body" style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 24px;">🪪</div>
                        <div>
                            <div style="font-weight: 800; color: #0369a1;">Licencias por vencer</div>
                            <div style="font-size: 13px; color: var(--text3);">Próximos 30 días:
                                <b>{{ $licenciasPorVencer }}</b></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. TABLA OPERATIVA EN TIEMPO REAL --}}
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="card-title">Monitoreo Operativo de Unidades</div>
                    <div style="font-size: 12px; color: var(--text3);">Vehículos activos y su estado de tributo hoy</div>
                </div>
                <a href="{{ route('tributos.index') }}" class="btn-secondary"
                    style="font-size: 11px; text-decoration:none;">Ver todos los tributos</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Padron</th>
                            <th>Placa</th>
                            <th>Conductor</th>
                            <th style="text-align: center;">Vueltas</th>
                            <th>Tributo Hoy</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehiculosHoy as $v)
                            @php $tributoHoy = $v->tributos->first(); @endphp
                            <tr>
                                <td><b style="color: var(--accent);">#{{ $v->numero_flota }}</b></td>
                                <td style="font-family: monospace; font-weight: 700;">{{ $v->placa }}</td>
                                <td>{{ $v->conductor->nombre ?? 'Sin asignar' }}</td>
                                <td style="text-align: center;">
                                    <span class="pill" style="background: #f1f5f9;">{{ $v->vueltas_hoy }} vueltas</span>
                                </td>
                                <td>
                                    @if ($tributoHoy)
                                        <span class="pill"
                                            style="background: {{ $tributoHoy->estado == 'pagado' ? '#dcfce7' : '#fee2e2' }}; color: {{ $tributoHoy->estado == 'pagado' ? '#166534' : '#991b1b' }};">
                                            {{ strtoupper($tributoHoy->estado) }}
                                        </span>
                                    @else
                                        <span style="font-size: 11px; color: var(--text3); font-style: italic;">No
                                            generado</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('vehiculos.show', $v->id) }}" style="text-decoration: none;">👁️</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
