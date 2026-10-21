@extends('layouts.app')
@section('title', 'Nuevo servicio')
@section('section', 'Servicios')
@section('content')
<div class="page-heading"><div><p class="eyebrow">CATÁLOGO</p><h1>Crear servicio</h1></div></div><section class="panel form-panel"><form method="POST" action="{{ route('admin.servicios.store') }}">@csrf @include('admin.servicios._form')</form></section>
@endsection
