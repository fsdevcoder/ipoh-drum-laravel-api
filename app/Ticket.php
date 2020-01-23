<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * Get the company of the inventory.
     */
    public function store()
    {
        return $this->belongsTo('App\Store');
    }

    /**
     * Get the saleitems of the inventory.
     */
    public function saleitems()
    {
        return $this->hasMany('App\SaleItem');
    }

    /**
     * Get the saleitems of the inventory.
     */
    public function verificationcodes()
    {
        return $this->hasMany('App\VerificationCode');
    }

    /**
     *
     */
    public function categories()
    {
        return $this->belongsToMany('App\Category')->withPivot('status','remark');
    }
    /**
     *
     */
    public function types()
    {
        return $this->belongsToMany('App\Type')->withPivot('status','remark');
    }
    /**
     *
     */
    public function productfeatures()
    {
        return $this->belongsToMany('App\ProductFeature')->withPivot('status','remark');
    }

    /**
     *
     */
    public function images()
    {
        return $this->hasMany('App\TicketImage');
    }

    
    /**
     *
     */
    public function ticketfamilies()
    {
        return $this->hasMany('App\TicketFamily');
    }

    /**
     *
     */
    public function productreviews()
    {
        return $this->hasMany('App\ProductReview');
    }
    
    /**
     *
     */
    public function promotion()
    {
        return $this->belongsTo('App\ProductPromotion', 'product_promotion_id');
    }

    /**
     *
     */
    public function warranty()
    {
        return $this->belongsTo('App\Warranty');
    }
    
    /**
     *
     */
    public function shipping()
    {
        return $this->belongsTo('App\Shipping');
    }

    /**
     *
     */
    public function characteristics(){

        return $this->belongsToMany('App\ProductCharacteristic','inventory_product_characteristic' , 'ticket_id' , 'characteristic_id')->withPivot('remark');
    }
}
