@extends('layouts.admin')

@section('back_url', route('superadmin.empresas.index'))

@php
    $pageTitle = 'Editar Empresa (Master)';
    $pageSubtitle = 'Gestión Global de ' . $empresa->nombre;
@endphp

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Actualizar Información Master</div>
            </div>
            <div class="card-body">
                <form action="{{ route('superadmin.empresas.update', $empresa->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Nombre Comercial</label>
                            <input type="text" name="nombre" class="form-control" value="{{ $empresa->nombre }}" required>
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">RUC</label>
                            <input type="text" name="ruc" class="form-control" value="{{ $empresa->ruc }}" maxlength="11">
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Razón Social</label>
                            <input type="text" name="razon_social" class="form-control" value="{{ $empresa->razon_social }}">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ $empresa->telefono }}">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Dirección</label>
                            <input type="text" name="direccion" class="form-control" value="{{ $empresa->direccion }}">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Plan SaaS</label>
                            <select name="plan" class="form-control" required>
                                <option value="basico" {{ $empresa->plan == 'basico' ? 'selected' : '' }}>Plan Básico</option>
                                <option value="pro" {{ $empresa->plan == 'pro' ? 'selected' : '' }}>Plan Pro</option>
                                <option value="enterprise" {{ $empresa->plan == 'enterprise' ? 'selected' : '' }}>Plan Enterprise</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Tributo Diario (S/)</label>
                            <input type="number" step="0.01" name="tributo_diario" class="form-control" value="{{ $empresa->tributo_diario }}" required>
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Logo de Empresa</label>
                            <div style="display: flex; gap: 15px; align-items: center;">
                                @if($empresa->logo_path)
                                    <img src="{{ asset('storage/' . $empresa->logo_path) }}" alt="Logo" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; background: #f9f9f9;">
                                @endif
                                <input type="file" name="logo" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 12px; color: var(--text3);">
                            Última actualización: {{ $empresa->updated_at->format('d/m/Y H:i') }}
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('superadmin.empresas.index') }}" class="btn-secondary" style="text-decoration: none; padding: 12px 20px;">Volver</a>
                            <button type="submit" class="btn-primary" style="padding: 12px 30px;">🔄 Actualizar Empresa</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
