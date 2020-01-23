<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketImage extends Model
{
    /**
     *
     */
    public function ticket()
    {
        return $this->belongsTo('App\Ticket');
    }
}
