@extends('layouts.admin')

@section('content')
    <div style="max-width: 500px; margin: 0 auto;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
            <a href="{{ route('conductor.index') }}" style="text-decoration: none; font-size: 20px;">⬅️</a>
            <h2 style="margin:0;">Mis Tributos</h2>
        </div>

        {{-- Resumen de Deuda --}}
        <div class="card" style="text-align: center; padding: 25px; margin-bottom: 20px; border-top: 5px solid #ef4444;">
            <div style="font-size: 12px; color: var(--text3); text-transform: uppercase;">Monto total a pagar</div>
            <div style="font-size: 36px; font-weight: 900; color: #ef4444;">S/ {{ number_format($totalDeuda, 2) }}</div>
        </div>

        {{-- Lista de Tributos Pendientes --}}
        <div style="display: grid; gap: 10px;">
            @forelse($tributosPendientes as $t)
                <div class="card">
                    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 800;">{{ \Carbon\Carbon::parse($t->fecha)->format('d/m/Y') }}</div>
                            <div style="font-size: 12px; color: var(--text3);">Tributo Diario</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 800; margin-bottom: 5px;">S/ {{ number_format($t->monto, 2) }}</div>

                            {{-- Botón Placeholder para Mercado Pago --}}
                            <form action="{{ route('conductor.pagar', $t->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary" style="font-size: 11px; padding: 5px 15px;">Pagar
                                    con Yape</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 50px; color: var(--text3);">
                    <p>🎉 ¡Felicidades! Estás al día con tus pagos.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
