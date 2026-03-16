@extends('layouts.admin')

@section('content')
    <div style="display: grid; gap: 20px;">
        {{-- Filtros --}}
        <div class="card">
            <div class="card-body">
                <form action="{{ route('reportes.tributos') }}" method="GET"
                    style="display: flex; gap: 20px; align-items: flex-end;">
                    <div class="form-group" style="margin:0;">
                        <label>Desde:</label>
                        <input type="date" name="desde" value="{{ $desde->toDateString() }}" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Hasta:</label>
                        <input type="date" name="hasta" value="{{ $hasta->toDateString() }}" class="form-control">
                    </div>
                    <button type="submit" class="btn-primary">📊 Filtrar Datos</button>
                    <button type="button" onclick="window.print()" class="btn-secondary">🖨️ Imprimir</button>
                </form>
            </div>
        </div>

        {{-- Dashboard de Totales --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div class="card" style="background: var(--accent); color: white; padding: 20px; text-align: center;">
                <div style="font-size: 11px; opacity: 0.8; text-transform: uppercase;">Total Recaudado en Rango</div>
                <div style="font-size: 32px; font-weight: 900;">S/ {{ number_format($totales['cobrado'], 2) }}</div>
            </div>
            <div class="card" style="background: #ef4444; color: white; padding: 20px; text-align: center;">
                <div style="font-size: 11px; opacity: 0.8; text-transform: uppercase;">Monto Pendiente (Rango)</div>
                <div style="font-size: 32px; font-weight: 900;">S/ {{ number_format($totales['pendiente'], 2) }}</div>
            </div>
            <div class="card" style="padding: 20px; text-align: center;">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase;">Días Analizados</div>
                <div style="font-size: 32px; font-weight: 900;">{{ $totales['dias'] }}</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 20px;">
            {{-- Tabla por Día --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Detalle de Ingresos por Día</div>
                </div>
                <div class="card-body" style="padding:0;">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th style="text-align:center;">Unidades</th>
                                <th style="text-align:right;">Cobrado</th>
                                <th style="text-align:right;">Pendiente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($porDia as $dia)
                                <tr>
                                    <td><b>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</b></td>
                                    <td style="text-align:center;">{{ $dia->pagados }} / {{ $dia->total_autos }}</td>
                                    <td style="text-align:right; font-weight: 700; color: #10b981;">S/
                                        {{ number_format($dia->total_cobrado, 2) }}</td>
                                    <td style="text-align:right; color: #ef4444;">S/
                                        {{ number_format($dia->monto_pendiente, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Métodos de Pago --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Por Método de Pago</div>
                </div>
                <div class="card-body">
                    @foreach ($porMetodo as $m)
                        <div style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span
                                    style="text-transform: uppercase; font-size: 12px; font-weight: 700;">{{ $m->metodo_pago }}</span>
                                <span style="font-weight: 800;">S/ {{ number_format($m->total, 2) }}</span>
                            </div>
                            <div style="font-size: 11px; color: var(--text3);">{{ $m->cantidad }} recibos generados</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
