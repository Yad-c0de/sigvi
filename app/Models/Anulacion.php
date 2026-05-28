<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Anulacion extends Model {
    protected $fillable = ['venta_id', 'user_id', 'motivo'];

    public function venta() { return $this->belongsTo(Venta::class); }
    public function user() { return $this->belongsTo(User::class); }
}
