<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model {
    protected $fillable = ['tipo_comprobante', 'serie', 'ultimo_correlativo'];
}
