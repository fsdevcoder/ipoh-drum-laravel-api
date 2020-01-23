<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Channel"
 * )
 */
class Channel extends Model
{
    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="company_id", type="integer"),
     * @OA\Property(property="user_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="imgpath", type="string"),
     * @OA\Property(property="imgpublicid", type="string"),
     * @OA\Property(property="tel1", type="string"),
     * @OA\Property(property="company_belongings", type="integer"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string"),
     * @OA\Property(property="company", ref="#/components/schemas/Company")
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }


    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function videos()
    {
        return $this->hasMany('App\Video');
    }
    
    public function channelsales()
    {
        return $this->hasMany('App\ChannelSale');
    }
    
}
