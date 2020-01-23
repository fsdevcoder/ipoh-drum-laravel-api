<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="ArticleImage"
 * )
 */
class ArticleImage extends Model
{

    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="article_id", type="integer"),
     * @OA\Property(property="title", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="like", type="integer"),
     * @OA\Property(property="dislike", type="integer"),
     * @OA\Property(property="imgpublicid", type="string"),
     * @OA\Property(property="imgpath", type="string"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */
    public function article()
    {
        return $this->belongsTo('App\Article');
    }
}
