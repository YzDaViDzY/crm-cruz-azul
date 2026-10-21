<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Arrendatario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'ci',
        'telefono',
        'direccion',
        'numero_departamento',
        'deuda_actual',
        'estado',
        'fecha_ingreso',
    ];

    protected $casts = [
        'deuda_actual' => 'decimal:2',
        'fecha_ingreso' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(FacturaServicio::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function historialCobros(): HasMany
    {
        return $this->hasMany(HistorialCobro::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres.' '.$this->apellidos);
    }
}
