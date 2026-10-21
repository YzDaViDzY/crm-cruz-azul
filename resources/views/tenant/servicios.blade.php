@extends('layouts.app')
@section('title', 'Mis servicios')
@section('section', 'Mis servicios')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MI DEPARTAMENTO</p><h1>Mis servicios</h1><p class="muted">Servicios asociados a tus facturas.</p></div></div>
<div class="service-grid">@forelse($invoices->unique('servicio_id') as $invoice)<article class="service-card"><span class="stat-icon blue"><i class="fa-solid fa-bolt"></i></span><div><h2>{{ $invoice->servicio->nombre }}</h2><p>{{ $invoice->servicio->descripcion ?: 'Servicio del condominio' }}</p><small>Última factura: {{ $invoice->fecha_emision->format('d/m/Y') }} · {{ ucfirst($invoice->estado_actual) }}</small></div></article>@empty<section class="panel empty-panel">No hay servicios asociados todavía.</section>@endforelse</div>
@endsection
