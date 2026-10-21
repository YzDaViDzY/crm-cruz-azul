<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'arrendatario_id',
        'factura_servicio_id',
        'registrado_por',
        'monto',
        'fecha_pago',
        'concepto',
        'metodo_pago',
        'observacion',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function arrendatario(): BelongsTo
    {
        return $this->belongsTo(Arrendatario::class);
    }

    public function facturaServicio(): BelongsTo
    {
        return $this->belongsTo(FacturaServicio::class);
    }

    public function usuarioRegistrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
