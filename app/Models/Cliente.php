<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombre_razon_social',
        'telefono',
        'email',
        'estado',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
