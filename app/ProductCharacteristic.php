<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="ProductCharacteristic"
 * )
 */
class ProductCharacteristic extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="icon", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
    /**
     *
     */
    public function inventories()
    {
         return $this->belongsToMany('App\ProductCharacteristic','inventory_product_characteristic', 'characteristic_id' , 'inventory_id' )->withPivot('remark');

    }
    /**
     *
     */
    public function tickets()
    {
        return $this->belongsToMany('App\ProductCharacteristic','inventory_product_characteristic', 'characteristic_id' , 'ticket_id' )->withPivot('remark');
    }
}
