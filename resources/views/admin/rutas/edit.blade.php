@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header">
            <div class="card-title">Editar Ruta: {{ $ruta->nombre }}</div>
        </div>
        <div class="card-body">
            {{-- Bloque de Errores --}}
            @if ($errors->any())
                <div class="alert"
                    style="background: #fee2e2; color: #dc2626; margin-bottom: 20px; border: 1px solid #fecaca;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('rutas.update', $ruta->id) }}" method="POST" id="formRuta">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Nombre de la Ruta</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $ruta->nombre) }}" class="form-control"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Código Interno</label>
                        <input type="text" name="codigo" value="{{ old('codigo', $ruta->codigo) }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Origen</label>
                        <input type="text" name="origen" value="{{ old('origen', $ruta->origen) }}" class="form-control"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Destino</label>
                        <input type="text" name="destino" value="{{ old('destino', $ruta->destino) }}"
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Duración Est. (min)</label>
                        <input type="number" name="duracion_min" value="{{ old('duracion_min', $ruta->duracion_min) }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control" required>
                            <option value="activa" {{ old('estado', $ruta->estado) == 'activa' ? 'selected' : '' }}>Activa
                            </option>
                            <option value="inactiva" {{ old('estado', $ruta->estado) == 'inactiva' ? 'selected' : '' }}>
                                Inactiva</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" value="{{ old('descripcion', $ruta->descripcion) }}"
                            class="form-control">
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
                            {{-- Cargar paraderos existentes --}}
                            @foreach ($ruta->paraderos->sortBy('orden') as $index => $paradero)
                                <tr>
                                    <td>
                                        <input type="text" name="paraderos[{{ $index }}][nombre]"
                                            value="{{ $paradero->nombre }}" class="form-control" required>
                                    </td>
                                    <td>
                                        <select name="paraderos[{{ $index }}][tipo]" class="form-control" required>
                                            <option value="intermedio"
                                                {{ $paradero->tipo == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                                            <option value="origen" {{ $paradero->tipo == 'origen' ? 'selected' : '' }}>
                                                Origen</option>
                                            <option value="destino" {{ $paradero->tipo == 'destino' ? 'selected' : '' }}>
                                                Destino</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" onclick="this.closest('tr').remove()"
                                            style="border:none; background:none; cursor:pointer;">❌</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 30px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('rutas.index') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary" style="padding: 12px 35px;">Actualizar Ruta</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Inicializamos el índice basándonos en la cantidad de paraderos existentes
        let paraderoIndex = {{ $ruta->paraderos->count() }};

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
    </script>
@endsection
