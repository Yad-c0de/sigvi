<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model {
    
    protected $table = 'empresa';   // <-- AÑADE ESTA LÍNEA

    protected $fillable = ['ruc', 'razon_social', 'direccion', 'distrito', 'ciudad'];
}
