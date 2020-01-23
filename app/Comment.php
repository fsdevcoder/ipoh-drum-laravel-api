<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Comment"
 * )
 */
class Comment extends Model
{

    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="video_id", type="integer"),
     * @OA\Property(property="article_id", type="integer"),
     * @OA\Property(property="article_image_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="text", type="string"),
     * @OA\Property(property="imgpath", type="string"),
     * @OA\Property(property="imgpublicid", type="string"),
     * @OA\Property(property="like", type="integer"),
     * @OA\Property(property="dislike", type="integer"),
     * @OA\Property(property="type", type="string"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
    public function secondcomments()
    {
        return $this->hasMany('App\SecondComment');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function video()
    {
        return $this->belongsTo('App\Video');
    }

    public function article()
    {
        return $this->belongsTo('App\Article');
    }

    public function articleimage()
    {
        return $this->belongsTo('App\ArticleImage');
    }

}
