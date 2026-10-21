@extends('layouts.app')
@section('title', 'Editar servicio')
@section('section', 'Servicios')
@section('content')
<div class="page-heading"><div><p class="eyebrow">CATÁLOGO</p><h1>Editar servicio</h1></div></div><section class="panel form-panel"><form method="POST" action="{{ route('admin.servicios.update', $service) }}">@csrf @method('PUT') @include('admin.servicios._form')</form></section>
@endsection
