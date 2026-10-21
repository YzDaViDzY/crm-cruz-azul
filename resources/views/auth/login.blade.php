@extends('layouts.app')

@section('title', 'Iniciar sesión | Condominio Cruz Azul')

@section('content')
<div class="login-wrap">
    <section class="login-card">
        <div class="login-logo"><span class="brand-mark"><i class="fa-solid fa-building"></i></span><div><strong>CONDOMINIO</strong><small>CRUZ AZUL</small></div></div>
        <p class="eyebrow">PORTAL DE ADMINISTRACIÓN</p>
        <h1>Bienvenido</h1>
        <p class="muted">Ingresa con tu correo y contraseña para continuar.</p>
        @if($errors->any())<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('login.store') }}" class="form-stack">
            @csrf
            <label>Correo electrónico<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="nombre@correo.com"></label>
            <label>Contraseña<input type="password" name="password" autocomplete="current-password" required placeholder="Tu contraseña"></label>
            <label class="checkbox-label"><input type="checkbox" name="remember" value="1"> Mantener sesión iniciada</label>
            <button class="button button-primary button-full" type="submit">Ingresar <i class="fa-solid fa-arrow-right"></i></button>
        </form>
        <p class="login-foot">Gestión clara y segura para tu comunidad.</p>
    </section>
    <aside class="login-art"><div class="art-badge"><i class="fa-solid fa-shield-halved"></i> Portal seguro</div><div><p class="eyebrow">TU COMUNIDAD, EN ORDEN</p><h2>Administración simple para vivir mejor.</h2><p>Consulta facturas, pagos y el estado de cuenta desde un solo lugar.</p></div><div class="art-footer"><i class="fa-solid fa-location-dot"></i> Condominio Cruz Azul</div></aside>
</div>
@endsection
