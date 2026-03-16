@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header">
            <div class="card-title">Registrar Nueva Ruta</div>
        </div>
        <div class="card-body">
            <form action="{{ route('rutas.store') }}" method="POST" id="formRuta">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Nombre de la Ruta (Ej: Huancayo - El Tambo)</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Código Interno</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control"
                            placeholder="H-01">
                    </div>
                    <div class="form-group">
                        <label>Origen</label>
                        <input type="text" name="origen" value="{{ old('origen') }}" class="form-control"
                            placeholder="Punto de inicio" required>
                    </div>
                    <div class="form-group">
                        <label>Destino</label>
                        <input type="text" name="destino" value="{{ old('destino') }}" class="form-control"
                            placeholder="Punto final" required>
                    </div>
                    <div class="form-group">
                        <label>Duración Est. (min)</label>
                        <input type="number" name="duracion_min" value="{{ old('duracion_min') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="activa" {{ old('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="inactiva" {{ old('estado') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" value="{{ old('descripcion') }}" class="form-control">
                    </div>
                </div>

                <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                <div id="section-paraderos">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="font-size: 16px; font-weight: 700;">Secuencia de Paraderos</h3>
                        <button type="button" onclick="agregarFila()" class="btn-secondary"
                            style="font-size: 12px; padding: 5px 15px;">+ Añadir Paradero</button>
                    </div>

                    <table class="tbl" id="tablaParaderos">
                        <thead>
                            <tr style="background: #f9fafb;">
                                <th>Nombre del Paradero</th>
                                <th width="200">Tipo</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Las filas se agregan con JS --}}
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 30px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('rutas.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary" style="padding: 12px 35px;">Guardar Ruta</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let paraderoIndex = 0;

        function agregarFila() {
            const tbody = document.querySelector('#tablaParaderos tbody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td><input type="text" name="paraderos[${paraderoIndex}][nombre]" class="form-control" required></td>
            <td>
                <select name="paraderos[${paraderoIndex}][tipo]" class="form-control" required>
                    <option value="intermedio">Intermedio</option>
                    <option value="origen">Origen</option>
                    <option value="destino">Destino</option>
                </select>
            </td>
            <td><button type="button" onclick="this.closest('tr').remove()" style="border:none; background:none; cursor:pointer;">❌</button></td>
        `;
            tbody.appendChild(tr);
            paraderoIndex++;
        }
        // Agregar una fila por defecto
        window.onload = agregarFila;
    </script>
@endsection
