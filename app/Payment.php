<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
      /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amt' => 'float',
        'discount' => 'float',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
   
    /**
     * Get the creator of the payment.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the sales of the payment.
     */
    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }

    /**
     * Get the sales of the payment.
     */
    public function channelsale()
    {
        return $this->belongsTo('App\ChannelSale', 'channel_sale_id');
    }
   
}
