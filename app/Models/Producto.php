<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion_tecnica',
        'foto',
        'categoria_id',
        'marca_id',
        'proveedor_id',
        'precio_costo',
        'precio_venta',
        'stock',
        'stock_minimo',
        'alertar_stock',
    ];

    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function marca() { return $this->belongsTo(Marca::class); }
    public function proveedor() { return $this->belongsTo(Proveedor::class); }
}
