@extends('layouts.app')
@section('title', 'Pagos')
@section('section', 'Pagos')
@section('content')
<div class="page-heading"><div><p class="eyebrow">INGRESOS REGISTRADOS</p><h1>Pagos</h1><p class="muted">Historial de pagos y comprobantes.</p></div><a class="button button-primary" href="{{ route('admin.pagos.create') }}"><i class="fa-solid fa-plus"></i> Registrar pago</a></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Comprobante</th><th>Arrendatario</th><th>Fecha</th><th>Concepto</th><th>Método</th><th>Monto</th><th></th></tr></thead><tbody>@forelse($payments as $payment)<tr><td>#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td><td><strong>{{ $payment->arrendatario->nombre_completo }}</strong><small>Depto. {{ $payment->arrendatario->numero_departamento }}</small></td><td>{{ $payment->fecha_pago->format('d/m/Y') }}</td><td>{{ $payment->concepto }}</td><td>{{ ucfirst($payment->metodo_pago) }}</td><td class="amount">Bs {{ number_format($payment->monto, 2) }}</td><td><a class="icon-button" href="{{ route('pagos.comprobante', $payment) }}" title="Ver comprobante"><i class="fa-solid fa-receipt"></i></a></td></tr>@empty<tr><td colspan="7" class="empty-cell">No hay pagos registrados.</td></tr>@endforelse</tbody></table></div><div class="pagination-wrap">{{ $payments->links() }}</div></section>
@endsection
