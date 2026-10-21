@extends('layouts.app')
@section('title', 'Mi cuenta | Condominio Cruz Azul')
@section('section', 'Inicio')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MI CUENTA</p><h1>Hola, {{ $tenant->nombres }}</h1><p class="muted">Departamento {{ $tenant->numero_departamento }} · Estado de tu cuenta.</p></div></div>
<div class="stats-grid">
    <article class="stat-card"><span class="stat-icon {{ (float) $tenant->deuda_actual > 0 ? 'orange' : 'green' }}"><i class="fa-solid fa-wallet"></i></span><div><small>Deuda actual</small>@if((float) $tenant->deuda_actual > 0)<strong>Bs {{ number_format($tenant->deuda_actual, 2) }}</strong><span class="stat-note">saldo pendiente</span>@else<strong class="status-clear">Bs 0.00</strong><span class="stat-note">Sin deuda pendiente</span>@endif</div></article>
    <article class="stat-card"><span class="stat-icon blue"><i class="fa-solid fa-file-invoice"></i></span><div><small>Facturas pendientes</small><strong>{{ $pendingInvoices->count() }}</strong><span class="stat-note">requieren atención</span></div></article>
    <article class="stat-card"><span class="stat-icon purple"><i class="fa-solid fa-calendar-days"></i></span><div><small>Próximo vencimiento</small><strong class="date-stat">{{ $nextInvoice ? $nextInvoice->fecha_vencimiento->format('d/m/Y') : '—' }}</strong><span class="stat-note">{{ $nextInvoice?->servicio?->nombre ?? 'Sin vencimientos' }}</span></div></article>
</div>
<div class="content-grid">
    <section class="panel"><div class="panel-heading"><div><h2>Facturas por pagar</h2><p>Servicios asociados a tu departamento</p></div><a class="text-link" href="{{ route('arrendatario.facturas') }}">Ver todas <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="table-wrap"><table><thead><tr><th>Servicio</th><th>Vence</th><th>Estado</th><th>Monto</th></tr></thead><tbody>
        @forelse($pendingInvoices as $invoice)<tr><td><strong>{{ $invoice->servicio->nombre }}</strong></td><td>{{ $invoice->fecha_vencimiento->format('d/m/Y') }}</td><td><span class="badge {{ $invoice->estado_actual === 'vencido' ? 'badge-danger' : 'badge-warning' }}">{{ ucfirst($invoice->estado_actual) }}</span></td><td class="amount">Bs {{ number_format($invoice->monto, 2) }}</td></tr>@empty<tr><td colspan="4" class="empty-cell">Sin facturas pendientes.</td></tr>@endforelse
        </tbody></table></div>
    </section>
    <section class="panel"><div class="panel-heading"><div><h2>Pagos recientes</h2><p>Comprobantes de tu cuenta</p></div><a class="text-link" href="{{ route('arrendatario.pagos') }}">Ver todos <i class="fa-solid fa-arrow-right"></i></a></div>
        @forelse($recentPayments as $payment)<a class="payment-row" href="{{ route('arrendatario.pagos.comprobante', $payment) }}"><span class="payment-check"><i class="fa-solid fa-check"></i></span><span>{{ $payment->concepto }}<small>{{ $payment->fecha_pago->format('d/m/Y') }}</small></span><strong>Bs {{ number_format($payment->monto, 2) }}</strong></a>@empty<p class="empty-cell">Aún no hay pagos en el historial.</p>@endforelse
    </section>
</div>
@endsection
