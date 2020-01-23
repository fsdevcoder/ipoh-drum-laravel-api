<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketFamily extends Model
{
    
    /**
     *
     */
    public function ticket()
    {
        return $this->belongsTo('App\Ticket');
    }
    
    /**
     *
     */
    public function saleitems()
    {
        return $this->hasMany('App\SaleItem');
    }
}
