<?php

namespace App\Http\Controllers;

use App\Models\Arrendatario;
use App\Models\FacturaServicio;
use App\Models\HistorialCobro;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PagoController extends Controller
{
    private function toCents(mixed $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    public function index(): View
    {
        return view('admin.pagos.index', [
            'payments' => Pago::with(['arrendatario.user', 'facturaServicio.servicio', 'usuarioRegistrador'])
                ->latest('fecha_pago')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.pagos.create', [
            'tenants' => Arrendatario::with(['user', 'facturas.servicio'])
                ->where('deuda_actual', '>', 0)->orderBy('apellidos')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'arrendatario_id' => ['required', 'exists:arrendatarios,id'],
            'factura_servicio_id' => ['nullable', 'exists:facturas_servicios,id'],
            'monto' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'concepto' => ['required', 'string', 'max:255'],
            'metodo_pago' => ['required', 'in:efectivo,transferencia,tarjeta,otro'],
            'fecha_pago' => ['required', 'date'],
            'observacion' => ['nullable', 'string', 'max:2000'],
        ]);

        $payment = DB::transaction(function () use ($data, $request) {
            $tenant = Arrendatario::whereKey($data['arrendatario_id'])->lockForUpdate()->firstOrFail();
            // Importes en centavos (enteros) para evitar errores de redondeo con float.
            $amountCents = $this->toCents($data['monto']);
            $debtBeforePaymentCents = $this->toCents($tenant->deuda_actual);
            $amount = $amountCents / 100;

            if ($amountCents > $debtBeforePaymentCents) {
                throw ValidationException::withMessages([
                    'monto' => 'El monto del pago no puede superar la deuda actual.',
                ]);
            }

            $invoice = null;
            if (!empty($data['factura_servicio_id'])) {
                $invoice = FacturaServicio::whereKey($data['factura_servicio_id'])
                    ->where('arrendatario_id', $tenant->id)
                    ->lockForUpdate()
                    ->firstOrFail();
                if ($invoice->estado === 'pagado') {
                    throw ValidationException::withMessages([
                        'factura_servicio_id' => 'La factura seleccionada ya está pagada.',
                    ]);
                }

                $paidCents = $this->toCents($invoice->pagos()->sum('monto'));
                if ($amountCents > $this->toCents($invoice->monto) - $paidCents) {
                    throw ValidationException::withMessages([
                        'monto' => 'El pago supera el saldo de la factura seleccionada.',
                    ]);
                }
            }

            $payment = Pago::create($data + ['registrado_por' => $request->user()->id]);
            $tenant->deuda_actual = max(0, $debtBeforePaymentCents - $amountCents) / 100;
            $tenant->save();

            if ($invoice) {
                $totalPaidCents = $this->toCents($invoice->pagos()->sum('monto'));
                if ($totalPaidCents >= $this->toCents($invoice->monto)) {
                    $invoice->update(['estado' => 'pagado']);
                }
            } elseif ($amountCents >= $debtBeforePaymentCents) {
                $tenant->facturas()->where('estado', '!=', 'pagado')->update(['estado' => 'pagado']);
            }

            HistorialCobro::create([
                'arrendatario_id' => $tenant->id,
                'factura_servicio_id' => $invoice ? $invoice->id : null,
                'pago_id' => $payment->id,
                'monto' => $amount,
                'concepto' => $data['concepto'],
                'fecha' => $data['fecha_pago'],
                'tipo' => 'pago',
                'descripcion' => $data['observacion'] ?? null,
            ]);

            return $payment;
        });

        return redirect()->route('pagos.comprobante', $payment)
            ->with('success', 'Pago registrado correctamente.');
    }

    public function tenantIndex(): View
    {
        $tenant = auth()->user()->arrendatario;
        abort_unless($tenant, 404, 'No hay un perfil de arrendatario asociado a esta cuenta.');

        return view('tenant.pagos', [
            'payments' => $tenant->pagos()->with(['facturaServicio.servicio', 'usuarioRegistrador'])
                ->latest('fecha_pago')->paginate(20),
        ]);
    }

    public function receipt(Pago $pago): View
    {
        if (auth()->user()->role !== 'admin') {
            $tenant = auth()->user()->arrendatario;
            abort_unless($tenant && $pago->arrendatario_id === $tenant->id, 404);
        }

        return view('print.receipt', [
            'payment' => $pago->load(['arrendatario.user', 'facturaServicio.servicio', 'usuarioRegistrador']),
        ]);
    }
}
