@extends('layouts.admin')

@php
    $pageTitle = 'Roles y Permisos';
    $pageSubtitle = 'Gestión de niveles de acceso y seguridad del sistema';
@endphp

@section('content')
    {{-- Fila de Acciones --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div style="position: relative;">
            <input type="text" class="search-box"
                style="border: 1px solid var(--border2); border-radius: 10px; padding: 10px 18px 10px 40px; width: 320px; font-size: 13px; outline: none; transition: all 0.2s;"
                placeholder="Buscar rol por nombre...">
            <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.4;">🔍</span>
        </div>

        <a href="{{ route('roles.create') }}" class="btn-primary"
            style="padding: 12px 24px; font-weight: 700; box-shadow: var(--shadow-m); text-decoration: none; border-radius: 10px;">
            + Crear Nuevo Rol
        </a>
    </div>

    {{-- Tabla de Roles --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Listado de Roles Registrados</div>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Nombre del Rol</th>
                        <th>Permisos Asignados</th>
                        <th>Usuarios</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        {{-- No es necesario el IF aquí porque el Controlador ya filtró el SUPER_ADMIN, 
                             pero lo dejamos como doble escudo de seguridad --}}
                        @if ($role->name !== 'SUPER_ADMIN')
                            <tr>
                                <td style="width: 200px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div
                                            style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent);">
                                        </div>
                                        <strong style="color: var(--text); font-weight: 700;">{{ $role->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                        @foreach ($role->permissions as $permission)
                                            <span
                                                style="font-size: 10px; padding: 2px 10px; border: 1px solid var(--border); border-radius: 12px; color: var(--text2); background: var(--bg); font-weight: 600;">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if ($role->permissions->count() == 0)
                                            <span style="color: var(--text3); font-style: italic; font-size: 12px;">Sin
                                                permisos</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 120px;">
                                    <span class="pill blue" style="font-size: 11px; font-weight: 700;">
                                        {{ $role->users_count ?? $role->users->count() }} usuarios
                                    </span>
                                </td>
                                <td style="text-align: right; width: 180px;">
                                    <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn-secondary btn-sm"
                                            style="text-decoration: none; padding: 6px 12px; border-radius: 8px;">Editar</a>

                                        {{-- Bloqueamos la eliminación de ADMIN porque es el rol base de la empresa --}}
                                        @if ($role->name !== 'ADMIN')
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar el rol {{ $role->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger btn-sm"
                                                    style="padding: 6px 12px; border-radius: 8px;">Eliminar</button>
                                            </form>
                                        @else
                                            <span
                                                style="color: var(--text3); font-size: 11px; align-self: center; font-weight: 600; padding-right: 10px;">(Base)</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 60px; color: var(--text3);">
                                <div style="font-size: 40px; margin-bottom: 10px;">📂</div>
                                No hay otros roles registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
