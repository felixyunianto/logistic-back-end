<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenyaluranLogistik extends Model
{
    protected $guarded = [];

    public function posko_pengirim(){
        return $this->belongsTo(Posko::class, 'pengirim_id');
    }

    public function produk(){
        return $this->belongsTo(Logistik::class, 'id_produk');
    }
}
