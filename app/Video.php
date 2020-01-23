<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Video"
 * )
 */
class Video extends Model
{

    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="channel_id", type="integer"),
     * @OA\Property(property="playlist_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="title", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="videopath", type="string"),
     * @OA\Property(property="videopublicid", type="string"),
     * @OA\Property(property="imgpath", type="string"),
     * @OA\Property(property="imgpublicid", type="string"),
     * @OA\Property(property="totallength", type="string"),
     * @OA\Property(property="view", type="integer"),
     * @OA\Property(property="like", type="integer"),
     * @OA\Property(property="dislike", type="integer"),
     * @OA\Property(property="price", type="number"),
     * @OA\Property(property="discpctg", type="number"),
     * @OA\Property(property="disc", type="number"),
     * @OA\Property(property="discbyprice", type="integer"),
     * @OA\Property(property="free", type="integer"),
     * @OA\Property(property="salesqty", type="integer"),
     * @OA\Property(property="scope", type="string"),
     * @OA\Property(property="agerestrict", type="integer"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string"),
     * @OA\Property(property="commentcount", type="integer"),
     * @OA\Property(property="channel", ref="#/components/schemas/Channel")
     *
     */
    public function channel()
    {
        return $this->belongsTo('App\Channel');
    }

    public function playlist()
    {
        return $this->belongsTo('App\Playlist');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function images()
    {
        return $this->hasMany('App\VideoImage');
    }

    public function channelsales()
    {
        return $this->hasMany('App\ChannelSale');
    }
    
    public function purchaseusers()
    {
         return $this->belongsToMany('App\User','user_purchased_video', 'video_id' , 'user_id' )->withPivot('status');

    }
}
