<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Store"
 * )
 */
class Store extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="company_id", type="integer"),
     * @OA\Property(property="company", ref="#/components/schemas/Company"),
     * @OA\Property(property="user_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="contact", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="imgpath", type="string"),
     * @OA\Property(property="imgpublicid", type="string"),
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="rating", type="number"),
     * @OA\Property(property="freeshippingminpurchase", type="number"),
     * @OA\Property(property="address", type="string"),
     * @OA\Property(property="state", type="string"),
     * @OA\Property(property="postcode", type="string"),
     * @OA\Property(property="city", type="string"),
     * @OA\Property(property="country", type="string"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="companyBelongings", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
    /**
    *
    */
    public function vouchers()
    {
        return $this->hasMany('App\Voucher');
    }

    /**
    *
    */
    public function reviews()
    {
        return $this->hasMany('App\StoreReview');
    }
     /**
     *
     */
    public function inventories()
    {
        return $this->hasMany('App\Inventory');
    }
    /**
     *
     */
    public function tickets()
    {
        return $this->hasMany('App\Ticket');
    }

    /**
     *
     */
    public function sales()
    {
        return $this->hasMany('App\Sale');
    }
    /**
     *
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }


    /**
     *
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
      /**
     *
     */
    public function promotions()
    {
        return $this->hasMany('App\ProductPromotion');
    }

    /**
     *
     */
    public function warranties()
    {
        return $this->hasMany('App\Warranty');
    }

    /**
     *
     */
    public function shippings()
    {
        return $this->hasMany('App\Shipping');
    }

}
