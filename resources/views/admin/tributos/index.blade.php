@extends('layouts.admin')

@section('content')
    <div style="display: grid; gap: 25px;">

        {{-- 1. RESUMEN FINANCIERO DEL DÍA --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
            <div class="card" style="padding: 20px; border-left: 5px solid #10b981;">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase;">Total Cobrado</div>
                <div style="font-size: 24px; font-weight: 800; color: #10b981;">S/
                    {{ number_format($resumen['total_cobrado'], 2) }}</div>
                <div style="font-size: 12px; color: var(--text3);">{{ $resumen['autos_pagaron'] }} vehículos</div>
            </div>
            <div class="card" style="padding: 20px; border-left: 5px solid #ef4444;">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase;">Por Cobrar (Hoy)</div>
                <div style="font-size: 24px; font-weight: 800; color: #ef4444;">S/
                    {{ number_format($resumen['total_pendiente'], 2) }}</div>
                <div style="font-size: 12px; color: var(--text3);">{{ $resumen['autos_pendientes'] }} vehículos</div>
            </div>
            <div class="card" style="padding: 20px; border-left: 5px solid var(--accent);">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase;">Fecha de Consulta</div>
                <form action="{{ route('tributos.index') }}" method="GET" id="fechaForm">
                    <input type="date" name="fecha" value="{{ $fecha }}"
                        onchange="document.getElementById('fechaForm').submit()"
                        style="border:none; font-family:inherit; font-weight:700; font-size:16px; color:var(--accent); cursor:pointer; width:100%;">
                </form>
            </div>
            <div style="display: flex; align-items: center;">
                <form action="{{ route('tributos.generar') }}" method="POST" style="width: 100%;">
                    @csrf
                    <button type="submit" class="btn-primary" style="width: 100%; height: 60px; font-weight: 800;">
                        🔄 GENERAR TRIBUTOS HOY
                    </button>
                </form>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 380px; gap: 25px;">

            {{-- COLUMNA IZQUIERDA: GESTIÓN DEL DÍA --}}
            <div style="display: grid; gap: 25px;">

                {{-- PENDIENTES --}}
                <div class="card">
                    <div class="card-header" style="background: #fff5f5;">
                        <div class="card-title" style="color:#c53030;">⚠️ Pendientes de hoy ({{ $fecha }})</div>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Unidad</th>
                                    <th>Conductor</th>
                                    <th>Monto</th>
                                    <th style="text-align: right;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendientes as $p)
                                    <tr>
                                        <td><b style="color:var(--accent);">#{{ $p->vehiculo->numero_flota }}</b>
                                            <small>{{ $p->vehiculo->placa }}</small>
                                        </td>
                                        <td style="font-size: 12px;">{{ $p->conductor->nombre ?? '---' }}</td>
                                        <td style="font-weight: 700;">S/ {{ number_format($p->monto, 2) }}</td>
                                        <td style="text-align: right;">
                                            <form action="{{ route('tributos.cobrar', $p->id) }}" method="POST"
                                                style="display: flex; gap: 5px; justify-content: flex-end;">
                                                @csrf
                                                <select name="metodo_pago" class="form-control"
                                                    style="width: 100px; padding: 2px 5px; font-size: 11px;" required>
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="yape">Yape</option>
                                                    <option value="plin">Plin</option>
                                                </select>
                                                <button type="submit" class="btn-primary"
                                                    style="padding: 5px 10px; font-size: 11px;">COBRAR</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 20px; color: var(--text3);">
                                            No hay tributos pendientes para esta fecha.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGADOS --}}
                <div class="card">
                    <div class="card-header" style="background: #f0fff4;">
                        <div class="card-title" style="color:#2f855a;">✅ Cobrados hoy ({{ $fecha }})</div>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Unidad</th>
                                    <th>Hora Cobro</th>
                                    <th>Método</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pagados as $pag)
                                    <tr>
                                        <td><b>#{{ $pag->vehiculo->numero_flota }}</b></td>
                                        <td style="font-size: 12px;">
                                            {{ \Carbon\Carbon::parse($pag->cobrado_at)->format('H:i A') }}</td>
                                        <td><span class="pill"
                                                style="background:#e2e8f0; font-size: 10px;">{{ strtoupper($pag->metodo_pago) }}</span>
                                        </td>
                                        <td style="font-weight: 700; color: #2f855a;">S/
                                            {{ number_format($pag->monto, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: DEUDAS HISTÓRICAS --}}
            <div class="card">
                <div class="card-header" style="background: #2d3748; color: white;">
                    <div class="card-title" style="color:white;">🚨 Deudas Acumuladas</div>
                </div>
                <div class="card-body" style="padding:0;">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Unidad</th>
                                <th>Días</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deudas as $deuda)
                                <tr style="background: {{ $deuda->dias_deuda > 3 ? '#fff5f5' : 'transparent' }}">
                                    <td><b>#{{ $deuda->vehiculo->numero_flota }}</b></td>
                                    <td style="text-align: center;"><span class="pill red">{{ $deuda->dias_deuda }}
                                            d</span></td>
                                    <td style="font-weight: 800; color:#c53030;">S/
                                        {{ number_format($deuda->total_deuda, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
