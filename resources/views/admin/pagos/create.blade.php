@extends('layouts.app')
@section('title', 'Registrar pago')
@section('section', 'Pagos')
@section('content')
<div class="page-heading"><div><p class="eyebrow">REGISTRO DE INGRESO</p><h1>Registrar pago</h1><p class="muted">El saldo se recalcula y el movimiento queda en el historial.</p></div></div>
<section class="panel form-panel"><form method="POST" action="{{ route('admin.pagos.store') }}">@csrf<div class="form-grid">
    <label>Arrendatario<select name="arrendatario_id" required data-tenant-select><option value="">Selecciona una cuenta</option>@foreach($tenants as $tenant)<option value="{{ $tenant->id }}" data-debt="{{ $tenant->deuda_actual }}" @selected(old('arrendatario_id') == $tenant->id)>{{ $tenant->nombre_completo }} · Deuda Bs {{ number_format($tenant->deuda_actual, 2) }}</option>@endforeach</select></label>
    <label>Factura (opcional)<select name="factura_servicio_id" data-invoice-select><option value="">Pago general / sin factura</option>@foreach($tenants as $tenant)@foreach($tenant->facturas->where('estado', '!=', 'pagado') as $invoice)<option value="{{ $invoice->id }}" data-tenant="{{ $tenant->id }}" data-balance="{{ number_format(max(0, (float) $invoice->monto - (float) $invoice->pagos->sum('monto')), 2, '.', '') }}" @selected(old('factura_servicio_id') == $invoice->id)>{{ $tenant->nombre_completo }} · {{ $invoice->servicio->nombre }} · Bs {{ number_format($invoice->monto, 2) }}</option>@endforeach @endforeach</select></label>
    <label>Monto recibido (Bs)<input type="number" name="monto" min="0.01" step="0.01" value="{{ old('monto') }}" required></label>
    <label>Concepto<input name="concepto" value="{{ old('concepto') }}" required maxlength="255" placeholder="Pago de factura de agua"></label>
    <label>Método de pago<select name="metodo_pago" required><option value="efectivo">Efectivo</option><option value="transferencia">Transferencia</option><option value="tarjeta">Tarjeta</option><option value="otro">Otro</option></select></label>
    <label>Fecha y hora<input type="datetime-local" name="fecha_pago" value="{{ old('fecha_pago', now()->format('Y-m-d\TH:i')) }}" required></label>
    <label class="full-field">Observación<textarea name="observacion" rows="3">{{ old('observacion') }}</textarea></label>
</div><div class="form-actions"><a class="button button-secondary" href="{{ route('admin.pagos.index') }}">Cancelar</a><button class="button button-primary" type="submit">Registrar pago</button></div></form></section>
@endsection
