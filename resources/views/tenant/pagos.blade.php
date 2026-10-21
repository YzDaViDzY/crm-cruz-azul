@extends('layouts.app')
@section('title', 'Mis pagos')
@section('section', 'Mis pagos')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MI CUENTA</p><h1>Mis pagos</h1><p class="muted">Descarga o imprime tus comprobantes.</p></div></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Concepto</th><th>Método</th><th>Monto</th><th></th></tr></thead><tbody>@forelse($payments as $payment)<tr><td>{{ $payment->fecha_pago->format('d/m/Y') }}</td><td>{{ $payment->concepto }}</td><td>{{ ucfirst($payment->metodo_pago) }}</td><td class="amount">Bs {{ number_format($payment->monto, 2) }}</td><td><a class="button button-small button-secondary" href="{{ route('arrendatario.pagos.comprobante', $payment) }}"><i class="fa-solid fa-receipt"></i> Comprobante</a></td></tr>@empty<tr><td colspan="5" class="empty-cell">Todavía no tienes pagos registrados.</td></tr>@endforelse</tbody></table></div><div class="pagination-wrap">{{ $payments->links() }}</div></section>
@endsection
