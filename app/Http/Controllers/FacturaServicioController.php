<?php

namespace App\Http\Controllers;

use App\Models\Arrendatario;
use App\Models\FacturaServicio;
use App\Models\HistorialCobro;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacturaServicioController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'estado' => ['nullable', 'in:pendiente,pagado,vencido'],
            'servicio_id' => ['nullable', 'integer', 'exists:servicios,id'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);
        $query = FacturaServicio::with(['arrendatario.user', 'servicio']);
        $status = $filters['estado'] ?? null;

        if (in_array($status, ['pendiente', 'pagado', 'vencido'], true)) {
            if ($status === 'vencido') {
                $query->where('estado', '!=', 'pagado')->whereDate('fecha_vencimiento', '<', today());
            } elseif ($status === 'pendiente') {
                $query->where('estado', '!=', 'pagado')->whereDate('fecha_vencimiento', '>=', today());
            } else {
                $query->where('estado', $status);
            }
        }
        if (!empty($filters['servicio_id'])) {
            $query->where('servicio_id', $filters['servicio_id']);
        }
        if (!empty($filters['desde'])) {
            $query->whereDate('fecha_emision', '>=', $filters['desde']);
        }
        if (!empty($filters['hasta'])) {
            $query->whereDate('fecha_emision', '<=', $filters['hasta']);
        }

        return view('admin.facturas.index', [
            'invoices' => $query->latest('fecha_emision')->paginate(20)->withQueryString(),
            'services' => Servicio::orderBy('nombre')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.facturas.create', [
            'tenants' => Arrendatario::with('user')->where('estado', 'activo')->orderBy('apellidos')->get(),
            'services' => Servicio::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'arrendatario_id' => ['required', Rule::exists('arrendatarios', 'id')->where('estado', 'activo')],
            'servicio_id' => ['required', Rule::exists('servicios', 'id')->where('activo', true)],
            'monto' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date', 'after_or_equal:fecha_emision'],
            'observacion' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data) {
            $tenant = Arrendatario::whereKey($data['arrendatario_id'])->lockForUpdate()->firstOrFail();
            $invoice = FacturaServicio::create($data + ['estado' => 'pendiente']);
            $tenant->deuda_actual = (float) $tenant->deuda_actual + (float) $invoice->monto;
            $tenant->save();

            HistorialCobro::create([
                'arrendatario_id' => $tenant->id,
                'factura_servicio_id' => $invoice->id,
                'monto' => $invoice->monto,
                'concepto' => 'Factura: '.$invoice->servicio->nombre,
                'fecha' => now(),
                'tipo' => 'cargo',
                'descripcion' => $invoice->observacion,
            ]);
        });

        return redirect()->route('admin.facturas.index')->with('success', 'Factura creada y deuda actualizada.');
    }

    public function tenantIndex(): View
    {
        $tenant = auth()->user()->arrendatario;
        abort_unless($tenant, 404, 'No hay un perfil de arrendatario asociado a esta cuenta.');

        return view('tenant.facturas', [
            'invoices' => $tenant->facturas()->with('servicio')->latest('fecha_emision')->paginate(20),
        ]);
    }

    public function dueIndex(): View
    {
        return view('admin.vencimientos', [
            'invoices' => FacturaServicio::with(['arrendatario.user', 'servicio'])
                ->where('estado', '!=', 'pagado')
                ->whereDate('fecha_vencimiento', '<=', today()->addDays(7))
                ->orderBy('fecha_vencimiento')
                ->paginate(25),
        ]);
    }
}
