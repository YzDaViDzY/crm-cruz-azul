<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'monto_base', 'activo'];

    protected $casts = [
        'monto_base' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function facturas(): HasMany
    {
        return $this->hasMany(FacturaServicio::class);
    }
}
