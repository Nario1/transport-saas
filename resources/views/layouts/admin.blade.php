@extends('layouts.app')

@section('body_content')
    <aside class="sb">
        <div class="sb-brand">
            <div class="brand-logo">
                <div class="brand-icon">TJ</div>
                <div>
                    <div class="brand-name">TransJunín</div>
                    <div class="brand-sub">SaaS Platform</div>
                </div>
            </div>
        </div>

        <div class="sb-company">
            <div class="company-dot"
                style="background: {{ auth()->user()->hasRole('SUPER_ADMIN') ? 'var(--gold)' : '#22c55e' }};"></div>
            <div class="company-name">
                {{ auth()->user()->hasRole('SUPER_ADMIN') ? 'Panel Global SaaS' : Auth::user()->empresa->nombre ?? 'Mi Empresa' }}
            </div>
            <div class="company-caret">▾</div>
        </div>

        <nav class="sb-nav">

            {{-- 1. SECCIÓN PRINCIPAL --}}
            <div class="nav-group">
                <div class="nav-group-label">Principal</div>

                @role('SUPER_ADMIN')
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                        <span class="ni">▦</span> Dashboard Maestro
                    </a>
                @endrole

                @unless (auth()->user()->hasRole('SUPER_ADMIN'))
                    @can('ver dashboard')
                        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="ni">▦</span> Dashboard
                        </a>
                    @endcan
                @endunless
            </div>

            {{-- 2. SECCIÓN PARA EL SUPER ADMIN (GERMAN) --}}
            @role('SUPER_ADMIN')
                <div class="nav-group">
                    <div class="nav-group-label" style="color: var(--gold);">Administración Global</div>
                    <a href="{{ route('empresas.index') }}"
                        class="nav-item {{ request()->routeIs('empresas.*') ? 'active' : '' }}">
                        <span class="ni">🏢</span> Empresas Clientes
                    </a>
                </div>
            @endrole

            {{-- 3. SECCIÓN OPERATIVA (Empresas) --}}
            @unless (auth()->user()->hasRole('SUPER_ADMIN'))
                {{-- GRUPO: MAESTROS --}}
                @if (auth()->user()->can('ver vehiculos') ||
                        auth()->user()->can('ver conductores') ||
                        auth()->user()->can('ver propietarios') ||
                        auth()->user()->can('ver rutas'))
                    <div class="nav-group">
                        <div class="nav-group-label">Maestros</div>
                        @can('ver vehiculos')
                            <a href="{{ route('vehiculos.index') }}"
                                class="nav-item {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}">
                                <span class="ni">🚗</span> Vehículos
                            </a>
                        @endcan
                        @can('ver conductores')
                            <a href="{{ route('conductores.index') }}"
                                class="nav-item {{ request()->routeIs('conductores.*') ? 'active' : '' }}">
                                <span class="ni">👤</span> Conductores
                            </a>
                        @endcan
                        @can('ver propietarios')
                            <a href="{{ route('propietarios.index') }}"
                                class="nav-item {{ request()->routeIs('propietarios.*') ? 'active' : '' }}">
                                <span class="ni">🏠</span> Propietarios
                            </a>
                        @endcan
                        @can('ver rutas')
                            <a href="{{ route('rutas.index') }}"
                                class="nav-item {{ request()->routeIs('rutas.*') ? 'active' : '' }}">
                                <span class="ni">🗺</span> Rutas
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- GRUPO: OPERACIÓN --}}
                @if (auth()->user()->can('ver vueltas') || auth()->user()->can('ver tributos') || auth()->user()->can('ver sanciones'))
                    <div class="nav-group">
                        <div class="nav-group-label">Operación</div>
                        @can('ver vueltas')
                            <a href="{{ route('vueltas.index') }}"
                                class="nav-item {{ request()->routeIs('vueltas.*') ? 'active' : '' }}">
                                <span class="ni">🔄</span> Control de Vueltas
                            </a>
                        @endcan
                        @can('ver tributos')
                            <a href="{{ route('tributos.index') }}"
                                class="nav-item {{ request()->routeIs('tributos.*') ? 'active' : '' }}">
                                <span class="ni">💰</span> Tributos
                            </a>
                        @endcan
                        @can('ver sanciones')
                            <a href="{{ route('sanciones.index') }}"
                                class="nav-item {{ request()->routeIs('sanciones.*') ? 'active' : '' }}">
                                <span class="ni">⚠️</span> Sanciones
                            </a>
                        @endcan
                    </div>
                @endif

                {{-- GRUPO: REPORTES --}}
                @can('ver reportes')
                    <div class="nav-group">
                        <div class="nav-group-label">Análisis</div>
                        <a href="{{ route('reportes.index') }}"
                            class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                            <span class="ni">📊</span> Reportes Generales
                        </a>
                    </div>
                @endcan

                {{-- GRUPO: CONFIGURACIÓN --}}
                @if (auth()->user()->can('gestionar usuarios') || auth()->user()->can('gestionar roles'))
                    <div class="nav-group">
                        <div class="nav-group-label">Configuración</div>
                        @can('gestionar usuarios')
                            <a href="{{ route('users.index') }}"
                                class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <span class="ni">👥</span> Mi Personal
                            </a>
                        @endcan
                        @can('gestionar roles')
                            <a href="{{ route('roles.index') }}"
                                class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <span class="ni">🔑</span> Roles / Permisos
                            </a>
                        @endcan
                    </div>
                @endif
            @endunless
        </nav>

        <div class="sb-footer">
            <div class="user-row">
                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                    <div class="user-av">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                    <div style="overflow: hidden;">
                        <div class="user-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ Auth::user()->name }}</div>
                        <div class="user-role">{{ Auth::user()->roles->pluck('name')->first() }}</div>
                    </div>
                </div>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="logout-btn" title="Cerrar Sesión">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar" style="height: 70px;">
            <div class="tb-left">
                <div class="tb-title" id="topTitle">{{ $pageTitle ?? 'TransJunín' }}</div>
                <div class="tb-crumb" id="topCrumb">{{ $pageSubtitle ?? 'Huancayo, Perú' }}</div>
            </div>
            <div class="tb-right">
                <div class="tb-date">📅 {{ ucfirst(\Carbon\Carbon::now()->translatedFormat('l d M Y')) }}</div>
                @yield('topbar_right_content')
            </div>
        </div>

        <div class="content panel-anim">
            @if (session('success'))
                <div class="alert"
                    style="background: var(--green-l); color: var(--green); border: 1px solid rgba(22, 163, 74, 0.2); font-weight: 700; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert"
                    style="background: var(--red-l); color: var(--red); border: 1px solid rgba(220, 38, 38, 0.2); font-weight: 700; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;">
                    🔒 {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </div>
@endsection
