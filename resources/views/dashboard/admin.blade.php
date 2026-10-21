@extends('layouts.app')
@section('title', 'Dashboard | Condominio Cruz Azul')
@section('section', 'Dashboard')
@section('content')
<div class="page-heading"><div><p class="eyebrow">VISTA GENERAL</p><h1>Panel de administración</h1><p class="muted">Resumen actualizado de la actividad del condominio.</p></div><a class="button button-primary" href="{{ route('admin.facturas.create') }}"><i class="fa-solid fa-plus"></i> Generar factura</a></div>
<div class="stats-grid">
    <article class="stat-card"><span class="stat-icon blue"><i class="fa-solid fa-users"></i></span><div><small>Arrendatarios</small><strong>{{ $tenantCount }}</strong><span class="stat-note">{{ $tenantsWithoutDebt }} sin deuda</span></div></article>
    <article class="stat-card"><span class="stat-icon orange"><i class="fa-solid fa-triangle-exclamation"></i></span><div><small>Con deuda</small><strong>{{ $tenantsWithDebt }}</strong><span class="stat-note">cuentas pendientes</span></div></article>
    <article class="stat-card"><span class="stat-icon red"><i class="fa-solid fa-wallet"></i></span><div><small>Deuda total</small><strong>Bs {{ number_format($totalDebt, 2) }}</strong><span class="stat-note">saldo por cobrar</span></div></article>
    <article class="stat-card"><span class="stat-icon green"><i class="fa-solid fa-circle-check"></i></span><div><small>Pagos del mes</small><strong>Bs {{ number_format($monthlyPayments, 2) }}</strong><span class="stat-note">{{ now()->translatedFormat('F Y') }}</span></div></article>
</div>
<div class="stats-grid stats-grid-small">
    <article class="mini-stat"><span>Facturas pendientes</span><strong>{{ $pendingInvoices }}</strong></article>
    <article class="mini-stat"><span>Facturas vencidas</span><strong class="text-danger">{{ $overdueInvoices }}</strong></article>
    <article class="mini-stat"><span>Vencen en 7 días</span><strong>{{ $upcomingInvoices->count() }}</strong></article>
</div>
<div class="content-grid">
    <section class="panel"><div class="panel-heading"><div><h2>Pagos recientes</h2><p>Últimos movimientos registrados</p></div><a class="text-link" href="{{ route('admin.pagos.index') }}">Ver todos <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="table-wrap"><table><thead><tr><th>Arrendatario</th><th>Fecha</th><th>Método</th><th>Monto</th></tr></thead><tbody>
        @forelse($recentPayments as $payment)<tr><td><strong>{{ $payment->arrendatario->nombre_completo }}</strong><small>Depto. {{ $payment->arrendatario->numero_departamento }}</small></td><td>{{ $payment->fecha_pago->format('d/m/Y') }}</td><td>{{ ucfirst($payment->metodo_pago) }}</td><td class="amount">Bs {{ number_format($payment->monto, 2) }}</td></tr>@empty<tr><td colspan="4" class="empty-cell">Todavía no hay pagos registrados.</td></tr>@endforelse
        </tbody></table></div>
    </section>
    <section class="panel"><div class="panel-heading"><div><h2>Mayor deuda</h2><p>Arrendatarios con saldo pendiente</p></div></div>
        <div class="rank-list">@forelse($highestDebt as $tenant)<a class="rank-item" href="{{ route('admin.arrendatarios.show', $tenant) }}"><span class="avatar">{{ mb_substr($tenant->nombres, 0, 1) }}</span><span class="rank-name">{{ $tenant->nombre_completo }}<small>Depto. {{ $tenant->numero_departamento }}</small></span><strong>Bs {{ number_format($tenant->deuda_actual, 2) }}</strong></a>@empty<p class="empty-cell">No hay deudas pendientes.</p>@endforelse</div>
    </section>
</div>
<section class="panel section-gap"><div class="panel-heading"><div><h2>Próximos vencimientos</h2><p>Facturas que vencen dentro de los próximos 7 días</p></div><a class="text-link" href="{{ route('admin.facturas.index') }}">Ver facturas <i class="fa-solid fa-arrow-right"></i></a></div>
    <div class="table-wrap"><table><thead><tr><th>Arrendatario</th><th>Servicio</th><th>Vencimiento</th><th>Importe</th></tr></thead><tbody>
    @forelse($upcomingInvoices as $invoice)<tr><td>{{ $invoice->arrendatario->nombre_completo }}</td><td>{{ $invoice->servicio->nombre }}</td><td><span class="badge badge-warning">{{ $invoice->fecha_vencimiento->format('d/m/Y') }}</span></td><td class="amount">Bs {{ number_format($invoice->monto, 2) }}</td></tr>@empty<tr><td colspan="4" class="empty-cell">No hay vencimientos próximos.</td></tr>@endforelse
    </tbody></table></div>
</section>
@endsection
