@extends('layouts.admin')

@php
    $pageTitle = 'Editar Empresa';
    $pageSubtitle = 'Modificar información de ' . $empresa->nombre;
@endphp

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Actualizar Información</div>
            </div>
            <div class="card-body">
                <form action="{{ route('empresas.update', $empresa->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Nombre Comercial</label>
                            <input type="text" name="nombre" class="form-control" value="{{ $empresa->nombre }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">RUC</label>
                            <input type="text" name="ruc" class="form-control" value="{{ $empresa->ruc }}"
                                maxlength="11">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Plan</label>
                            <select name="plan" class="form-control" required>
                                <option value="basico" {{ $empresa->plan == 'basico' ? 'selected' : '' }}>Plan Básico
                                </option>
                                <option value="pro" {{ $empresa->plan == 'pro' ? 'selected' : '' }}>Plan Pro</option>
                                <option value="enterprise" {{ $empresa->plan == 'enterprise' ? 'selected' : '' }}>Plan
                                    Enterprise</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Tributo Diario (S/)</label>
                            <input type="number" step="0.01" name="tributo_diario" class="form-control"
                                value="{{ $empresa->tributo_diario }}" required>
                        </div>
                    </div>

                    <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 12px; color: var(--text3);">
                            Última actualización: {{ $empresa->updated_at->format('d/m/Y H:i') }}
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('empresas.index') }}" class="btn-secondary"
                                style="text-decoration: none; padding: 12px 20px;">Volver</a>
                            <button type="submit" class="btn-primary" style="padding: 12px 30px;">🔄 Actualizar
                                Datos</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
