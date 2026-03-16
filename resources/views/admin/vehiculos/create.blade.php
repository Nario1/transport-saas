@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 1000px; margin: 0 auto;">
        <div class="card-header">
            <div class="card-title">Registrar Nueva Unidad</div>
        </div>
        <div class="card-body">
            <form action="{{ route('vehiculos.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    {{-- Identificación --}}
                    <div class="form-group">
                        <label>Placa</label>
                        <input type="text" name="placa" value="{{ old('placa') }}" class="form-control"
                            placeholder="ABC-123" required>
                    </div>
                    <div class="form-group">
                        <label>Número de Padron</label>
                        <input type="number" name="numero_flota" value="{{ old('numero_flota') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="activo">🟢 Activo</option>
                            <option value="inactivo">🔴 Inactivo</option>
                            <option value="mantenimiento">🟠 Mantenimiento</option>
                        </select>
                    </div>

                    {{-- Detalles Técnicos --}}
                    <div class="form-group">
                        <label>Marca</label>
                        <input type="text" name="marca" value="{{ old('marca') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" value="{{ old('modelo') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" value="{{ old('color') }}" class="form-control"
                            placeholder="Ej: Blanco">
                    </div>
                    <div class="form-group">
                        <label>Año</label>
                        <input type="number" name="anio" value="{{ old('anio') }}" class="form-control">
                    </div>

                    {{-- Vencimientos --}}
                    <div class="form-group">
                        <label>SOAT Vencimiento</label>
                        <input type="date" name="soat_vence" value="{{ old('soat_vence') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Revisión Técnica Vence</label>
                        <input type="date" name="rev_tecnica_vence" value="{{ old('rev_tecnica_vence') }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Tarjeta Propiedad Vence</label>
                        <input type="date" name="tarjeta_prop_vence" value="{{ old('tarjeta_prop_vence') }}"
                            class="form-control">
                    </div>

                    {{-- Asignaciones --}}
                    <div class="form-group">
                        <label>Propietario</label>
                        <select name="propietario_id" class="form-control">
                            <option value="">-- Seleccionar --</option>
                            @foreach ($propietarios as $p)
                                <option value="{{ $p->id }}"
                                    {{ old('propietario_id') == $p->id ? 'selected' : '' }}>{{ $p->nombre }}
                                    {{ $p->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Conductor</label>
                        <select name="conductor_id" class="form-control">
                            <option value="">-- Seleccionar --</option>
                            @foreach ($conductores as $c)
                                <option value="{{ $c->id }}" {{ old('conductor_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->nombre }} {{ $c->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rutas --}}
                    <div class="form-group" style="grid-column: span 3;">
                        <label style="font-weight: 700; display: block; margin-bottom: 10px;">Rutas:</label>
                        <div
                            style="display: flex; flex-wrap: wrap; gap: 15px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            @foreach ($rutas as $ruta)
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
                                    <input type="checkbox" name="rutas[]" value="{{ $ruta->id }}"> {{ $ruta->nombre }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div style="margin-top: 30px; text-align: right;">
                    <button type="submit" class="btn-primary" style="padding: 10px 30px;">Guardar Vehículo</button>
                </div>
            </form>
        </div>
    </div>
@endsection
