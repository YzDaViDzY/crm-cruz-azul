<?php

namespace App\Http\Controllers;

use App\Models\Arrendatario;
use App\Models\FacturaServicio;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect()
    {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('arrendatario.dashboard');
    }

    public function admin(): View
    {
        return view('dashboard.admin', [
            'tenantCount' => Arrendatario::count(),
            'tenantsWithDebt' => Arrendatario::where('deuda_actual', '>', 0)->count(),
            'tenantsWithoutDebt' => Arrendatario::where('deuda_actual', '<=', 0)->count(),
            'totalDebt' => Arrendatario::sum('deuda_actual'),
            'monthlyPayments' => Pago::whereMonth('fecha_pago', now()->month)
                ->whereYear('fecha_pago', now()->year)->sum('monto'),
            'pendingInvoices' => FacturaServicio::where('estado', '!=', 'pagado')->count(),
            'overdueInvoices' => FacturaServicio::where('estado', '!=', 'pagado')
                ->whereDate('fecha_vencimiento', '<', today())->count(),
            'upcomingInvoices' => FacturaServicio::with(['arrendatario', 'servicio'])
                ->where('estado', '!=', 'pagado')
                ->whereBetween('fecha_vencimiento', [today(), today()->addDays(7)])
                ->orderBy('fecha_vencimiento')->limit(6)->get(),
            'recentPayments' => Pago::with(['arrendatario', 'usuarioRegistrador'])
                ->latest('fecha_pago')->limit(6)->get(),
            'highestDebt' => Arrendatario::with('user')->where('deuda_actual', '>', 0)
                ->orderByDesc('deuda_actual')->limit(5)->get(),
        ]);
    }

    public function tenant(): View
    {
        $tenant = Auth::user()->arrendatario;
        abort_unless($tenant, 404, 'No hay un perfil de arrendatario asociado a esta cuenta.');

        return view('dashboard.inquilino', [
            'tenant' => $tenant,
            'pendingInvoices' => $tenant->facturas()->with('servicio')
                ->where('estado', '!=', 'pagado')->orderBy('fecha_vencimiento')->get(),
            'nextInvoice' => $tenant->facturas()->where('estado', '!=', 'pagado')
                ->whereDate('fecha_vencimiento', '>=', today())->orderBy('fecha_vencimiento')->first(),
            'recentPayments' => $tenant->pagos()->with('usuarioRegistrador')
                ->latest('fecha_pago')->limit(5)->get(),
        ]);
    }
}
