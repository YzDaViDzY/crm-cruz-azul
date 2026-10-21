<?php

namespace Database\Seeders;

use App\Models\Arrendatario;
use App\Models\FacturaServicio;
use App\Models\HistorialCobro;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('El seeder de demostración (credenciales fijas) no debe ejecutarse en producción.');
        }

        if (!config('app.seed_demo_data')) {
            throw new \RuntimeException('Seeder de demostración bloqueado: define SEED_DEMO_DATA=true en .env solo en desarrollo local.');
        }

        // firstOrCreate: nunca modifica ni sobrescribe usuarios que ya existen.
        $admin = User::firstOrCreate(
            ['email' => 'admin@cruzazul.com'],
            ['name' => 'Administrador', 'password' => Hash::make('Admin123*'), 'role' => 'admin']
        );

        $tenantUser = User::firstOrCreate(
            ['email' => 'arrendatario@cruzazul.com'],
            ['name' => 'María Pérez', 'password' => Hash::make('Arrendatario123*'), 'role' => 'arrendatario']
        );

        $tenant = Arrendatario::firstOrCreate(
            ['user_id' => $tenantUser->id],
            [
                'nombres' => 'María',
                'apellidos' => 'Pérez',
                'ci' => '1234567',
                'telefono' => '70000000',
                'direccion' => 'Condominio Cruz Azul',
                'numero_departamento' => 'A-101',
                'deuda_actual' => 0,
                'estado' => 'activo',
                'fecha_ingreso' => today()->subMonths(6),
            ]
        );

        foreach (['Agua', 'Electricidad', 'Internet', 'Expensas', 'Limpieza'] as $name) {
            Servicio::firstOrCreate(
                ['nombre' => $name],
                ['descripcion' => 'Servicio de '.$name, 'monto_base' => 100, 'activo' => true]
            );
        }

        $water = Servicio::where('nombre', 'Agua')->firstOrFail();
        $invoice = FacturaServicio::firstOrCreate(
            ['arrendatario_id' => $tenant->id, 'servicio_id' => $water->id, 'fecha_emision' => today()->startOfMonth()],
            ['monto' => 100, 'fecha_vencimiento' => today()->addDays(5), 'estado' => 'pendiente', 'observacion' => 'Factura de demostración']
        );

        if ($invoice->wasRecentlyCreated) {
            $tenant->increment('deuda_actual', $invoice->monto);
            HistorialCobro::create([
                'arrendatario_id' => $tenant->id,
                'factura_servicio_id' => $invoice->id,
                'monto' => $invoice->monto,
                'concepto' => 'Factura de Agua',
                'fecha' => now(),
                'tipo' => 'cargo',
                'descripcion' => 'Factura de demostración',
            ]);
        }

        $electricity = Servicio::where('nombre', 'Electricidad')->firstOrFail();
        $paidInvoice = FacturaServicio::firstOrCreate(
            [
                'arrendatario_id' => $tenant->id,
                'servicio_id' => $electricity->id,
                'fecha_emision' => today()->subMonth()->startOfMonth(),
            ],
            [
                'monto' => 75,
                'fecha_vencimiento' => today()->subDays(10),
                'estado' => 'pagado',
                'observacion' => 'Factura pagada de demostración',
            ]
        );

        if ($paidInvoice->wasRecentlyCreated) {
            $payment = Pago::create([
                'arrendatario_id' => $tenant->id,
                'factura_servicio_id' => $paidInvoice->id,
                'registrado_por' => $admin->id,
                'monto' => $paidInvoice->monto,
                'concepto' => 'Pago de Electricidad',
                'metodo_pago' => 'transferencia',
                'fecha_pago' => today()->subDays(12)->setTime(10, 0),
                'observacion' => 'Pago de demostración',
            ]);

            HistorialCobro::create([
                'arrendatario_id' => $tenant->id,
                'factura_servicio_id' => $paidInvoice->id,
                'monto' => $paidInvoice->monto,
                'concepto' => 'Factura de Electricidad',
                'fecha' => $paidInvoice->created_at,
                'tipo' => 'cargo',
                'descripcion' => $paidInvoice->observacion,
            ]);
            HistorialCobro::create([
                'arrendatario_id' => $tenant->id,
                'factura_servicio_id' => $paidInvoice->id,
                'pago_id' => $payment->id,
                'monto' => $payment->monto,
                'concepto' => $payment->concepto,
                'fecha' => $payment->fecha_pago,
                'tipo' => 'pago',
                'descripcion' => $payment->observacion,
            ]);
        }

        $lateUser = User::firstOrCreate(
            ['email' => 'arrendatario.vencido@cruzazul.com'],
            ['name' => 'Carlos Rojas', 'password' => Hash::make('Arrendatario123*'), 'role' => 'arrendatario']
        );
        $lateTenant = Arrendatario::firstOrCreate(
            ['user_id' => $lateUser->id],
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Rojas',
                'ci' => '7654321',
                'telefono' => '70000001',
                'direccion' => 'Condominio Cruz Azul',
                'numero_departamento' => 'B-202',
                'deuda_actual' => 0,
                'estado' => 'activo',
                'fecha_ingreso' => today()->subMonths(4),
            ]
        );
        $lateInvoice = FacturaServicio::firstOrCreate(
            [
                'arrendatario_id' => $lateTenant->id,
                'servicio_id' => $water->id,
                'fecha_emision' => today()->subMonth()->startOfMonth(),
            ],
            [
                'monto' => 60,
                'fecha_vencimiento' => today()->subDays(5),
                'estado' => 'pendiente',
                'observacion' => 'Factura vencida de demostración',
            ]
        );
        if ($lateInvoice->wasRecentlyCreated) {
            $lateTenant->increment('deuda_actual', $lateInvoice->monto);
            HistorialCobro::create([
                'arrendatario_id' => $lateTenant->id,
                'factura_servicio_id' => $lateInvoice->id,
                'monto' => $lateInvoice->monto,
                'concepto' => 'Factura vencida de Agua',
                'fecha' => now(),
                'tipo' => 'cargo',
                'descripcion' => $lateInvoice->observacion,
            ]);
        }
    }
}
