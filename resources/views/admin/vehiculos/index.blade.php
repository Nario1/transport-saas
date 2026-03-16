@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="card-title">Flota de Vehículos</div>
            <a href="{{ route('vehiculos.create') }}" class="btn-primary" style="text-decoration:none; padding: 10px 20px;">+
                Nuevo Vehículo</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Padron/Placa</th>
                        <th>Vehículo</th>
                        <th>Color</th>
                        <th>Rutas Asignadas</th> {{-- Nueva Columna --}}
                        <th>Próximos Vencimientos</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehiculos as $v)
                        <tr>
                            <td>
                                <div style="font-weight: 800; color: var(--accent);">#{{ $v->numero_flota ?? '---' }}</div>
                                <div
                                    style="font-family: monospace; font-weight: 700; background: #eee; padding: 2px 5px; border-radius: 4px; display: inline-block;">
                                    {{ $v->placa }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $v->marca }} {{ $v->modelo }}</div>
                                <div style="font-size: 11px; color: var(--text3);">Año: {{ $v->anio ?? '---' }}</div>
                            </td>
                            <td><span style="text-transform: capitalize;">{{ $v->color ?? '---' }}</span></td>

                            {{-- Columna de Rutas --}}
                            <td>
                                @forelse($v->rutas as $ruta)
                                    <span class="pill"
                                        style="background: #e0f2fe; color: #0369a1; font-size: 10px; margin-bottom: 2px; display: inline-block;">
                                        {{ $ruta->nombre }}
                                    </span>
                                @empty
                                    <span style="color: #94a3b8; font-size: 11px; font-style: italic;">Sin ruta</span>
                                @endforelse
                            </td>

                            <td>
                                <div style="font-size: 11px;"><b>SOAT:</b> {{ $v->soat_vence ?? 'Pend.' }}</div>
                                <div style="font-size: 11px;"><b>Rev. Tec:</b> {{ $v->rev_tecnica_vence ?? 'Pend.' }}</div>
                            </td>
                            <td>
                                <span class="pill"
                                    style="background: {{ $v->estado == 'activo' ? '#dcfce7' : '#fee2e2' }}; color: {{ $v->estado == 'activo' ? '#166534' : '#991b1b' }};">
                                    {{ ucfirst($v->estado) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('vehiculos.show', $v->id) }}"
                                    style="text-decoration:none; margin-right: 8px;">👁️</a>
                                <a href="{{ route('vehiculos.edit', $v->id) }}"
                                    style="text-decoration:none; margin-right: 8px;">⚙️</a>
                                <form action="{{ route('vehiculos.destroy', $v->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="border:none; background:none; cursor:pointer;"
                                        onclick="return confirm('¿Eliminar?')">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">No hay vehículos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
