<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'cliente_id', 'user_id', 'tipo_comprobante', 'serie',
        'correlativo', 'subtotal', 'igv', 'total', 'total_letras',
        'forma_pago', 'estado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function garantias()
    {
        return $this->hasMany(Garantia::class);
    }
}
