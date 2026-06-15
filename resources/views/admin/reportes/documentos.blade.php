@extends('layouts.admin')

@section('back_url', route('reportes.index'))

@section('content')
    <div style="display: grid; gap: 20px;">
        {{-- Resumen de Alertas --}}
        <div class="flex-h no-print" style="gap: 20px; margin-bottom: 5px;">
            <div class="card flex-1" style="border-left: 5px solid var(--red); padding: 15px;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text3); text-transform: uppercase;">Críticos (7d)</div>
                <div style="font-size: 24px; font-weight: 800; color: var(--red);">{{ $resumen['criticos'] }}</div>
            </div>
            <div class="card flex-1" style="border-left: 5px solid var(--blue); padding: 15px;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text3); text-transform: uppercase;">Este Mes</div>
                <div style="font-size: 24px; font-weight: 800; color: var(--blue);">{{ $resumen['mes_actual'] }}</div>
            </div>
            <div class="card flex-1" style="border-left: 5px solid #111; padding: 15px;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text3); text-transform: uppercase;">Ya Vencidos</div>
                <div style="font-size: 24px; font-weight: 800; color: #111;">{{ $resumen['vencidos'] }}</div>
            </div>
        </div>

        {{-- Filtros Avanzados --}}
        <div class="card no-print">
            <form action="{{ route('reportes.documentos') }}" method="GET" class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                    <div class="field">
                        <label style="font-weight: 800; font-size: 11px;">ESTADO / PERIODO:</label>
                        <select name="tipo_filtro" onchange="this.form.submit()" style="height: 45px; border-radius: 10px; border: 1.5px solid var(--border); padding: 0 15px; font-weight: 600; width: 100%;">
                            <option value="todo" {{ $tipoFiltro === 'todo' ? 'selected' : '' }}>Historial Completo (Todo)</option>
                            <option value="mes" {{ $tipoFiltro === 'mes' ? 'selected' : '' }}>Mes Seleccionado</option>
                            <option value="urgente" {{ $tipoFiltro === 'urgente' ? 'selected' : '' }}>Críticos (7 días)</option>
                            <option value="vencidos" {{ $tipoFiltro === 'vencidos' ? 'selected' : '' }}>Ya Vencidos</option>
                            <option value="proximos_30" {{ $tipoFiltro === 'proximos_30' ? 'selected' : '' }}>Próximos 30 días</option>
                        </select>
                    </div>
                    <div class="field">
                        <label style="font-weight: 800; font-size: 11px;">MES DE VENCIMIENTO:</label>
                        <input type="month" name="mes" value="{{ $selectedMes }}" onchange="document.getElementById('tipo_filtro').value='mes'; this.form.submit();"
                               style="height: 45px; border-radius: 10px; border: 1.5px solid var(--border);">
                    </div>

                    <div class="field">
                        <label style="font-weight: 800; font-size: 11px;">TIPO DOCUMENTO:</label>
                        <select name="documento_tipo" onchange="this.form.submit()" style="height: 45px; border-radius: 10px; border: 1.5px solid var(--border); padding: 0 15px; font-weight: 600; color: var(--text); width: 100%;">
                            <option value="todos" {{ $docFiltro === 'todos' ? 'selected' : '' }}>Todos los documentos</option>
                            <option value="tarjeta" {{ $docFiltro === 'tarjeta' ? 'selected' : '' }}>Tarjeta de Propiedad</option>
                            <option value="soat" {{ $docFiltro === 'soat' ? 'selected' : '' }}>SOAT</option>
                            <option value="revision" {{ $docFiltro === 'revision' ? 'selected' : '' }}>Revisión Técnica</option>
                            <option value="licencia" {{ $docFiltro === 'licencia' ? 'selected' : '' }}>Licencia de Conducir</option>
                        </select>
                    </div>

                    <div class="field">
                        <label style="font-weight: 800; font-size: 11px;">BÚSQUEDA RÁPIDA:</label>
                        <div style="position: relative;">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Placa o Nombre..." 
                                   style="padding-left: 40px; height: 45px; border-radius: 10px; border: 1.5px solid var(--border); width: 100%;">
                            <i class="fa-solid fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text3);"></i>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="height: 45px; border-radius: 10px; width: 45px; padding:0; justify-content:center;">
                            <i class="fa-solid fa-sync"></i>
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-secondary" style="height: 45px; border-radius: 10px; width: 45px; padding:0; justify-content:center;">
                            <i class="fa-solid fa-print"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLA UNIFICADA DE ALERTAS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    @if($tipoFiltro === 'todo') 📑 Historial Completo de Documentos
                    @elseif($tipoFiltro === 'mes') Alertas de Vencimiento: {{ ucfirst($desde->monthName) }} {{ $desde->year }}
                    @elseif($tipoFiltro === 'urgente') 🚨 Documentos con Vencimiento Crítico (7 días)
                    @elseif($tipoFiltro === 'vencidos') 💀 Documentos Ya Vencidos
                    @elseif($tipoFiltro === 'proximos_15') ⏳ Vencimientos en los próximos 15 días
                    @elseif($tipoFiltro === 'proximos_30') 📅 Vencimientos en los próximos 30 días
                    @endif
                    <small style="color: var(--text3); font-weight: 400; font-size: 13px; display: block; margin-top: 4px;">
                        @if($search) Resultados para "{{ $search }}" - @endif
                        Mostrando alertas del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}
                    </small>
                </div>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="tbl tbl-modern">
                    <thead>
                        <tr>
                            <th>Entidad / Responsable</th>
                            <th>Documento</th>
                            <th style="text-align: center;">Fecha Vencimiento</th>
                            <th style="text-align: right;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alertas as $a)
                            @php
                                $dias = (int) $hoy->diffInDays($a->fecha, false);
                                $colorClass = $dias < 0 ? 'red' : ($dias <= 15 ? 'orange' : 'gold');
                                $estadoTxt = $dias < 0 ? 'VENCIDO' : ($dias == 0 ? 'VENCE HOY' : "VENCE EN " . abs($dias) . " DÍAS");
                            @endphp
                            <tr style="{{ $dias < 0 ? 'background: #fff5f5;' : '' }}">
                                <td>
                                    <div style="font-weight: 700; color: var(--text);">{{ $a->entidad }}</div>
                                    <div style="font-size: 11px; color: var(--text3);">Responsable: {{ $a->conductor }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 13px;">
                                        @if($a->documento == 'SOAT') <i class="fa-solid fa-shield-heart" style="color:var(--green);"></i> 
                                        @elseif($a->documento == 'Revisión Técnica') <i class="fa-solid fa-wrench" style="color:var(--orange);"></i>
                                        @else <i class="fa-solid fa-id-card" style="color:var(--accent);"></i> @endif
                                        {{ $a->documento }}
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="font-weight: 700; font-size: 15px;">{{ $a->fecha->format('d/m/Y') }}</div>
                                    <div style="font-size: 10px; color: var(--text3);">{{ $a->fecha->diffForHumans() }}</div>
                                </td>
                                <td style="text-align: right;">
                                    <span class="pill {{ $colorClass }}" style="font-size: 10px; font-weight: 800;">
                                        {{ $estadoTxt }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 60px; color: var(--text3);">
                                    <i class="fa-solid fa-file-circle-check" style="font-size: 40px; opacity: 0.1; display: block; margin-bottom: 15px;"></i>
                                    No se encontraron documentos próximos a vencer en este rango.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
