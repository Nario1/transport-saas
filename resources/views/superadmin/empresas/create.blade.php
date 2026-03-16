@extends('layouts.admin')

@php
    $pageTitle = 'Registrar Empresa';
    $pageSubtitle = 'Dar de alta una nueva empresa de transporte en la plataforma';
@endphp

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Datos de la Empresa</div>
            </div>
            <div class="card-body">
                <form action="{{ route('empresas.store') }}" method="POST" id="mainForm">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        {{-- Nombre --}}
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Nombre Comercial</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Transportes B"
                                required>
                        </div>

                        {{-- RUC --}}
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">RUC</label>
                            <input type="text" name="ruc" class="form-control" placeholder="11 dígitos"
                                maxlength="11">
                        </div>

                        {{-- Plan --}}
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Plan de Servicio</label>
                            <select name="plan" class="form-control" required>
                                <option value="basico">Plan Básico</option>
                                <option value="pro">Plan Pro</option>
                                <option value="enterprise">Plan Enterprise</option>
                            </select>
                        </div>

                        {{-- Tributo --}}
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; margin-bottom: 8px;">Tributo Diario (S/)</label>
                            <input type="number" step="0.01" name="tributo_diario" class="form-control" value="24.00"
                                required>
                        </div>
                    </div>

                    <div
                        style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="{{ route('empresas.index') }}" class="btn-secondary"
                            style="text-decoration: none; padding: 12px 20px;">Cancelar</a>
                        <button type="submit" class="btn-primary" style="padding: 12px 30px;">💾 Guardar Empresa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
