@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <div class="card-title">Registrar Nuevo Propietario</div>
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

            <form action="{{ route('propietarios.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>DNI</label>
                        <input type="text" name="dni" value="{{ old('dni') }}" class="form-control"
                            maxlength="8">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control">
                    </div>
                </div>

                <div style="margin-top: 25px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('propietarios.index') }}" class="btn-secondary"
                        style="text-decoration: none;">Cancelar</a>
                    <button type="submit" class="btn-primary" style="padding: 10px 25px;">Guardar Propietario</button>
                </div>
            </form>
        </div>
    </div>
@endsection
