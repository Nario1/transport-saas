@extends('layouts.admin')

@php
    $pageTitle = 'Editar Usuario';
    $pageSubtitle = 'Actualizar información de acceso para ' . $user->name;
@endphp

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto; border-radius: 15px; box-shadow: var(--shadow-m);">
        <div class="card-header" style="background: var(--bg); border-bottom: 1px solid var(--border);">
            <h3 style="margin:0; font-weight: 800; color: var(--text);">👤 Perfil de Usuario</h3>
        </div>
        <div class="card-body" style="padding: 25px;">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nombre --}}
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text2);">Nombre
                        Completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control"
                        required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border);">
                    @error('name')
                        <small style="color: var(--red);">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Email --}}
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text2);">Correo
                        Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control"
                        required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border);">
                    @error('email')
                        <small style="color: var(--red);">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Roles --}}
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text2);">Rol del
                        Sistema</label>
                    <select name="role" class="form-control" required
                        style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: white;">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ (old('role') ?? ($user->roles->first()->name ?? '')) == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border); margin: 25px 0;">

                <p style="font-size: 12px; color: var(--text3); margin-bottom: 15px;">
                    ℹ️ Complete los siguientes campos solo si desea cambiar la contraseña actual.
                </p>

                {{-- Password --}}
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text2);">Nueva
                        Contraseña</label>
                    <input type="password" name="password" class="form-control"
                        style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border);">
                    @error('password')
                        <small style="color: var(--red);">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Password Confirmation (Necesario para el controlador) --}}
                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 700; margin-bottom: 8px; color: var(--text2);">Confirmar
                        Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control"
                        style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border);">
                </div>

                {{-- Botones --}}
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn-primary"
                        style="flex: 1; padding: 12px; font-weight: 800; border-radius: 10px;">
                        💾 Actualizar Datos
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-secondary"
                        style="flex: 1; text-align: center; text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
