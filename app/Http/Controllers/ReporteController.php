<?php

namespace App\Http\Controllers;

use App\Models\Arrendatario;
use App\Models\FacturaServicio;
use App\Models\Pago;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(): View
    {
        return view('admin.reportes', [
            'tenants' => Arrendatario::with('user')->orderBy('apellidos')->get(),
            'invoices' => FacturaServicio::with(['arrendatario.user', 'servicio'])
                ->where('estado', '!=', 'pagado')->orderBy('fecha_vencimiento')->get(),
            'payments' => Pago::with(['arrendatario.user', 'usuarioRegistrador'])
                ->latest('fecha_pago')->limit(50)->get(),
        ]);
    }

    public function tenant(Arrendatario $arrendatario): View
    {
        return view('print.statement', [
            'tenant' => $arrendatario->load(['user', 'facturas.servicio', 'pagos.usuarioRegistrador', 'historialCobros']),
        ]);
    }

    public function tenantSelf(): View
    {
        $tenant = auth()->user()->arrendatario;
        abort_unless($tenant, 404, 'No hay un perfil de arrendatario asociado a esta cuenta.');

        return view('print.statement', [
            'tenant' => $tenant->load(['user', 'facturas.servicio', 'pagos.usuarioRegistrador', 'historialCobros']),
        ]);
    }
}
