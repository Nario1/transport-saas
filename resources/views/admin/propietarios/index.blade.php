@extends('layouts.admin')

@php
    $pageTitle = 'Propietarios';
    $pageSubtitle = 'Dueños de unidades de transporte';
@endphp

@section('content')
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="card-title">Listado de Propietarios</div>
            <a href="{{ route('propietarios.create') }}" class="btn-primary"
                style="text-decoration:none; padding: 10px 20px;">+ Nuevo Propietario</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($propietarios as $p)
                        <tr>
                            <td><span style="font-weight: 700; font-family: monospace;">{{ $p->dni ?? '---' }}</span></td>
                            <td>{{ $p->nombre_completo }}</td>
                            <td>{{ $p->telefono }}</td>
                            <td>
                                <span class="pill"
                                    style="background: {{ $p->activo ? '#dcfce7' : '#fee2e2' }}; color: {{ $p->activo ? '#166534' : '#991b1b' }};">
                                    {{ $p->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('propietarios.edit', $p->id) }}"
                                    style="text-decoration:none; margin-right: 10px;">⚙️</a>
                                <form action="{{ route('propietarios.destroy', $p->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="border:none; background:none; cursor:pointer;"
                                        onclick="return confirm('¿Eliminar propietario?')">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
