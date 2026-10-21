<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacturaServicio extends Model
{
    use HasFactory;

    protected $table = 'facturas_servicios';

    protected $fillable = [
        'arrendatario_id',
        'servicio_id',
        'monto',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function arrendatario(): BelongsTo
    {
        return $this->belongsTo(Arrendatario::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'factura_servicio_id');
    }

    public function getEstadoActualAttribute(): string
    {
        if ($this->estado === 'pagado') {
            return 'pagado';
        }

        return $this->fecha_vencimiento->isPast() && !$this->fecha_vencimiento->isToday()
            ? 'vencido'
            : 'pendiente';
    }
}
