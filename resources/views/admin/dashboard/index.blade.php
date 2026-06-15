@extends('layouts.admin')

@php
    $pageTitle = 'Dashboard';
    $pageSubtitle = 'Panel de Control Operativo - ' . (auth()->user()->empresa?->nombre ?? 'TransJunín');
@endphp

@section('content')
    <div class="panel">


        {{-- 2. INDICADORES CLAVE --}}
        <div class="stats-row g-4">
            <div class="stat blue">
                <div class="stat-label">Recaudación (Hoy)</div>
                <div class="stat-val">S/ {{ number_format($totalCobradoHoy, 2) }}</div>
                <div class="stat-sub">{{ $pagadosHoy }} cobros ejecutados</div>
                <span class="stat-icon"><i class="fa-solid fa-money-bill-trend-up"></i></span>
            </div>

            <div class="stat red">
                <div class="stat-label">Deuda Acumulada</div>
                <div class="stat-val">S/ {{ number_format($deudaTotal, 2) }}</div>
                <div class="stat-sub">{{ $autosConDeuda }} flotas con pagos pendientes</div>
                <span class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span>
            </div>

            <div class="stat">
                <div class="stat-label">Disponibilidad Flota</div>
                <div class="stat-val">{{ $autosActivos }} <small style="font-size: 14px; opacity: 0.5;">/
                        {{ $totalVehiculos }}</small></div>
                <div class="stat-sub">{{ $totalConductores }} operando hoy</div>
                <span class="stat-icon"><i class="fa-solid fa-bus"></i></span>
            </div>

            <div class="stat gold">
                <div class="stat-label">Control de Vueltas</div>
                <div class="stat-val">{{ $vueltasHoy }}</div>
                <div class="stat-sub">Rendimiento:
                    {{ $vueltasHoy > $vueltasAyer ? '⬆️ Al alza' : 'Ayer: ' . $vueltasAyer }}
                </div>
                <span class="stat-icon"><i class="fa-solid fa-rotate-right"></i></span>
            </div>
        </div>

        {{-- 3. SECCIÓN CENTRAL: GRÁFICOS Y MONITOREO --}}
        <style>
            .dashboard-layout {
                display: flex;
                flex-direction: column;
                gap: 24px;
            }
            @media (min-width: 1100px) {
                .dashboard-layout {
                    display: grid;
                    grid-template-columns: 1.6fr 1.4fr;
                }
            }
        </style>
        <div class="dashboard-layout">

            <div class="flex-v" style="gap: 24px;">

                {{-- Gráfico de Ingresos --}}
                <div class="card">
                    <div class="card-header flex-between">
                        <div class="card-title"><i class="fa-solid fa-chart-area"></i> Flujo de Ingresos
                            ({{ $rangoGrafico }})</div>
                        <div class="flex-h" style="gap: 8px;">
                            <a href="{{ route('dashboard', ['offset' => $offset + 1]) }}" class="btn-secondary btn-sm"
                                style="padding: 5px 10px;" title="Semana Anterior">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                            @if ($offset > 0)
                                <a href="{{ route('dashboard', ['offset' => $offset - 1]) }}" class="btn-secondary btn-sm"
                                    style="padding: 5px 10px;" title="Semana Siguiente">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            @else
                                <button class="btn-secondary btn-sm"
                                    style="padding: 5px 10px; opacity: 0.3; cursor: not-allowed;" disabled>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div
                            style="display: flex; align-items: flex-end; justify-content: space-between; height: 180px; padding-top: 30px;">
                            @foreach ($ultimos7 as $dia)
                                @php
                                    $maxMonto = $ultimos7->max('monto') ?: 1;
                                    $altura = ($dia['monto'] / $maxMonto) * 120;
                                @endphp
                                <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                    <div
                                        style="font-size: 10px; font-weight: 800; color: var(--accent); margin-bottom: 5px;">
                                        S/{{ number_format($dia['monto'], 0) }}
                                    </div>
                                    <div
                                        style="width: 50%; background: var(--accent); height: {{ $altura }}px; border-radius: 8px 8px 0 0; opacity: 0.8; transition: height 0.3s;">
                                    </div>
                                    <div style="font-size: 10px; color: var(--text3); margin-top: 10px; font-weight: 700;">
                                        {{ $dia['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-desktop"></i> Desempeño por Unidad</div>
                    </div>
                    <div class="tbl-wrap">
                        <table class="tbl tbl-modern">
                            <thead>
                                <tr>
                                    <th>Unidad</th>
                                    <th>Conductor</th>
                                    <th style="text-align: center;">Vueltas</th>
                                    <th>Tributo Diario</th>
                                    <th style="text-align: right;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vehiculosHoy as $v)
                                    @php $tributoHoy = $v->tributos->first(); @endphp
                                    <tr>
                                        <td>
                                            <div style="font-weight: 800; color: var(--accent);">#{{ $v->numero_flota }}
                                            </div>
                                            <div class="mono" style="font-size: 11px;">{{ $v->placa }}</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 13px; font-weight: 600;">
                                                {{ $v->conductor->nombre ?? '—' }}</div>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="pill blue"
                                                style="font-size: 10px; background: var(--accent-t); color: var(--accent);">{{ $v->vueltas_hoy }}
                                                completadas</span>
                                        </td>
                                        <td>
                                            <span class="pill {{ $tributoHoy?->estado == 'pagado' ? 'green' : 'red' }}"
                                                style="font-size: 10px;">
                                                {{ strtoupper($tributoHoy?->estado ?? 'PENDIENTE') }}
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('vehiculos.show', $v->id) }}"
                                                class="action-icon show-icon"><i class="fa-solid fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="padding: 15px; border-top: 1px solid var(--border-l);">
                        {{ $vehiculosHoy->appends(['page_d' => request('page_d'), 'offset' => request('offset')])->links('partials.pagination') }}
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: ALERTAS Y RANKINGS --}}
            <aside class="flex-v" style="gap: 24px;">

                {{-- TOP CONDUCTORES --}}
                <div class="card" style="border-top: 4px solid var(--accent);">
                    <div class="card-header">
                        <div class="card-title text-accent"><i class="fa-solid fa-trophy"></i> Líderes del Día</div>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        @forelse($topConductores as $index => $tc)
                            <div class="flex-between" style="padding: 12px 20px; border-bottom: 1px solid var(--border);">
                                <div class="flex-h" style="gap: 12px;">
                                    <div
                                        style="font-weight: 900; color: {{ $index == 0 ? 'var(--gold)' : 'var(--text3)' }}; font-size: 18px;">
                                        {{ $index + 1 }}</div>
                                    <div class="flex-v" style="gap: 2px;">
                                        <div style="font-weight: 700; font-size: 13px;">{{ $tc->conductor?->nombre }}
                                        </div>
                                        <div style="font-size: 10px; color: var(--text3);">Unidad:
                                            {{ $tc->conductor?->vehiculos->first()?->placa }}</div>
                                    </div>
                                </div>
                                <div class="pill blue" style="font-size: 10px; font-weight: 800;">
                                    {{ $tc->total_vueltas }} VLT.</div>
                            </div>
                        @empty
                            <div style="padding: 30px; text-align: center; color: var(--text3);">Sin datos hoy</div>
                        @endforelse
                    </div>
                </div>

                {{-- RANKING DE MOROSIDAD DETALLADO --}}
                <div class="card">
                    <div class="card-header flex-between">
                        <div class="card-title text-red"><i class="fa-solid fa-sack-xmark"></i> Flotas con más deuda (> 5 días)</div>
                        <span style="font-size: 11px; color: var(--text3); font-weight: 600;">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <div class="flex-v" style="gap: 12px;">
                            {{-- Encabezados --}}
                            <div class="flex-h" style="gap: 8px; font-size: 10px; font-weight: 800; color: var(--text3); padding-bottom: 4px; text-transform: uppercase;">
                                <div style="flex: 0.8; text-align: center;">Unidad</div>
                                <div style="flex: 1; text-align: center;">Días Deuda</div>
                                <div style="flex: 1.5;">Cond. / Prop.</div>
                                <div style="flex: 1; text-align: right;">Monto Total</div>
                            </div>
                            
                            {{-- Filas --}}
                            @forelse($topDeudores as $td)
                                <div class="flex-h" style="gap: 8px; align-items: stretch;">
                                    {{-- Unidad --}}
                                    <div style="flex: 0.8; border: 1px solid var(--border); border-radius: 8px; padding: 6px; background: var(--bg-body); display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                        <div style="background: var(--red-l); color: var(--red); padding: 2px 6px; border-radius: 4px; font-weight: 800; font-size: 11px; margin-bottom: 4px; white-space: nowrap;">#{{ $td->vehiculo?->numero_flota }}</div>
                                        <div class="mono" style="font-weight: 700; font-size: 10px; color: var(--text3);">{{ $td->vehiculo?->placa }}</div>
                                    </div>
                                    
                                    {{-- Días Deuda --}}
                                    <div style="flex: 1; border: 1px solid var(--border); border-radius: 8px; padding: 6px; background: var(--bg-body); display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                                        <div style="font-weight: 800; color: var(--red); font-size: 12px;">{{ $td->dias_deuda }} d</div>
                                        <div style="font-size: 9px; color: var(--text3);">desde {{ \Carbon\Carbon::parse($td->desde_cuando)->format('d/m/y') }}</div>
                                    </div>
                                    
                                    {{-- Conductor / Propietario --}}
                                    <div style="flex: 1.5; border: 1px solid var(--border); border-radius: 8px; padding: 6px 8px; background: var(--bg-body); display: flex; flex-direction: column; justify-content: center;">
                                        <div style="font-weight: 700; font-size: 10px; color: var(--text); line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $td->vehiculo?->conductor?->nombre }}">
                                            <i class="fa-solid fa-id-card" style="font-size: 9px; color: var(--text3); margin-right: 3px;"></i>{{ Str::limit($td->vehiculo?->conductor?->nombre ?? '—', 15) }}
                                        </div>
                                        <div style="font-size: 10px; color: var(--text3); border-top: 1px dashed var(--border); margin-top: 4px; padding-top: 4px; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $td->vehiculo?->propietario?->nombre }}">
                                            <i class="fa-solid fa-user-tie" style="font-size: 9px; color: var(--text3); margin-right: 3px;"></i>{{ Str::limit($td->vehiculo?->propietario?->nombre ?? '—', 15) }}
                                        </div>
                                    </div>
                                    
                                    {{-- Monto Total --}}
                                    <div style="flex: 1; border: 1px solid var(--red-l); border-radius: 8px; padding: 6px; background: var(--red-l); display: flex; flex-direction: column; justify-content: center; align-items: flex-end;">
                                        <div style="font-weight: 900; color: var(--red); font-size: 12px; white-space: nowrap;">S/ {{ number_format($td->total_deuda, 2) }}</div>
                                    </div>
                                </div>
                            @empty
                                <div style="text-align: center; padding: 30px; color: var(--green); font-weight: 600; border: 1px dashed var(--green); border-radius: 8px; background: var(--green-l);">
                                    <i class="fa-solid fa-check-circle"></i> No hay flotas con deuda crítica.
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div style="padding: 15px; border-top: 1px solid var(--border-l);">
                        {{ $topDeudores->appends(['page_v' => request('page_v'), 'offset' => request('offset')])->links('partials.pagination') }}
                    </div>
                </div>

            </aside>
        </div>
    </div>
@endsection
