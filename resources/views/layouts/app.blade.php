<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Condominio Cruz Azul')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@auth
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-mark"><i class="fa-solid fa-building"></i></span>
                <span>CRUZ AZUL<small>Administración</small></span>
            </a>
            <div class="nav-caption">{{ auth()->user()->role === 'admin' ? 'ADMINISTRACIÓN' : 'MI CONDOMINIO' }}</div>
            <nav class="side-nav">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i>Dashboard</a>
                    <a href="{{ route('admin.arrendatarios.index') }}" class="{{ request()->routeIs('admin.arrendatarios.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i>Arrendatarios</a>
                    <a href="{{ route('admin.servicios.index') }}" class="{{ request()->routeIs('admin.servicios.*') ? 'active' : '' }}"><i class="fa-solid fa-bolt"></i>Servicios</a>
                    <a href="{{ route('admin.facturas.index') }}" class="{{ request()->routeIs('admin.facturas.*') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice"></i>Facturas</a>
                    <a href="{{ route('admin.vencimientos.index') }}" class="{{ request()->routeIs('admin.vencimientos.*') ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i>Vencimientos</a>
                    <a href="{{ route('admin.pagos.index') }}" class="{{ request()->routeIs('admin.pagos.*') ? 'active' : '' }}"><i class="fa-solid fa-money-bill-wave"></i>Pagos</a>
                    <a href="{{ route('admin.historial.index') }}" class="{{ request()->routeIs('admin.historial.*') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i>Historial</a>
                    <a href="{{ route('admin.reportes.index') }}" class="{{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}"><i class="fa-solid fa-chart-column"></i>Reportes</a>
                @else
                    <a href="{{ route('arrendatario.dashboard') }}" class="{{ request()->routeIs('arrendatario.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i>Inicio</a>
                    <a href="{{ route('arrendatario.facturas') }}" class="{{ request()->routeIs('arrendatario.facturas') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice"></i>Mis facturas</a>
                    <a href="{{ route('arrendatario.estado-cuenta') }}" class="{{ request()->routeIs('arrendatario.estado-cuenta') ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i>Mi estado de cuenta</a>
                    <a href="{{ route('arrendatario.servicios') }}" class="{{ request()->routeIs('arrendatario.servicios') ? 'active' : '' }}"><i class="fa-solid fa-bolt"></i>Mis servicios</a>
                    <a href="{{ route('arrendatario.pagos') }}" class="{{ request()->routeIs('arrendatario.pagos') ? 'active' : '' }}"><i class="fa-solid fa-money-bill-wave"></i>Mis pagos</a>
                    <a href="{{ route('arrendatario.historial') }}" class="{{ request()->routeIs('arrendatario.historial') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i>Mi historial</a>
                @endif
            </nav>
            <div class="sidebar-bottom">
                <div class="profile-mini"><span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span>{{ auth()->user()->name }}<small>{{ auth()->user()->role === 'admin' ? 'Administrador' : 'Arrendatario' }}</small></span></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-logout" type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</button></form>
            </div>
        </aside>
        <div class="main-area">
            <header class="topbar">
                <button class="menu-toggle" type="button" aria-label="Abrir menú" data-menu-toggle><i class="fa-solid fa-bars"></i></button>
                <div class="topbar-label">Condominio Cruz Azul <span>/</span> @yield('section', 'Panel')</div>
                <div class="topbar-user"><span>{{ auth()->user()->name }}</span><span class="avatar avatar-small">{{ mb_substr(auth()->user()->name, 0, 1) }}</span></div>
            </header>
            <main class="page-content">
                @if(session('success'))<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>@endif
                @if($errors->any())<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i><div><strong>Revisa la información ingresada.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
                @yield('content')
            </main>
        </div>
    </div>
@else
    @yield('content')
@endauth
@stack('scripts')
</body>
</html>
