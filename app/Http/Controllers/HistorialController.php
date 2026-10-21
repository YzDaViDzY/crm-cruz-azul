<?php

namespace App\Http\Controllers;

use App\Models\HistorialCobro;
use Illuminate\View\View;

class HistorialController extends Controller
{
    public function index(): View
    {
        return view('admin.historial', [
            'entries' => HistorialCobro::with('arrendatario.user')->latest('fecha')->paginate(25),
        ]);
    }

    public function tenantIndex(): View
    {
        $tenant = auth()->user()->arrendatario;
        abort_unless($tenant, 404, 'No hay un perfil de arrendatario asociado a esta cuenta.');

        return view('tenant.historial', [
            'entries' => $tenant->historialCobros()->latest('fecha')->paginate(25),
        ]);
    }
}
