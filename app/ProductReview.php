<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="ProductReview"
 * )
 */
class ProductReview extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="inventory_id", type="integer"),
     * @OA\Property(property="ticket_id", type="integer"),
     * @OA\Property(property="user_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="title", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="imgpath", type="string"),
     * @OA\Property(property="imgpublicid", type="string"),
     * @OA\Property(property="type", type="string"),
     * @OA\Property(property="rating", type="number"),
     * @OA\Property(property="like", type="integer"),
     * @OA\Property(property="dislike", type="integer"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
    /**
    *
    */
    public function inventory()
    {
        return $this->belongsTo('App\Inventory');
    }
    /**
    *
    */
    public function ticket()
    {
        return $this->belongsTo('App\Ticket');
    }

    /**
    *
    */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
