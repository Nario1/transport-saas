@extends('layouts.admin')

@section('content')
    <div style="display: grid; gap: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin:0;">Ranking de Morosidad</h2>
            <div style="background: #fee2e2; color: #b91c1c; padding: 10px 20px; border-radius: 8px; font-weight: 800;">
                Deuda Total Empresa: S/ {{ number_format($totalDeuda, 2) }}
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:0;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th>Días Deuda</th>
                            <th>Deuda Desde</th>
                            <th>Conductor / Propietario</th>
                            <th style="text-align:right;">Monto Total</th>
                            <th style="text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deudas as $d)
                            <tr>
                                <td><b style="font-size: 16px;">#{{ $d->vehiculo->numero_flota }}</b> <br>
                                    <small>{{ $d->vehiculo->placa }}</small></td>
                                <td style="text-align:center;">
                                    <span class="pill red"
                                        style="font-weight: 800; padding: 5px 15px; font-size: 14px;">{{ $d->dias_deuda }}
                                        días</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($d->desde_fecha)->format('d/m/Y') }}</td>
                                <td>
                                    <div style="font-size: 12px;">👤 C: {{ $d->vehiculo->conductor->nombre ?? '---' }}</div>
                                    <div style="font-size: 12px; color: var(--text3);">🏠 P:
                                        {{ $d->vehiculo->propietario->nombre ?? '---' }}</div>
                                </td>
                                <td style="text-align:right; font-size: 18px; font-weight: 900; color: #b91c1c;">S/
                                    {{ number_format($d->total_deuda, 2) }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('tributos.index') }}?vehiculo={{ $d->vehiculo_id }}"
                                        class="btn-primary"
                                        style="padding: 5px 10px; font-size: 11px; text-decoration:none;">Cobrar Deuda</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
