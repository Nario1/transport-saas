@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <div class="card-header">
            <div class="card-title">Registrar Nueva Sanción / Multa</div>
        </div>
        <div class="card-body">
            <form action="{{ route('sanciones.store') }}" method="POST">
                @csrf
                <div style="display: grid; gap: 20px;">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Vehículo (Unidad)</label>
                            <select name="vehiculo_id" id="vehiculo_select" class="form-control" required>
                                <option value="">-- Seleccionar Unidad --</option>
                                @foreach ($vehiculos as $v)
                                    <option value="{{ $v->id }}" data-conductor="{{ $v->conductor_id }}">
                                        Padron #{{ $v->numero_flota }} - {{ $v->placa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Conductor Responsable</label>
                            <select name="conductor_id" id="conductor_select" class="form-control">
                                <option value="">-- Seleccionar Conductor --</option>
                                @foreach ($conductores as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }} {{ $c->apellidos }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Fecha de la Infracción</label>
                            <input type="date" name="fecha" value="{{ $fechaHoy }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Monto de la Sanción (S/)</label>
                            <input type="number" name="monto" step="0.50" class="form-control" placeholder="0.00"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Motivo de la Sanción</label>
                        <select name="motivo" class="form-control" required>
                            <option value="">-- Seleccionar Motivo --</option>
                            <option value="Incumplimiento de Ruta">Incumplimiento de Ruta</option>
                            <option value="Exceso de Velocidad">Exceso de Velocidad</option>
                            <option value="Paradero Omitido">Paradero Omitido</option>
                            <option value="Maltrato al Usuario">Maltrato al Usuario</option>
                            <option value="Falta de Documentación">Falta de Documentación</option>
                            <option value="Otro">Otro (Especificar en descripción)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Descripción / Detalles</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalle lo ocurrido..."></textarea>
                    </div>

                    <div style="margin-top: 10px; display: flex; gap: 10px;">
                        <button type="submit" class="btn-primary"
                            style="flex: 1; padding: 15px; font-weight: 800;">REGISTRAR SANCIÓN</button>
                        <a href="{{ route('sanciones.index') }}" class="btn-secondary"
                            style="padding: 15px; text-decoration: none;">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Seleccionar automáticamente al conductor asignado al vehículo
        document.getElementById('vehiculo_select').addEventListener('change', function() {
            let conductorId = this.options[this.selectedIndex].getAttribute('data-conductor');
            if (conductorId) {
                document.getElementById('conductor_select').value = conductorId;
            }
        });
    </script>
@endsection
