<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    protected $fillable = ['venta_id', 'producto_id', 'fecha_limite', 'estado'];

    public function venta() { return $this->belongsTo(Venta::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}
