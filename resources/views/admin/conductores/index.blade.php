@extends('layouts.admin')

@php
    $pageTitle = 'Conductores';
    $pageSubtitle = 'Gestión de personal de conducción';
@endphp

@section('content')
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="card-title">Lista de Conductores</div>
            <a href="{{ route('conductores.create') }}" class="btn-primary" style="text-decoration:none; padding: 10px 20px;">+
                Nuevo Conductor</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Conductor</th>
                        <th>Licencia</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conductores as $c)
                        <tr>
                            <td><span style="font-weight: 700; font-family: monospace;">{{ $c->dni ?? '---' }}</span></td>
                            <td>
                                <div style="font-weight: 600;">{{ $c->nombre }} {{ $c->apellidos }}</div>
                                <div style="font-size: 11px; color: var(--text3);">Prop:
                                    {{ $c->propietario->nombre ?? 'Sin asignar' }}</div>
                            </td>
                            <td><span class="pill"
                                    style="background: #f3f4f6; color: #374151;">{{ $c->tipo_licencia }}</span></td>
                            <td>{{ $c->licencia_vence ? \Carbon\Carbon::parse($c->licencia_vence)->format('d/m/Y') : '---' }}
                            </td>
                            <td>
                                @php
                                    $colors = [
                                        'activo' => ['bg' => '#dcfce7', 'text' => '#166534'],
                                        'suspendido' => ['bg' => '#fef9c3', 'text' => '#854d0e'],
                                        'inactivo' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                    ];
                                    $color = $colors[$c->estado] ?? ['bg' => '#eee', 'text' => '#333'];
                                @endphp
                                <span class="pill"
                                    style="background: {{ $color['bg'] }}; color: {{ $color['text'] }}; text-transform: capitalize;">
                                    {{ $c->estado }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('conductores.edit', $c->id) }}"
                                    style="text-decoration:none; margin-right: 10px;" title="Editar">⚙️</a>
                                <form action="{{ route('conductores.destroy', $c->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="border:none; background:none; cursor:pointer;"
                                        onclick="return confirm('¿Eliminar conductor?')">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text3);">No hay
                                conductores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($conductores->hasPages())
            <div style="padding: 20px;">
                {{ $conductores->links() }}
            </div>
        @endif
    </div>
@endsection
