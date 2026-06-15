@extends('layouts.admin')

@section('back_url', route('superadmin.empresas.index'))

@php
    $pageTitle = 'Registrar Nueva Empresa';
    $pageSubtitle = 'Dar de alta una empresa de transporte en el sistema global';
@endphp

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Datos Maestros de la Empresa</div>
            </div>
            <div class="card-body">
                <form action="{{ route('superadmin.empresas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Nombre Comercial *</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Transportes B" value="{{ old('nombre') }}" required>
                            @error('nombre') <small style="color: #dc2626;">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">RUC *</label>
                            <input type="text" name="ruc" class="form-control" placeholder="11 dígitos" value="{{ old('ruc') }}" maxlength="11" required>
                            @error('ruc') <small style="color: #dc2626;">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Razón Social</label>
                            <input type="text" name="razon_social" class="form-control" placeholder="Razón Social Legal" value="{{ old('razon_social') }}">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="Central telefónica" value="{{ old('telefono') }}">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Dirección</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Sede principal" value="{{ old('direccion') }}">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Plan SaaS *</label>
                            <select name="plan" class="form-control" required>
                                <option value="basico">Plan Básico</option>
                                <option value="pro">Plan Pro</option>
                                <option value="enterprise" selected>Plan Enterprise</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Tributo Diario Base (S/) *</label>
                            <input type="number" step="0.01" name="tributo_diario" class="form-control" value="24.00" required>
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Logo de Empresa</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <small style="color: var(--text3);">Sube el logo que se mostrará en el sidebar de este cliente.</small>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="{{ route('superadmin.empresas.index') }}" class="btn-secondary" style="text-decoration: none; padding: 12px 20px;">Cancelar</a>
                        <button type="submit" class="btn-primary" style="padding: 12px 30px;">🚀 Registrar Empresa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
