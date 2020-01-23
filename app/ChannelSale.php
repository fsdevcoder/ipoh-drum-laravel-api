<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelSale extends Model
{
    /**
     * Get the payments of the sale.
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

     /**
     * Get the creator of the purchase.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }


    /**
     * Get the creator of the purchase.
     */
    public function channel()
    {
        return $this->belongsTo('App\Channel');
    }
    
    /**
     * Get the creator of the purchase.
     */
    public function video()
    {
        return $this->belongsTo('App\Video');
    }
}
