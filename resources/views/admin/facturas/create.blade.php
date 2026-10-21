@extends('layouts.app')
@section('title', 'Generar factura')
@section('section', 'Facturas')
@section('content')
<div class="page-heading"><div><p class="eyebrow">NUEVO CARGO</p><h1>Generar factura</h1><p class="muted">La deuda del arrendatario y su historial se actualizarán automáticamente.</p></div></div>
<section class="panel form-panel"><form method="POST" action="{{ route('admin.facturas.store') }}">@csrf<div class="form-grid">
    <label>Arrendatario<select name="arrendatario_id" required><option value="">Selecciona una cuenta</option>@foreach($tenants as $tenant)<option value="{{ $tenant->id }}" @selected(old('arrendatario_id') == $tenant->id)>{{ $tenant->nombre_completo }} · Depto. {{ $tenant->numero_departamento }}</option>@endforeach</select></label>
    <label>Servicio<select name="servicio_id" required><option value="">Selecciona servicio</option>@foreach($services as $service)<option value="{{ $service->id }}" data-amount="{{ $service->monto_base }}" @selected(old('servicio_id') == $service->id)>{{ $service->nombre }} · Bs {{ number_format($service->monto_base, 2) }}</option>@endforeach</select></label>
    <label>Monto (Bs)<input type="number" name="monto" min="0.01" step="0.01" value="{{ old('monto') }}" required></label>
    <label>Fecha de emisión<input type="date" name="fecha_emision" value="{{ old('fecha_emision', today()->format('Y-m-d')) }}" required></label>
    <label>Fecha de vencimiento<input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', today()->addDays(10)->format('Y-m-d')) }}" required></label>
    <label class="full-field">Observación<textarea name="observacion" rows="3">{{ old('observacion') }}</textarea></label>
</div><div class="form-actions"><a class="button button-secondary" href="{{ route('admin.facturas.index') }}">Cancelar</a><button class="button button-primary" type="submit">Guardar factura</button></div></form></section>
@endsection
