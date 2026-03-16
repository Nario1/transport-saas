@extends('layouts.admin')

@php
    $pageTitle = 'Crear Nuevo Rol';
    $pageSubtitle = 'Define los accesos para el nuevo nivel de usuario';
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
                    <div class="card-title">Formulario de Registro de Rol</div>
                </div>

                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="form-section">
                            <div class="form-section-title">Nombre del Rol</div>
                            <div class="form-grid">
                                <div class="field field-full">
                                    <label>Seleccionar nivel de acceso</label>
                                    <select id="role_selector" onchange="checkRole(this)"
                                        style="width: 100%; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 14px;">
                                        <option value="" disabled selected>Seleccione un rol...</option>
                                        <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                                        <option value="OPERADOR">OPERADOR</option>
                                        <option value="USUARIO">USUARIO</option>
                                        <option value="custom">-- OTRO (Escribir nombre personalizado) --</option>
                                    </select>
                                </div>

                                {{-- Campo oculto para nombre personalizado --}}
                                <div id="custom_role_wrapper" class="field field-full"
                                    style="display: none; margin-top: 15px;">
                                    <label>Nombre del Nuevo Rol</label>
                                    <input type="text" id="custom_role_input" name="name"
                                        style="width: 100%; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 14px;"
                                        placeholder="Ej: GERENTE, DESPACHADOR, etc.">
                                </div>
                            </div>
                        </div>

                        <div class="form-section" style="margin-top: 30px;">
                            <div class="form-section-title">Asignación de Permisos (Accesos)</div>
                            <p style="font-size: 12px; color: var(--text3); margin-bottom: 15px;">
                                Selecciona las secciones a las que este rol podrá entrar:
                            </p>

                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px;">
                                @foreach ($allPermissions as $permiso)
                                    <label
                                        style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #f1f5f9; border-radius: 10px; cursor: pointer;">
                                        <input type="checkbox" name="permissions[]" value="{{ $permiso }}"
                                            style="width: 18px; height: 18px; accent-color: var(--accent);">
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
                                Guardar Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkRole(select) {
            const customWrapper = document.getElementById('custom_role_wrapper');
            const customInput = document.getElementById('custom_role_input');

            if (select.value === 'custom') {
                customWrapper.style.display = 'block';
                customInput.value = '';
                customInput.focus();
                // El input de texto lleva el name="name"
                customInput.name = "name";
                select.removeAttribute('name');
            } else {
                customWrapper.style.display = 'none';
                // El select lleva el name="name"
                select.name = "name";
                customInput.removeAttribute('name');
            }
        }
    </script>
@endsection
