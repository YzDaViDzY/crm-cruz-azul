<?php

namespace Tests\Feature;

use App\Models\Arrendatario;
use App\Models\FacturaServicio;
use App\Models\HistorialCobro;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmAccessAndPaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_arrendatario_cannot_open_admin_area_or_another_tenant_receipt()
    {
        $tenant = $this->createTenant('tenant@example.com', 'A-101');
        $otherTenant = $this->createTenant('other@example.com', 'B-202');
        $payment = Pago::create([
            'arrendatario_id' => $otherTenant->id,
            'monto' => 20,
            'concepto' => 'Pago de prueba',
            'metodo_pago' => 'efectivo',
            'fecha_pago' => now(),
        ]);

        $this->actingAs($tenant->user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->get(route('pagos.comprobante', $payment))->assertNotFound();
    }

    public function test_arrendatario_can_print_only_their_own_receipt()
    {
        $tenant = $this->createTenant('tenant@example.com', 'A-101');
        $payment = Pago::create([
            'arrendatario_id' => $tenant->id,
            'monto' => 25,
            'concepto' => 'Pago de prueba',
            'metodo_pago' => 'efectivo',
            'fecha_pago' => now(),
        ]);

        $this->actingAs($tenant->user)
            ->get(route('pagos.comprobante', $payment))
            ->assertOk()
            ->assertSee('Pago de prueba');
    }

    public function test_admin_payment_updates_debt_invoice_and_history_in_one_workflow()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tenant = $this->createTenant('tenant@example.com', 'A-101', 50);
        $service = Servicio::create([
            'nombre' => 'Agua',
            'monto_base' => 50,
            'activo' => true,
        ]);
        $invoice = FacturaServicio::create([
            'arrendatario_id' => $tenant->id,
            'servicio_id' => $service->id,
            'monto' => 50,
            'fecha_emision' => today(),
            'fecha_vencimiento' => today()->addDays(5),
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.pagos.store'), [
            'arrendatario_id' => $tenant->id,
            'factura_servicio_id' => $invoice->id,
            'monto' => 50,
            'concepto' => 'Pago de agua',
            'metodo_pago' => 'transferencia',
            'fecha_pago' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $this->assertSame('0.00', $tenant->fresh()->deuda_actual);
        $this->assertSame('pagado', $invoice->fresh()->estado);
        $this->assertDatabaseHas('pagos', [
            'arrendatario_id' => $tenant->id,
            'factura_servicio_id' => $invoice->id,
            'monto' => 50,
        ]);
        $this->assertSame(1, HistorialCobro::where('pago_id', Pago::first()->id)->count());
    }

    public function test_inactive_arrendatario_cannot_log_in()
    {
        $tenant = $this->createTenant('inactive@example.com', 'C-303');
        $tenant->update(['estado' => 'inactivo']);

        $this->post(route('login.store'), [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_payment_above_debt_returns_validation_error_instead_of_abort()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tenant = $this->createTenant('tenant@example.com', 'A-101', 30);

        $this->actingAs($admin)->post(route('admin.pagos.store'), [
            'arrendatario_id' => $tenant->id,
            'monto' => 50,
            'concepto' => 'Pago excedido',
            'metodo_pago' => 'efectivo',
            'fecha_pago' => now()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors('monto');

        $this->assertSame(0, Pago::count());
        $this->assertSame('30.00', $tenant->fresh()->deuda_actual);
    }

    public function test_arrendatario_with_invoices_cannot_be_deleted()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tenant = $this->createTenant('tenant@example.com', 'A-101', 50);
        $service = Servicio::create(['nombre' => 'Agua', 'monto_base' => 50, 'activo' => true]);
        FacturaServicio::create([
            'arrendatario_id' => $tenant->id,
            'servicio_id' => $service->id,
            'monto' => 50,
            'fecha_emision' => today(),
            'fecha_vencimiento' => today()->addDays(5),
            'estado' => 'pendiente',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.arrendatarios.destroy', $tenant))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('arrendatarios', ['id' => $tenant->id]);
    }

    private function createTenant(string $email, string $department, float $debt = 0): Arrendatario
    {
        $user = User::factory()->create([
            'email' => $email,
            'role' => 'arrendatario',
        ]);

        return Arrendatario::create([
            'user_id' => $user->id,
            'nombres' => 'Demo',
            'apellidos' => 'Arrendatario',
            'ci' => $department,
            'numero_departamento' => $department,
            'deuda_actual' => $debt,
            'estado' => 'activo',
        ]);
    }
}
