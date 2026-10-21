@extends('layouts.app')
@section('title', 'Vencimientos')
@section('section', 'Vencimientos')
@section('content')
<div class="page-heading"><div><p class="eyebrow">ALERTAS DE COBRO</p><h1>Vencimientos</h1><p class="muted">Facturas vencidas y las que vencen durante los próximos siete días.</p></div></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Arrendatario</th><th>Departamento</th><th>Servicio</th><th>Fecha límite</th><th>Estado</th><th>Monto</th></tr></thead><tbody>@forelse($invoices as $invoice)<tr><td><strong>{{ $invoice->arrendatario->nombre_completo }}</strong><small>{{ $invoice->arrendatario->user->email }}</small></td><td>{{ $invoice->arrendatario->numero_departamento }}</td><td>{{ $invoice->servicio->nombre }}</td><td>{{ $invoice->fecha_vencimiento->format('d/m/Y') }}</td><td><span class="badge {{ $invoice->estado_actual === 'vencido' ? 'badge-danger' : 'badge-warning' }}">{{ $invoice->estado_actual === 'vencido' ? 'Vencido' : ($invoice->fecha_vencimiento->isToday() ? 'Vence hoy' : 'Próximo') }}</span></td><td class="amount">Bs {{ number_format($invoice->monto, 2) }}</td></tr>@empty<tr><td colspan="6" class="empty-cell">No hay facturas vencidas ni próximas a vencer.</td></tr>@endforelse</tbody></table></div><div class="pagination-wrap">{{ $invoices->links() }}</div></section>
@endsection
