@extends('layouts.admin')

@php
    $pageTitle = 'Rutas de Transporte';
    $pageSubtitle = 'Gestión de itinerarios y paraderos';
@endphp

@section('content')
    {{-- Barra de Acciones Superior --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="flex: 1; max-width: 400px;">
            <input type="text" class="form-control" placeholder="🔍 Buscar ruta..." style="background: white;">
        </div>
        <a href="{{ route('rutas.create') }}" class="btn-primary" style="text-decoration:none; padding: 10px 20px;">
            + Nueva Ruta
        </a>
    </div>

    {{-- Cuadrícula de Rutas --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); gap: 20px;">
        @forelse($rutas as $r)
            <div class="card" style="display: flex; flex-direction: column;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <div class="card-title" style="font-size: 16px;">
                            {{ $r->codigo ?? 'R-' . $r->id }} — {{ $r->nombre }}
                        </div>
                        <div style="font-size:12px; color:var(--text3); margin-top:2px;">
                            {{ $r->vehiculos_activos_count }} vehículos asignados · {{ $r->vueltas_count }} vueltas hoy
                        </div>
                    </div>
                    <span class="pill {{ $r->estado === 'activa' ? 'green' : 'red' }}"
                        style="background: {{ $r->estado === 'activa' ? '#dcfce7' : '#fee2e2' }}; 
                                 color: {{ $r->estado === 'activa' ? '#166534' : '#991b1b' }};">
                        {{ ucfirst($r->estado) }}
                    </span>
                </div>

                <div style="padding: 16px 20px; flex-grow: 1;">
                    {{-- Timeline de Paraderos --}}
                    <div style="position: relative; padding-left: 10px;">
                        @php
                            $paraderos = $r->paraderos->sortBy('orden');
                            $totalParaderos = $paraderos->count();
                        @endphp

                        @forelse($paraderos as $index => $p)
                            <div class="route-stop"
                                style="display: flex; gap: 15px; margin-bottom: 12px; position: relative;">
                                {{-- Indicador Visual (Dot y Línea) --}}
                                <div style="display: flex; flex-direction: column; align-items: center; width: 12px;">
                                    @php
                                        $dotColor = 'var(--gold)';
                                        if ($p->tipo === 'origen') {
                                            $dotColor = 'var(--green)';
                                        }
                                        if ($p->tipo === 'destino') {
                                            $dotColor = 'var(--red)';
                                        }
                                    @endphp

                                    <div
                                        style="width: 12px; height: 12px; border-radius: 50%; background: {{ $dotColor }}; z-index: 2;">
                                    </div>

                                    @if ($index < $totalParaderos - 1)
                                        <div
                                            style="width: 2px; height: calc(100% + 12px); background: #e2e8f0; position: absolute; top: 12px;">
                                        </div>
                                    @endif
                                </div>

                                {{-- Información del Paradero --}}
                                <div>
                                    <div style="font-size: 13px; font-weight: 700; color: var(--text1); line-height: 1.2;">
                                        {{ $p->nombre }}
                                        <span
                                            style="font-size: 10px; font-weight: 400; color: var(--text3); text-transform: uppercase;">
                                            ({{ $p->tipo }})
                                        </span>
                                    </div>
                                    @if ($loop->first)
                                        <div style="font-size: 11px; color: var(--text3);">Punto de inicio ·
                                            {{ $r->origen }}</div>
                                    @elseif($loop->last)
                                        <div style="font-size: 11px; color: var(--text3);">Terminal final ·
                                            {{ $r->destino }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="font-size: 12px; color: var(--text3); font-style: italic; padding: 10px 0;">
                                No se han configurado paraderos para esta ruta.
                            </div>
                        @endforelse
                    </div>

                    <hr style="margin: 16px 0; border: 0; border-top: 1px solid #eee;">

                    {{-- Botones de Acción --}}
                    <div style="display:flex; gap:10px;">
                        <a href="{{ route('rutas.show', $r->id) }}" class="btn-secondary btn-sm"
                            style="text-decoration: none; font-size: 12px; padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text1);">
                            Ver detalle
                        </a>
                        <a href="{{ route('rutas.edit', $r->id) }}" class="btn-secondary btn-sm"
                            style="text-decoration: none; font-size: 12px; padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text1);">
                            Editar ruta
                        </a>
                        <form action="{{ route('rutas.destroy', $r->id) }}" method="POST" style="margin-left: auto;">
                            @csrf @method('DELETE')
                            <button type="submit" style="border:none; background:none; cursor:pointer;"
                                onclick="return confirm('¿Eliminar ruta?')">🗑️</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; padding: 40px; text-align: center; color: var(--text3);">
                No hay rutas registradas actualmente.
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div style="margin-top: 25px;">
        {{ $rutas->links() }}
    </div>
@endsection
