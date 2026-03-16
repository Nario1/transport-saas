@extends('layouts.admin')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 25px;">

        {{-- COLUMNA IZQUIERDA --}}
        <div style="display: grid; gap: 25px;">

            {{-- Itinerario de Paraderos (Timeline) --}}
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="card-title">Itinerario de Control</div>
                    </div>
                    <button onclick="document.getElementById('modalParadero').style.display='flex'" class="btn-primary"
                        style="font-size: 12px; padding: 8px 15px;">+ Nuevo Punto</button>
                </div>
                <div class="card-body">
                    <div style="position: relative; padding-left: 20px;">
                        @php $paraderos = $ruta->paraderos->sortBy('orden'); @endphp
                        @foreach ($paraderos as $p)
                            <div style="display: flex; gap: 20px; margin-bottom: 20px; position: relative;">
                                <div style="display: flex; flex-direction: column; align-items: center; width: 14px;">
                                    @php
                                        $dotColor = '#eab308';
                                        if ($p->tipo === 'origen') {
                                            $dotColor = '#22c55e';
                                        }
                                        if ($p->tipo === 'destino') {
                                            $dotColor = '#ef4444';
                                        }
                                    @endphp
                                    <div
                                        style="width: 14px; height: 14px; border-radius: 50%; background: {{ $dotColor }}; z-index: 2; border: 3px solid white; box-shadow: 0 0 0 1px {{ $dotColor }};">
                                    </div>
                                    @if (!$loop->last)
                                        <div
                                            style="width: 2px; height: calc(100% + 20px); background: #e2e8f0; position: absolute; top: 14px;">
                                        </div>
                                    @endif
                                </div>
                                <div style="flex: 1; display: flex; justify-content: space-between;">
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700;">{{ $p->nombre }}</div>
                                        <div style="font-size: 11px; color: var(--text3); text-transform: uppercase;">
                                            {{ $p->tipo }}</div>
                                    </div>
                                    <form action="{{ route('rutas.paraderos.destroy', [$ruta->id, $p->id]) }}"
                                        method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            style="border:none; background:none; cursor:pointer; color: #94a3b8;"
                                            onclick="return confirm('¿Quitar?')">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- VEHÍCULOS ASIGNADOS (Corregido) --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Vehículos en esta Ruta</div>
                    <div style="font-size: 12px; color: var(--text3);">Unidades activas actualmente</div>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                        {{-- CAMBIADO A $ruta->vehiculos --}}
                        @forelse($ruta->vehiculos as $v)
                            <div
                                style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span
                                        style="font-family: monospace; font-weight: 800; color: var(--accent); background: white; padding: 2px 6px; border-radius: 4px; border: 1px solid #cbd5e1;">
                                        {{ $v->placa }}
                                    </span>
                                    <span
                                        style="font-size: 11px; font-weight: 700; color: var(--text3);">#{{ $v->numero_flota }}</span>
                                </div>
                                <div style="font-size: 12px; font-weight: 700; color: var(--text1);">
                                    👤 {{ $v->conductor->nombre ?? 'Sin Conductor' }}
                                </div>
                                <div style="font-size: 10px; color: var(--text3);">{{ $v->marca }} {{ $v->modelo }}
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1/-1; text-align: center; color: var(--text3); font-size: 13px;">
                                No hay vehículos activos asignados.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA --}}
        <aside style="display: grid; gap: 25px; align-content: start;">
            <div class="card" style="border-top: 4px solid var(--accent);">
                <div class="card-body">
                    <div style="font-weight: 800; font-size: 22px;">{{ $ruta->nombre }}</div>
                    <div style="color: var(--accent); font-weight: 700; font-family: monospace; margin-bottom: 15px;">
                        {{ $ruta->codigo }}</div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div style="background: #e0f2fe; padding: 15px; border-radius: 12px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 800; color: #0369a1;">{{ $ruta->vehiculos->count() }}
                            </div>
                            <div style="font-size: 10px; color: #0369a1; font-weight: 700;">UNIDADES</div>
                        </div>
                        <div style="background: #fdf2f8; padding: 15px; border-radius: 12px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 800; color: #be185d;">
                                {{ $ruta->vueltas->where('fecha', today())->count() }}</div>
                            <div style="font-size: 10px; color: #be185d; font-weight: 700;">VUELTAS HOY</div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('rutas.index') }}" class="btn-secondary"
                style="text-align: center; text-decoration: none;">Volver al listado</a>
        </aside>
    </div>

    {{-- Modal Paradero (Igual al anterior) --}}
    ...
@endsection
