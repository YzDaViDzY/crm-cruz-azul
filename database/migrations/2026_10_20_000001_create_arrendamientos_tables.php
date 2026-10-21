<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('arrendatarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('ci')->unique();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('numero_departamento')->unique();
            $table->decimal('deuda_actual', 12, 2)->default(0);
            $table->string('estado')->default('activo');
            $table->date('fecha_ingreso')->nullable();
            $table->timestamps();
        });

        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('monto_base', 12, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('facturas_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arrendatario_id')->constrained('arrendatarios')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->restrictOnDelete();
            $table->decimal('monto', 12, 2);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->string('estado')->default('pendiente');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->index(['estado', 'fecha_vencimiento']);
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arrendatario_id')->constrained('arrendatarios')->restrictOnDelete();
            $table->foreignId('factura_servicio_id')->nullable()->constrained('facturas_servicios')->nullOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('monto', 12, 2);
            $table->string('concepto');
            $table->string('metodo_pago');
            $table->dateTime('fecha_pago');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        Schema::create('historial_cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arrendatario_id')->constrained('arrendatarios')->cascadeOnDelete();
            $table->foreignId('factura_servicio_id')->nullable()->constrained('facturas_servicios')->nullOnDelete();
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->decimal('monto', 12, 2);
            $table->string('concepto');
            $table->dateTime('fecha');
            $table->string('tipo');
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->index(['arrendatario_id', 'fecha']);
        });

        DB::table('users')->where('role', 'inquilino')->update(['role' => 'arrendatario']);
    }

    public function down()
    {
        Schema::dropIfExists('historial_cobros');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('facturas_servicios');
        Schema::dropIfExists('servicios');
        Schema::dropIfExists('arrendatarios');
    }
};
