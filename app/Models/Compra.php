<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model {
    protected $fillable = ['proveedor_id', 'comprobante_numero', 'total', 'fecha_compra'];

    public function proveedor() { return $this->belongsTo(Proveedor::class); }
    public function detalles() { return $this->hasMany(DetalleCompra::class); }
}
