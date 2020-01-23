<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="SaleItem"
 * )
 */
class SaleItem extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="sale_id", type="integer"),
     * @OA\Property(property="inventory_id", type="integer"),
     * @OA\Property(property="inventory_family_id", type="integer"),
     * @OA\Property(property="pattern_id", type="integer"),
     * @OA\Property(property="ticket_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="trackingcode", type="string"),
     * @OA\Property(property="qty", type="integer"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="cost", type="number"),
     * @OA\Property(property="price", type="number"),
     * @OA\Property(property="disc", type="number"),
     * @OA\Property(property="totalprice", type="number"),
     * @OA\Property(property="totalcost", type="number"),
     * @OA\Property(property="grandtotal", type="number"),
     * @OA\Property(property="status", type="string"),
     * @OA\Property(property="type", type="string"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'qty' => 'integer',
        'price' => 'float',
        'totaldisc' => 'float',
        'linetotal' => 'float',
        'payment' => 'float',
        'outstanding' => 'float',
        'docdate' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
     /**
     * Get the sale of the sale item.
     */
    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }



     /**
     * Get the sale of the sale item.
     */
    public function inventory()
    {
        return $this->belongsTo('App\Inventory');
    }

    /**
     * Get the inventory of the purchase item.
     */
    public function inventoryfamily()
    {
        return $this->belongsTo('App\InventoryFamily', 'inventory_family_id');
    }
    /**
     * Get the inventory of the purchase item.
     */
    public function pattern()
    {
        return $this->belongsTo('App\Pattern');
    }

    /**
     * Get the inventory of the purchase item.
     */
    public function ticketfamily()
    {
        return $this->belongsTo('App\TicketFamily');
    }


}
