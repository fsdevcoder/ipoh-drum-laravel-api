<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
      /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'stock' => 'float',
        'warrantyperiod' => 'float',
        'status' => 'boolean',
        'price' => 'float',
        'cost' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * Get the purchases of the account.
     */
    public function inventory()
    {
        return $this->belongsTo('App\Inventory');
    }

    /**
     * Get the sale of the sale item.
     */
    public function saleitems()
    {
        return $this->belongsToMany('App\SaleItem','batch_sale_item')->withPivot( 'stock','status','created_at','updated_at');
    }

    /**
     * Get the sale of the sale item.
     */
    public function purchaseitem()
    {
        return $this->hasOne('App\PurchaseItem');
    }
}
