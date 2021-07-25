<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenerimaanLogistik extends Model
{
    protected $guarded = [];

    public function posko_pengirim(){
        return $this->belongsTo(Posko::class, 'pengirim_id');
    }

    public function posko_penerima(){
        return $this->belongsTo(Posko::class, 'penerima_id');
    }

    public function produk(){
        return $this->belongsTo(Logistik::class, 'id_produk');
    }
}
