<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Sale"
 * )
 */
class Sale extends Model
{
    /**
     * @OA\Property(property="id", type="integer"),
     * @OA\Property(property="user_id", type="integer"),
     * @OA\Property(property="store_id", type="integer"),
     * @OA\Property(property="voucher_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="sono", type="string"),
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="contact", type="string"),
     * @OA\Property(property="qty", type="integer"),
     * @OA\Property(property="disc", type="number"),
     * @OA\Property(property="totalcost", type="number"),
     * @OA\Property(property="totalprice", type="number"),
     * @OA\Property(property="charge", type="number"),
     * @OA\Property(property="net", type="number"),
     * @OA\Property(property="grandtotal", type="number"),
     * @OA\Property(property="salestatus", type="string"),
     * @OA\Property(property="status", type="string"),
     * @OA\Property(property="remark", type="string"),
     * @OA\Property(property="pos", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string"),
     * @OA\Property(property="user", ref="#/components/schemas/User"),
     *      * @OA\Property(
     *     property="saleitems",
     *      type="array",
     *      @OA\Items(
     *          ref="#/components/schemas/SaleItem"
     *      )
     * ),
     * @OA\Property(property="store", ref="#/components/schemas/Store"),
     */

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'totalqty' => 'integer',
        'discpctg' => 'float',
        'totaldisc' => 'float',
        'totalbfdisc' => 'float',
        'totalbftax' => 'float',
        'grandtotal' => 'float',
        'payment' => 'float',
        'outstanding' => 'float',
        'docdate' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * Get the payments of the sale.
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    /**
     * Get the sale items of the sale.
     */
    public function saleitems()
    {
        return $this->hasMany('App\SaleItem');
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
    public function store()
    {
        return $this->belongsTo('App\Store');
    }
}
