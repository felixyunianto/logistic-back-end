<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Posko;
use App\Logistik;

class KebutuhanLogistik extends Model
{
    protected $guarded = [];
    
    public function posko(){
        return $this->belongsTo(Posko::class, 'id_posko');
    }

    public function produk(){
        return $this->belongsTo(Logistik::class, 'id_produk');
    }
}
