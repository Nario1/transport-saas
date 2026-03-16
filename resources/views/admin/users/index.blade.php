@extends('layouts.admin')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Usuarios de la Empresa</h2>
        <a href="{{ route('users.create') }}" class="btn-primary" style="text-decoration: none;">+ Nuevo Usuario</a>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <table class="tbl" style="width: 100%;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 12px;">Nombre</th>
                        <th style="padding: 12px;">Email</th>
                        <th style="padding: 12px;">Rol</th>
                        <th style="padding: 12px; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $user->email }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                <span class="badge"
                                    style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 5px; font-size: 11px;">
                                    {{ $user->roles->pluck('name')->implode(', ') ?: 'Sin Rol' }}
                                </span>
                            </td>
                            <td
                                style="padding: 12px; border-bottom: 1px solid #eee; text-align: right; display: flex; justify-content: flex-end; gap: 5px;">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-secondary btn-sm">Editar</a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('¿Eliminar usuario?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
