@extends('layouts.app')
@section('title', 'Mi historial')
@section('section', 'Mi historial')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MI CUENTA</p><h1>Mi historial</h1><p class="muted">Movimientos de cargos y pagos asociados a tu perfil.</p></div></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Tipo</th><th>Concepto</th><th>Descripción</th><th>Monto</th></tr></thead><tbody>@forelse($entries as $entry)<tr><td>{{ $entry->fecha->format('d/m/Y H:i') }}</td><td><span class="badge {{ $entry->tipo === 'pago' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($entry->tipo) }}</span></td><td>{{ $entry->concepto }}</td><td>{{ $entry->descripcion ?: '—' }}</td><td class="amount">Bs {{ number_format($entry->monto, 2) }}</td></tr>@empty<tr><td colspan="5" class="empty-cell">No hay movimientos todavía.</td></tr>@endforelse</tbody></table></div><div class="pagination-wrap">{{ $entries->links() }}</div></section>
@endsection
