<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** @OA\Schema(
 *     title="Article"
 * )
 */
class Article extends Model
{

    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="blogger_id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="title", type="string"),
     * @OA\Property(property="desc", type="string"),
     * @OA\Property(property="view", type="integer"),
     * @OA\Property(property="like", type="integer"),
     * @OA\Property(property="dislike", type="integer"),
     * @OA\Property(property="scope", type="string"),
     * @OA\Property(property="agerestrict", type="integer"),
     * @OA\Property(property="status", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string"),
     * @OA\Property(property="commentcount", type="integer"),
     * @OA\Property(
     *     property="articleimages",
     *      type="array",
     *      @OA\Items(
     *          ref="#/components/schemas/ArticleImage"
     *      )
     * ),
     * @OA\Property(property="store", ref="#/components/schemas/Blogger"),
     *      * @OA\Property(
     *     property="comments",
     *      type="array",
     *      @OA\Items(
     *          ref="#/components/schemas/Comment"
     *      )
     * ),
     */
    public function articleimages()
    {
        return $this->hasMany('App\ArticleImage');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function blogger()
    {
        return $this->belongsTo('App\Blogger');
    }

}
