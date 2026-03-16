@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 1000px; margin: 0 auto;">
        <div class="card-header">
            <div class="card-title">Editar Unidad: {{ $vehiculo->placa }}</div>
        </div>
        <div class="card-body">
            {{-- Bloque de errores de validación --}}
            @if ($errors->any())
                <div
                    style="background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #fecaca; font-size: 13px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('vehiculos.update', $vehiculo->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Importante para procesar la actualización --}}

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">

                    {{-- Identificación --}}
                    <div class="form-group">
                        <label>Placa</label>
                        <input type="text" name="placa" value="{{ old('placa', $vehiculo->placa) }}"
                            class="form-control" placeholder="ABC-123" required>
                    </div>

                    <div class="form-group">
                        <label>Número de Padron</label>
                        <input type="number" name="numero_flota" value="{{ old('numero_flota', $vehiculo->numero_flota) }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="activo" {{ old('estado', $vehiculo->estado) == 'activo' ? 'selected' : '' }}>🟢
                                Activo (Genera Tributo)</option>
                            <option value="inactivo" {{ old('estado', $vehiculo->estado) == 'inactivo' ? 'selected' : '' }}>
                                🔴 Inactivo</option>
                            <option value="mantenimiento"
                                {{ old('estado', $vehiculo->estado) == 'mantenimiento' ? 'selected' : '' }}>🟠 Mantenimiento
                            </option>
                            <option value="sin_salir"
                                {{ old('estado', $vehiculo->estado) == 'sin_salir' ? 'selected' : '' }}>🟡 Sin Salir
                            </option>
                        </select>
                    </div>

                    {{-- Detalles Técnicos --}}
                    <div class="form-group">
                        <label>Marca</label>
                        <input type="text" name="marca" value="{{ old('marca', $vehiculo->marca) }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" value="{{ old('modelo', $vehiculo->modelo) }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" value="{{ old('color', $vehiculo->color) }}"
                            class="form-control" placeholder="Ej: Blanco">
                    </div>

                    <div class="form-group">
                        <label>Año</label>
                        <input type="number" name="anio" value="{{ old('anio', $vehiculo->anio) }}"
                            class="form-control">
                    </div>

                    {{-- Vencimientos --}}
                    <div class="form-group">
                        <label>SOAT Vencimiento</label>
                        <input type="date" name="soat_vence" value="{{ old('soat_vence', $vehiculo->soat_vence) }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Revisión Técnica Vence</label>
                        <input type="date" name="rev_tecnica_vence"
                            value="{{ old('rev_tecnica_vence', $vehiculo->rev_tecnica_vence) }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tarjeta Propiedad Vence</label>
                        <input type="date" name="tarjeta_prop_vence"
                            value="{{ old('tarjeta_prop_vence', $vehiculo->tarjeta_prop_vence) }}" class="form-control">
                    </div>

                    {{-- Asignaciones --}}
                    <div class="form-group">
                        <label>Propietario</label>
                        <select name="propietario_id" class="form-control">
                            <option value="">-- Seleccionar --</option>
                            @foreach ($propietarios as $p)
                                <option value="{{ $p->id }}"
                                    {{ old('propietario_id', $vehiculo->propietario_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }} {{ $p->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Conductor</label>
                        <select name="conductor_id" class="form-control">
                            <option value="">-- Seleccionar --</option>
                            @foreach ($conductores as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('conductor_id', $vehiculo->conductor_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->nombre }} {{ $c->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rutas (Muchos a Muchos) --}}
                    <div class="form-group" style="grid-column: span 3;">
                        <label style="font-weight: 700; display: block; margin-bottom: 10px;">Rutas Asignadas:</label>
                        <div
                            style="display: flex; flex-wrap: wrap; gap: 15px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            @foreach ($rutas as $ruta)
                                <label
                                    style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                    <input type="checkbox" name="rutas[]" value="{{ $ruta->id }}" {{-- Lógica para marcar las rutas que ya tiene el vehículo --}}
                                        @if (is_array(old('rutas', $rutasAsignadas ?? $vehiculo->rutas->pluck('id')->toArray())) &&
                                                in_array($ruta->id, old('rutas', $rutasAsignadas ?? $vehiculo->rutas->pluck('id')->toArray()))) checked @endif>
                                    {{ $ruta->nombre }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Notas adicionales --}}
                    <div class="form-group" style="grid-column: span 3;">
                        <label>Notas / Observaciones</label>
                        <textarea name="notas" class="form-control" rows="2">{{ old('notas', $vehiculo->notas) }}</textarea>
                    </div>
                </div>

                <div style="margin-top: 30px; text-align: right; display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('vehiculos.index') }}" class="btn-secondary"
                        style="text-decoration: none; padding: 10px 20px; border-radius: 5px; background: #94a3b8; color: white;">Cancelar</a>
                    <button type="submit" class="btn-primary" style="padding: 10px 30px; font-weight: 700;">Actualizar
                        Vehículo</button>
                </div>
            </form>
        </div>
    </div>
@endsection
