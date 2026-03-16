@extends('layouts.admin')

@section('content')
    <div style="display: grid; gap: 25px;">

        {{-- 1. INDICADORES DE SANCIONES --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div class="card" style="padding: 20px; border-left: 5px solid #ef4444;">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase; font-weight: 700;">Deuda Total
                    Pendiente</div>
                <div style="font-size: 24px; font-weight: 800; color: #ef4444;">S/
                    {{ number_format($resumen['total_pendiente'], 2) }}</div>
                <div style="font-size: 12px; color: var(--text3);">{{ $resumen['cantidad_pendiente'] }} sanciones sin cobrar
                </div>
            </div>
            <div class="card" style="padding: 20px; border-left: 5px solid #10b981;">
                <div style="font-size: 11px; color: var(--text3); text-transform: uppercase; font-weight: 700;">Cobrado este
                    Mes</div>
                <div style="font-size: 24px; font-weight: 800; color: #10b981;">S/
                    {{ number_format($resumen['total_cobrado_mes'], 2) }}</div>
                <div style="font-size: 12px; color: var(--text3);">Efectividad de cobranza</div>
            </div>
            <div style="display: flex; align-items: center; justify-content: flex-end;">
                <a href="{{ route('sanciones.create') }}" class="btn-primary"
                    style="height: 50px; padding: 0 30px; display: flex; align-items: center; text-decoration: none; font-weight: 800;">
                    🚨 REGISTRAR SANCIÓN
                </a>
            </div>
        </div>

        {{-- 2. SANCIONES PENDIENTES (LO URGENTE) --}}
        <div class="card">
            <div class="card-header" style="background: #fff5f5;">
                <div class="card-title" style="color: #c53030;">Sanciones Pendientes de Pago</div>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th>Fecha/Motivo</th>
                            <th>Monto</th>
                            <th>Registrado por</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendientes as $p)
                            <tr>
                                <td>
                                    <div style="font-weight: 800; color: var(--accent);">#{{ $p->vehiculo->numero_flota }}
                                    </div>
                                    <div style="font-size: 11px; font-family: monospace;">{{ $p->vehiculo->placa }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 700;">{{ $p->motivo }}</div>
                                    <div style="font-size: 11px; color: var(--text3);">
                                        {{ \Carbon\Carbon::parse($p->fecha)->format('d/m/Y') }} ·
                                        {{ $p->conductor->nombre ?? 'Sin conductor' }}</div>
                                </td>
                                <td style="font-weight: 800; color: #ef4444;">S/ {{ number_format($p->monto, 2) }}</td>
                                <td><small>{{ $p->registrador->name }}</small></td>
                                <td style="text-align: right;">
                                    <form action="{{ route('sanciones.pagar', $p->id) }}" method="POST"
                                        style="display: inline-flex; gap: 5px;">
                                        @csrf
                                        <select name="metodo_pago" class="form-control"
                                            style="width: 100px; height: 32px; font-size: 11px;">
                                            <option value="efectivo">Efectivo</option>
                                            <option value="yape">Yape</option>
                                            <option value="plin">Plin</option>
                                        </select>
                                        <button type="submit" class="btn-primary"
                                            style="padding: 0 10px; font-size: 11px; height: 32px;">PAGAR</button>
                                    </form>
                                    <form action="{{ route('sanciones.destroy', $p->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background:none; border:none; cursor:pointer;"
                                            onclick="return confirm('¿Eliminar sanción?')">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: var(--text3);">No hay
                                    sanciones pendientes. ¡Buen trabajo!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 3. HISTORIAL DE SANCIONES PAGADAS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Historial de Sanciones Pagadas</div>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th>Motivo</th>
                            <th>Cobrado el</th>
                            <th>Monto</th>
                            <th>Método</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pagadas as $pag)
                            <tr>
                                <td><b>#{{ $pag->vehiculo->numero_flota }}</b></td>
                                <td style="font-size: 12px;">{{ $pag->motivo }}</td>
                                <td style="font-size: 11px;">
                                    {{ \Carbon\Carbon::parse($pag->cobrado_at)->format('d/m/Y H:i') }}</td>
                                <td style="font-weight: 700; color: #10b981;">S/ {{ number_format($pag->monto, 2) }}</td>
                                <td><span class="pill"
                                        style="background: #f1f5f9; text-transform: uppercase; font-size: 10px;">{{ $pag->metodo_pago ?? '---' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="padding: 20px;">{{ $pagadas->links() }}</div>
            </div>
        </div>
    </div>
@endsection
