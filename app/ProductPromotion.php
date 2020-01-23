<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="ProductPromotion"
 * )
 */
class ProductPromotion extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="store_id", type="integer"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="qty", type="integer"),
     * @OA\Property(property="disc", type="number"),
     * @OA\Property(property="discpctg", type="number"),
     * @OA\Property(property="discbyprice", type="integer"),
     * @OA\Property(property="promostartdate", type="string"),
     * @OA\Property(property="promoenddate", type="string"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
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
    public function store()
    {
        return $this->belongsTo('App\Store');
    }
}
