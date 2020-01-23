<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Voucher"
 * )
 */
class Voucher extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="store_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="qty", type="integer"),
     * @OA\Property(property="redeemqty", type="integer"),
     * @OA\Property(property="releaseqty", type="integer"),
     * @OA\Property(property="disc", type="number"),
     * @OA\Property(property="discpctg", type="number"),
     * @OA\Property(property="discbyprice", type="integer"),
     * @OA\Property(property="startdate", type="string"),
     * @OA\Property(property="enddate", type="string"),
     * @OA\Property(property="minpurchase", type="number"),
     * @OA\Property(property="minqty", type="integer"),
     * @OA\Property(property="minvariety", type="integer"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="unlimited", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
    public function store()
    {
        return $this->belongsTo('App\Store');
    }

    public function vouchercodes()
    {
        return $this->hasMany('App\VoucherCode');
    }
}
