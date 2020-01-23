<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Inventory"
 * )
 */
class Inventory extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="store_id", type="integer"),
     * @OA\Property(property="product_promotion_id", type="integer"),
     * @OA\Property(property="shipping_id", type="integer"),
     * @OA\Property(property="warranty_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="code", type="string"),
     * @OA\Property(property="sku", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="imgpublicid", type="string"),
     * @OA\Property(property="imgpath", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="rating", type="number"),
     * @OA\Property(property="cost", type="number"),
     * @OA\Property(property="price", type="number"),
     * @OA\Property(property="qty", type="integer"),
     * @OA\Property(property="promoendqty", type="integer"),
     * @OA\Property(property="promopctg", type="integer"),
     * @OA\Property(property="promoprice", type="string"),
     * @OA\Property(property="totalproductreview", type="integer"),
     * @OA\Property(property="salesqty", type="integer"),
     * @OA\Property(property="stockthreshold", type="integer"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="onsale", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string"),
     * @OA\Property(property="store", ref="#/components/schemas/Store"),
     * @OA\Property(property="promotion", ref="#/components/schemas/ProductPromotion"),
     * @OA\Property(property="warranty", ref="#/components/schemas/Warranty"),
     * @OA\Property(property="shipping", ref="#/components/schemas/Shipping"),
     * @OA\Property(
     *     property="inventoryfamilies",
     *      type="array",
     *      @OA\Items(
     *          ref="#/components/schemas/InventoryFamily"
     *      )
     * ),
     * @OA\Property(
     *     property="images",
     *      type="array",
     *      @OA\Items(
     *          ref="#/components/schemas/InventoryImage"
     *      )
     * ),
     * @OA\Property(
     *     property="reviews",
     *      type="array",
     *      @OA\Items(
     *          ref="#/components/schemas/ProductReview"
     *      )
     * ),
     * @OA\Property(
     *     property="characteristics",
     *      type="array",
     *      @OA\Items(
     *          ref="#/components/schemas/ProductCharacteristic"
     *      )
     * )
     */

    /**
     * The attributes that shoul{d be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'cost' => 'float',
        'price' => 'float',
        'stock' => 'integer',
        'salesqty' => 'integer',
        'stockthreshold' => 'integer',
        'backorder' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the store of the inventory.
     */
    public function store()
    {
        return $this->belongsTo('App\Store');
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
        return $this->hasMany('App\InventoryImage');
    }

    /**
     *
     */
    public function inventoryfamilies()
    {
        return $this->hasMany('App\InventoryFamily');
    }

    /**
     *
     */
    public function reviews()
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

        return $this->belongsToMany('App\ProductCharacteristic','inventory_product_characteristic' , 'inventory_id' , 'characteristic_id')->withPivot('remark');
    }
}
