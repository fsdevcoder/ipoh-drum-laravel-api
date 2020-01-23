<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherCode extends Model
{
    
    public function voucher()
    {
        return $this->belongsTo('App\Store');
    }
}
