@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header">
            <div class="card-title">Editar Conductor: {{ $conductor->nombre }}</div>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert"
                    style="background: #fee2e2; color: #dc2626; margin-bottom: 20px; border: 1px solid #fecaca;">
                    <ul style="margin: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('conductores.update', $conductor->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $conductor->nombre) }}"
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $conductor->apellidos) }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>DNI</label>
                        <input type="text" name="dni" value="{{ old('dni', $conductor->dni) }}" class="form-control"
                            maxlength="8">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $conductor->telefono) }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Propietario Asignado</label>
                        <select name="propietario_id" class="form-control">
                            <option value="">-- Seleccionar Propietario --</option>
                            @foreach ($propietarios as $p)
                                <option value="{{ $p->id }}"
                                    {{ old('propietario_id', $conductor->propietario_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }} {{ $p->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Licencia</label>
                        <input type="text" name="tipo_licencia"
                            value="{{ old('tipo_licencia', $conductor->tipo_licencia) }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Vencimiento de Licencia</label>
                        <input type="date" name="licencia_vence"
                            value="{{ old('licencia_vence', $conductor->licencia_vence) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="activo" {{ old('estado', $conductor->estado) == 'activo' ? 'selected' : '' }}>🟢
                                Activo</option>
                            <option value="suspendido"
                                {{ old('estado', $conductor->estado) == 'suspendido' ? 'selected' : '' }}>🟡 Suspendido
                            </option>
                            <option value="inactivo"
                                {{ old('estado', $conductor->estado) == 'inactivo' ? 'selected' : '' }}>🔴 Inactivo
                            </option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $conductor->direccion) }}"
                            class="form-control">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Notas</label>
                        <textarea name="notas" class="form-control" rows="3">{{ old('notas', $conductor->notas) }}</textarea>
                    </div>
                </div>
                <div style="margin-top: 25px; text-align: right; gap: 10px; display: flex; justify-content: flex-end;">
                    <a href="{{ route('conductores.index') }}" class="btn-secondary"
                        style="text-decoration:none;">Cancelar</a>
                    <button type="submit" class="btn-primary" style="padding: 10px 30px;">Actualizar Conductor</button>
                </div>
            </form>
        </div>
    </div>
@endsection
