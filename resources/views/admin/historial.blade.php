@extends('layouts.app')
@section('title', 'Historial de cobros')
@section('section', 'Historial')
@section('content')
<div class="page-heading"><div><p class="eyebrow">REGISTRO DE MOVIMIENTOS</p><h1>Historial de cobros</h1><p class="muted">Cargos generados y pagos recibidos.</p></div></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Arrendatario</th><th>Tipo</th><th>Concepto</th><th>Detalle</th><th>Monto</th></tr></thead><tbody>@forelse($entries as $entry)<tr><td>{{ $entry->fecha->format('d/m/Y H:i') }}</td><td><strong>{{ $entry->arrendatario->nombre_completo }}</strong><small>Depto. {{ $entry->arrendatario->numero_departamento }}</small></td><td><span class="badge {{ $entry->tipo === 'pago' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($entry->tipo) }}</span></td><td>{{ $entry->concepto }}</td><td>{{ $entry->descripcion ?: '—' }}</td><td class="amount">Bs {{ number_format($entry->monto, 2) }}</td></tr>@empty<tr><td colspan="6" class="empty-cell">Sin movimientos.</td></tr>@endforelse</tbody></table></div><div class="pagination-wrap">{{ $entries->links() }}</div></section>
@endsection
