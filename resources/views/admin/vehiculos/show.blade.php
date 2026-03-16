@extends('layouts.admin')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 25px;">
        {{-- Historial (Izquierda) --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Historial de la Unidad</div>
            </div>
            <div class="card-body"> ... historial de vueltas ... </div>
        </div>

        {{-- Ficha Técnica (Derecha) --}}
        <aside style="display: grid; gap: 20px;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Ficha Técnica</div>
                </div>
                <div class="card-body" style="font-size: 13px;">
                    <p><b>Placa:</b> {{ $vehiculo->placa }}</p>
                    <p><b>Padron:</b> #{{ $vehiculo->numero_flota }}</p>
                    <p><b>Color:</b> {{ $vehiculo->color ?? 'S/D' }}</p>
                    <hr>
                    <p style="font-weight: 700; color: var(--accent);">DOCUMENTACIÓN:</p>
                    <p><b>SOAT:</b> {{ $vehiculo->soat_vence ?? '---' }}</p>
                    <p><b>Rev. Técnica:</b> {{ $vehiculo->rev_tecnica_vence ?? '---' }}</p>
                    <p><b>Tarjeta Prop.:</b> {{ $vehiculo->tarjeta_prop_vence ?? '---' }}</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
