@extends('layouts.admin')

@php
    $pageTitle = 'Editar Rol';
    $pageSubtitle = 'Modifica los accesos para el nivel: ' . $role->name;
@endphp

@section('content')
    <div style="display: flex; justify-content: center; width: 100%; padding: 20px 0;">
        <div style="width: 100%; max-width: 800px;">

            <div style="display: flex; justify-content: flex-start; margin-bottom: 24px;">
                <a href="{{ route('roles.index') }}" class="btn-secondary"
                    style="text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 600;">
                    ← Volver al listado
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Actualizar Configuración de Rol</div>
                </div>

                <div class="card-body">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-section">
                            <div class="form-section-title">Nombre del Rol</div>
                            <div class="form-grid">
                                <div class="field field-full">
                                    <label>Nombre identificador</label>
                                    <input type="text" name="name" value="{{ $role->name }}" required
                                        style="width: 100%; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 14px; background-color: #f8fafc;"
                                        {{ $role->name == 'SUPER_ADMIN' ? 'readonly' : '' }}>
                                    @if ($role->name == 'SUPER_ADMIN')
                                        <p style="font-size: 11px; color: var(--text3); margin-top: 5px;">
                                            * El nombre del rol SUPER_ADMIN no puede ser modificado por seguridad.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-section" style="margin-top: 30px;">
                            <div class="form-section-title">Asignación de Permisos (Accesos)</div>
                            <p style="font-size: 12px; color: var(--text3); margin-bottom: 15px;">
                                Marca o desmarca los permisos para este rol:
                            </p>

                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px;">
                                @foreach ($allPermissions as $permiso)
                                    <label
                                        style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #f1f5f9; border-radius: 10px; cursor: pointer;">
                                        <input type="checkbox" name="permissions[]" value="{{ $permiso }}"
                                            style="width: 18px; height: 18px; accent-color: var(--accent);"
                                            {{ $role->hasPermissionTo($permiso) ? 'checked' : '' }}>
                                        <span style="font-size: 13.5px; font-weight: 500; color: var(--text2);">
                                            {{ ucfirst($permiso) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div
                            style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border); display: flex; gap: 12px; justify-content: flex-end;">
                            <a href="{{ route('roles.index') }}" class="btn-secondary"
                                style="text-decoration: none;">Cancelar</a>
                            <button type="submit" class="btn-primary"
                                style="padding: 12px 32px; font-weight: 700; box-shadow: var(--shadow-m);">
                                Actualizar Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
