<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="ProductFeature"
 * )
 */
class ProductFeature extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="icon", type="string"),
     * @OA\Property(property="imgpath", type="string"),
     * @OA\Property(property="imgpublicid", type="string"),
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
        return $this->belongsToMany('App\Inventory')->withPivot('remark');
    }
    /**
     *
     */
    public function tickets()
    {
        return $this->belongsToMany('App\Ticket')->withPivot('remark');
    }
}
