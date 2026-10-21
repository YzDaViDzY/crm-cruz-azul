@extends('layouts.app')
@section('title', 'Arrendatarios')
@section('section', 'Arrendatarios')
@section('content')
<div class="page-heading"><div><p class="eyebrow">GESTIÓN DE PERSONAS</p><h1>Arrendatarios</h1><p class="muted">Consulta cuentas, datos de contacto y saldos.</p></div><a class="button button-primary" href="{{ route('admin.arrendatarios.create') }}"><i class="fa-solid fa-user-plus"></i> Nuevo arrendatario</a></div>
<section class="panel">
    <form class="filter-bar" method="GET"><label class="search-field"><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ $search }}" placeholder="Nombre, CI, departamento o correo"></label>
        <select name="deuda"><option value="">Todas las cuentas</option><option value="si" @selected(request('deuda') === 'si')>Con deuda</option><option value="no" @selected(request('deuda') === 'no')>Sin deuda</option></select>
        <select name="estado"><option value="">Todos los estados</option><option value="activo" @selected(request('estado') === 'activo')>Activo</option><option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option></select>
        <button class="button button-secondary" type="submit">Filtrar</button>
    </form>
    <div class="table-wrap"><table><thead><tr><th>Arrendatario</th><th>Departamento</th><th>CI</th><th>Estado</th><th>Deuda</th><th></th></tr></thead><tbody>
        @forelse($tenants as $tenant)<tr><td><strong>{{ $tenant->nombre_completo }}</strong><small>{{ $tenant->user->email }}</small></td><td>{{ $tenant->numero_departamento }}</td><td>{{ $tenant->ci }}</td><td><span class="badge {{ $tenant->estado === 'activo' ? 'badge-success' : 'badge-neutral' }}">{{ ucfirst($tenant->estado) }}</span></td><td class="amount">Bs {{ number_format($tenant->deuda_actual, 2) }}</td><td><a class="icon-button" title="Ver detalle" href="{{ route('admin.arrendatarios.show', $tenant) }}"><i class="fa-regular fa-eye"></i></a><a class="icon-button" title="Editar" href="{{ route('admin.arrendatarios.edit', $tenant) }}"><i class="fa-regular fa-pen-to-square"></i></a></td></tr>
        @empty<tr><td colspan="6" class="empty-cell">No se encontraron arrendatarios.</td></tr>@endforelse
    </tbody></table></div>
    <div class="pagination-wrap">{{ $tenants->links() }}</div>
</section>
@endsection
