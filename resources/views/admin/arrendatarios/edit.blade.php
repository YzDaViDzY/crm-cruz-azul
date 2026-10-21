@extends('layouts.app')
@section('title', 'Editar arrendatario')
@section('section', 'Arrendatarios')
@section('content')
<div class="page-heading"><div><p class="eyebrow">ACTUALIZAR INFORMACIÓN</p><h1>Editar arrendatario</h1><p class="muted">{{ $tenant->nombre_completo }} · Departamento {{ $tenant->numero_departamento }}</p></div></div>
<section class="panel form-panel"><form method="POST" action="{{ route('admin.arrendatarios.update', $tenant) }}">@csrf @method('PUT') @include('admin.arrendatarios._form')</form></section>
@endsection
