@extends('layouts.admin')

@section('back_url', route('reportes.index'))

@section('content')
    <div style="display: grid; gap: 20px;">
        {{-- Filtros --}}
        <div class="card no-print">
            <form action="{{ route('reportes.deudas') }}" method="GET" class="card-body g-filters">
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
                    <button type="submit" class="btn-primary" style="height: 48px; padding: 0 25px;">📊 FILTRAR</button>
                    <button type="button" onclick="window.print()" class="btn-secondary" style="height: 48px; border-radius: 12px; width: 48px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </div>
                <div style="width: 100%; margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: flex; justify-content: flex-end;">
                    <div style="background: #fee2e2; color: #b91c1c; padding: 10px 20px; border-radius: 8px; font-weight: 800; font-size: 14px;">
                        Deuda Total (en rango): S/ {{ number_format($totalDeuda, 2) }}
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Ranking de Morosidad por Unidad
                    <small style="color: var(--text3); font-weight: 400; font-size: 13px;">({{ $desde->format('d/m/Y') }} - {{ $hasta->format('d/m/Y') }})</small>
                </div>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th style="text-align:center;">Días Deuda</th>
                            <th>Conductor / Propietario</th>
                            <th style="text-align:right;">Monto Total</th>
                            <th style="text-align:right;" class="no-print">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deudas as $d)
                            <tr>
                                <td>
                                    <div style="font-weight: 800; color: var(--accent); font-size: 17px;">#{{ $d->vehiculo->numero_flota }}</div>
                                    <div class="mono" style="font-size: 11px; color: var(--text3); font-weight: 600;">{{ $d->vehiculo->placa }}</div>
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
                                        <span class="pill red" style="font-weight: 800; padding: 4px 12px; font-size: 13px;">{{ $d->dias_deuda }} días</span>
                                        <div style="font-size: 10px; color: var(--text3);">desde {{ \Carbon\Carbon::parse($d->desde_fecha)->format('d/m/Y') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <div style="font-size: 12.5px; font-weight: 600; color: var(--text1);">
                                            <i class="fa-solid fa-user-steering" style="width:15px; color:var(--accent);"></i> 
                                            {{ $d->vehiculo->conductor->nombre ?? 'Sin Conductor' }}
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--text3);">
                                            <i class="fa-solid fa-house-user" style="width:15px;"></i> 
                                            {{ $d->vehiculo->propietario->nombre ?? 'Sin Propietario' }}
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align:right; font-size: 18px; font-weight: 900; color: #b91c1c;">
                                    S/ {{ number_format($d->total_deuda, 2) }}
                                </td>
                                <td style="text-align:right;" class="no-print">
                                    <a href="{{ route('tributos.index') }}?vehiculo={{ $d->vehiculo_id }}"
                                        class="btn-primary"
                                        style="padding: 5px 10px; font-size: 11px; text-decoration:none;">Cobrar Deuda</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; padding: 40px; color: var(--text3);">No hay deudas pendientes en este rango.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
