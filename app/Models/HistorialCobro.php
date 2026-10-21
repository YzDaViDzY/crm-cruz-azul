<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialCobro extends Model
{
    use HasFactory;

    protected $fillable = [
        'arrendatario_id',
        'factura_servicio_id',
        'pago_id',
        'monto',
        'concepto',
        'fecha',
        'tipo',
        'descripcion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public function arrendatario(): BelongsTo
    {
        return $this->belongsTo(Arrendatario::class);
    }

    public function facturaServicio(): BelongsTo
    {
        return $this->belongsTo(FacturaServicio::class);
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }
}
