@extends('layouts.app')
@section('title', 'Nuevo arrendatario')
@section('section', 'Arrendatarios')
@section('content')
<div class="page-heading"><div><p class="eyebrow">NUEVA CUENTA</p><h1>Registrar arrendatario</h1><p class="muted">El perfil y el acceso se crearán juntos.</p></div></div>
<section class="panel form-panel"><form method="POST" action="{{ route('admin.arrendatarios.store') }}">@csrf @include('admin.arrendatarios._form')</form></section>
@endsection
