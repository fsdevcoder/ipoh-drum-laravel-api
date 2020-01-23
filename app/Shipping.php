<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Shipping"
 * )
 */
class Shipping extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="store_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="price", type="number"),
     * @OA\Property(property="maxweight", type="number"),
     * @OA\Property(property="maxdimension", type="number"),
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
    public function store()
    {
        return $this->belongsTo('App\Store');
    }
}
