@extends('layouts.admin')

@section('back_url', route('reportes.index'))

@section('content')
    <div style="display: grid; gap: 20px;">
        {{-- Filtros --}}
        <div class="card no-print">
            <form action="{{ route('reportes.tributos') }}" method="GET" class="card-body g-filters">
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
                    <input type="date"
                        onchange="if(this.value){ document.getElementsByName('desde')[0].value=this.value; document.getElementsByName('hasta')[0].value=this.value; this.form.submit(); }">
                </div>

                <div class="flex-h" style="gap: 10px; margin-top: auto;">
                    <button type="submit" class="btn-primary" style="height: 48px; padding: 0 25px;">📊 FILTRAR</button>
                    <button type="button" onclick="window.print()" class="btn-secondary"
                        style="height: 48px; border-radius: 12px; width: 48px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- TABLA DETALLADA --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Detalle de Movimientos
                    <small style="color: var(--text3); font-weight: 400; font-size: 13px;">({{ $desde->format('d/m/Y') }} -
                        {{ $hasta->format('d/m/Y') }})</small>
                </div>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="tbl" id="tablaDetallada">
                    <thead>
                        <tr>
                            <th>Fecha / Hora</th>
                            <th>Unidad / Placa</th>
                            <th>Conductor</th>
                            <th style="text-align:center;">Estado</th>
                            <th>Recaudación / Motivo</th>
                            <th style="text-align:right;">Monto</th>
                            <th style="text-align:right;">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detalle as $reg)
                            <tr>
                                <td style="font-size: 11.5px;">
                                    <div style="font-weight:700;">{{ $reg->fecha->format('d/m/Y') }}</div>
                                    <div class="mono" style="color:var(--text3); font-size:10px;">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $reg->cobrado_at ? $reg->cobrado_at->format('h:i A') : '---' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 800; color: var(--accent);">#{{ $reg->vehiculo->numero_flota }}
                                    </div>
                                    <div class="mono" style="font-size: 10px; color:var(--text3); font-weight:600;">
                                        {{ $reg->vehiculo->placa }}</div>
                                </td>
                                <td style="font-size: 12.5px; font-weight:500;">{{ $reg->conductor->nombre ?? '---' }}</td>
                                <td style="text-align:center;">
                                    @if ($reg->estado === 'pagado')
                                        <span class="pill green" style="font-size:9px;">PAGADO</span>
                                    @else
                                        <span class="pill gray" style="font-size:9px;">EXONERADO</span>
                                    @endif
                                </td>
                                <td style="font-size: 11.5px; max-width: 250px;">
                                    @if ($reg->estado === 'pagado')
                                        <div style="display:flex; flex-direction:column; gap:2px;">
                                            <span class="pill blue"
                                                style="font-size: 9px; align-self:flex-start;">{{ strtoupper($reg->metodo_pago ?? 'EFECTIVO') }}</span>
                                            @if ($reg->pagoMp)
                                                <span style="font-size:9.5px; color:#009ee3; font-weight:600;">ID:
                                                    {{ $reg->pagoMp->payment_id }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color:var(--text3); font-style:italic;">
                                            <i class="fa-solid fa-info-circle"></i>
                                            {{ \Str::limit($reg->observaciones, 40) }}
                                        </span>
                                    @endif
                                </td>
                                <td
                                    style="text-align:right; font-weight: 800; color: {{ $reg->estado === 'pagado' ? 'var(--green)' : 'var(--text3)' }};">
                                    S/ {{ number_format($reg->monto, 2) }}
                                </td>
                                <td style="text-align:right; font-size: 10.5px; color:var(--text3);">
                                    <div style="font-weight:600; color:var(--text2);">
                                        {{ $reg->cobrador->name ?? 'Sistema' }}</div>
                                    <div style="font-size:9px;">
                                        {{ $reg->cobrador ? $reg->cobrador->roles->first()?->name : '-' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding: 40px; color:var(--text3);">No hay
                                    movimientos procesados en este rango.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endsection
