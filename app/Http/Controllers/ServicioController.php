<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServicioController extends Controller
{
    public function index(): View
    {
        return view('admin.servicios.index', [
            'services' => Servicio::withCount('facturas')->orderBy('nombre')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.servicios.create', ['service' => new Servicio()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:servicios,nombre'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'monto_base' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);
        $data['activo'] = $request->boolean('activo');
        Servicio::create($data);

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio creado.');
    }

    public function edit(Servicio $servicio): View
    {
        return view('admin.servicios.edit', ['service' => $servicio]);
    }

    public function update(Request $request, Servicio $servicio): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique('servicios', 'nombre')->ignore($servicio->id)],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'monto_base' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);
        $data['activo'] = $request->boolean('activo');
        $servicio->update($data);

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio actualizado.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        if ($servicio->facturas()->exists()) {
            $servicio->update(['activo' => false]);
            return redirect()->route('admin.servicios.index')
                ->with('success', 'El servicio tiene facturas históricas y fue desactivado para conservarlas.');
        }

        $servicio->delete();
        return redirect()->route('admin.servicios.index')->with('success', 'Servicio eliminado.');
    }

    public function tenantIndex(): View
    {
        $tenant = auth()->user()->arrendatario;
        abort_unless($tenant, 404, 'No hay un perfil de arrendatario asociado a esta cuenta.');

        return view('tenant.servicios', [
            'invoices' => $tenant->facturas()->with('servicio')->latest('fecha_emision')->get(),
        ]);
    }
}
