@extends('layouts.admin')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3 style="margin:0;">Registrar Nuevo Usuario</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label>Nombre Completo</label>
                    <input type="text" name="name" class="form-control" required style="width: 100%; padding: 8px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" required style="width: 100%; padding: 8px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="form-control" required style="width: 100%; padding: 8px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" required
                        style="width: 100%; padding: 8px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label>Asignar Rol</label>
                    <select name="role" class="form-control" required style="width: 100%; padding: 8px;">
                        <option value="">Seleccione un rol...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">Guardar Usuario</button>
            </form>
        </div>
    </div>
@endsection
