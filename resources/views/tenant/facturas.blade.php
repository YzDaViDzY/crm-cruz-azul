@extends('layouts.app')
@section('title', 'Mis facturas')
@section('section', 'Mis facturas')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MI CUENTA</p><h1>Mis facturas</h1><p class="muted">Consulta tus servicios, fechas y estados de pago.</p></div></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Servicio</th><th>Emisión</th><th>Vencimiento</th><th>Estado</th><th>Importe</th></tr></thead><tbody>@forelse($invoices as $invoice)<tr><td><strong>{{ $invoice->servicio->nombre }}</strong><small>{{ $invoice->observacion }}</small></td><td>{{ $invoice->fecha_emision->format('d/m/Y') }}</td><td>{{ $invoice->fecha_vencimiento->format('d/m/Y') }}</td><td><span class="badge {{ $invoice->estado_actual === 'pagado' ? 'badge-success' : ($invoice->estado_actual === 'vencido' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($invoice->estado_actual) }}</span></td><td class="amount">Bs {{ number_format($invoice->monto, 2) }}</td></tr>@empty<tr><td colspan="5" class="empty-cell">No tienes facturas registradas.</td></tr>@endforelse</tbody></table></div><div class="pagination-wrap">{{ $invoices->links() }}</div></section>
@endsection
