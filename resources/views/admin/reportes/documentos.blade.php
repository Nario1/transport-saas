@extends('layouts.admin')

@section('content')
    <div style="display: grid; gap: 25px;">

        {{-- ALERTAS --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="card" style="background: #fef2f2; border: 1px solid #fee2e2;">
                <div class="card-body" style="padding: 20px; display: flex; align-items: center; gap: 20px;">
                    <div style="font-size: 30px;">❌</div>
                    <div>
                        <div style="font-weight: 800; color: #991b1b; font-size: 18px;">{{ $vencidos->count() }} Documentos
                            Vencidos</div>
                        <div style="font-size: 13px; color: #991b1b;">Vehículos que no deberían estar circulando.</div>
                    </div>
                </div>
            </div>
            <div class="card" style="background: #fffbeb; border: 1px solid #fef3c7;">
                <div class="card-body" style="padding: 20px; display: flex; align-items: center; gap: 20px;">
                    <div style="font-size: 30px;">⚠️</div>
                    <div>
                        <div style="font-weight: 800; color: #92400e; font-size: 18px;">{{ $porVencer->count() }} Próximos a
                            Vencer</div>
                        <div style="font-size: 13px; color: #92400e;">Documentos con vencimiento en los próximos 30 días.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLA DE DETALLE --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Listado Detallado de Control</div>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th>SOAT Vence</th>
                            <th>Rev. Técnica Vence</th>
                            <th>Conductor Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vencidos as $v)
                            <tr style="background: #fff1f2;">
                                <td><b style="color: #ef4444;">#{{ $v->numero_flota }}</b>
                                    <small>{{ $v->placa }}</small></td>
                                <td
                                    style="color: {{ $v->soat_vence < $hoy ? '#ef4444' : 'inherit' }}; font-weight: {{ $v->soat_vence < $hoy ? '800' : '400' }}">
                                    {{ \Carbon\Carbon::parse($v->soat_vence)->format('d/m/Y') }}
                                </td>
                                <td
                                    style="color: {{ $v->rev_tecnica_vence < $hoy ? '#ef4444' : 'inherit' }}; font-weight: {{ $v->rev_tecnica_vence < $hoy ? '800' : '400' }}">
                                    {{ \Carbon\Carbon::parse($v->rev_tecnica_vence)->format('d/m/Y') }}
                                </td>
                                <td>{{ $v->conductor->nombre ?? '---' }}</td>
                            </tr>
                        @endforeach

                        @foreach ($porVencer as $v)
                            <tr>
                                <td><b>#{{ $v->numero_flota }}</b> <small>{{ $v->placa }}</small></td>
                                <td style="color: #92400e; font-weight: 700;">
                                    {{ \Carbon\Carbon::parse($v->soat_vence)->format('d/m/Y') }}</td>
                                <td style="color: #92400e; font-weight: 700;">
                                    {{ \Carbon\Carbon::parse($v->rev_tecnica_vence)->format('d/m/Y') }}</td>
                                <td>{{ $v->conductor->nombre ?? '---' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
