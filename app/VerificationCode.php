<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    /**
     * 
     */
    public function ticket()
    {
        return $this->belongsTo('App\Ticket');
    }
}
