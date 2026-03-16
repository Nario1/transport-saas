@extends('layouts.admin') {{-- O tu layout base --}}

@section('content')
    <div style="max-width: 500px; margin: 0 auto;">
        {{-- Perfil rápido --}}
        <div class="card"
            style="background: linear-gradient(135deg, var(--accent) 0%, #1e40af 100%); color: white; border: none; margin-bottom: 20px;">
            <div class="card-body" style="padding: 25px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div
                        style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 style="margin:0; font-size: 18px;">{{ Auth::user()->name }}</h2>
                        <p style="margin:0; font-size: 13px; opacity: 0.9;">Unidad: #{{ $vehiculo->numero_flota ?? 'S/N' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Menú de Acceso Rápido --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <a href="{{ route('conductor.pagos.index') }}" style="text-decoration: none;">
                <div class="card" style="text-align: center; padding: 20px;">
                    <div style="font-size: 30px; margin-bottom: 10px;">💰</div>
                    <div style="font-weight: 800; color: var(--text1);">Mis Pagos</div>
                    <div style="font-size: 11px; color: var(--text3);">S/ {{ number_format($totalDeuda, 2) }} pendiente
                    </div>
                </div>
            </a>

            <div class="card" style="text-align: center; padding: 20px; opacity: 0.6;">
                <div style="font-size: 30px; margin-bottom: 10px;">🔄</div>
                <div style="font-weight: 800; color: var(--text1);">Mis Vueltas</div>
                <div style="font-size: 11px; color: var(--text3);">Próximamente</div>
            </div>
        </div>

        {{-- Alertas de Documentos --}}
        @if ($documentosVencidos > 0)
            <div class="card" style="margin-top: 20px; border-left: 5px solid #ef4444; background: #fef2f2;">
                <div class="card-body" style="display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 20px;">⚠️</span>
                    <div style="font-size: 13px; color: #991b1b; font-weight: 700;">
                        Tienes documentos del vehículo por vencer o vencidos.
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
