<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Logistik;

class LogistikMasuk extends Model
{
    protected $guarded = [];

    public function produk() {
        return $this->belongsTo(Logistik::class, 'id_produk');
    }
    
}
