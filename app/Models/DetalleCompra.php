<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model {
    protected $fillable = ['compra_id', 'producto_id', 'cantidad', 'precio_compra'];

    public function compra() { return $this->belongsTo(Compra::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}
