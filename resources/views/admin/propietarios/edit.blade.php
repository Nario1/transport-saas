@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <div class="card-title">Editar Propietario: {{ $propietario->nombre }}</div>
        </div>
        <div class="card-body">

            {{-- BLOQUE PARA VER ERRORES DE VALIDACIÓN --}}
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

            <form action="{{ route('propietarios.update', $propietario->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- VITAL para que Laravel reconozca que es una actualización --}}

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $propietario->nombre) }}"
                            class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $propietario->apellidos) }}"
                            class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>DNI</label>
                        <input type="text" name="dni" value="{{ old('dni', $propietario->dni) }}"
                            class="form-control" maxlength="8">
                    </div>

                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $propietario->telefono) }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $propietario->email) }}"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <select name="activo" class="form-control">
                            <option value="1" {{ old('activo', $propietario->activo) == 1 ? 'selected' : '' }}>🟢
                                Activo</option>
                            <option value="0" {{ old('activo', $propietario->activo) == 0 ? 'selected' : '' }}>🔴
                                Inactivo</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $propietario->direccion) }}"
                            class="form-control">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Notas / Observaciones</label>
                        <textarea name="notas" class="form-control" rows="3" style="resize: none;">{{ old('notas', $propietario->notas) }}</textarea>
                    </div>
                </div>

                <div style="margin-top: 25px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('propietarios.index') }}" class="btn-secondary"
                        style="text-decoration: none; padding: 10px 20px; border-radius: 8px; background: #f3f4f6; color: #374151; display: inline-block;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary" style="padding: 10px 25px; font-weight: 700;">
                        Actualizar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
