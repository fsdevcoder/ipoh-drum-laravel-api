<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Article;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\ArticleServices;
use App\Traits\ArticleImageServices;
use App\Traits\CommentServices;
use App\Traits\LogServices;

class ArticleController extends Controller
{
    use GlobalFunctions, NotificationFunctions, ArticleServices, ArticleImageServices, LogServices , CommentServices;

    private $controllerName = '[ArticleController]';
    /**
     * @OA\Get(
     *      path="/api/article",
     *      operationId="getArticles",
     *      tags={"ArticleControllerService"},
     *      summary="Get list of articles",
     *      description="Returns list of articles",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="number of pageSize",
     *     @OA\Schema(type="integer")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of articles"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of articles")
     *    )
     */
    public function index(Request $request)
    {
        error_log($this->controllerName.'Retrieving list of articles.');
        // api/article (GET)
        $articles = $this->getArticles($request->user());
        if ($this->isEmpty($articles)) {
            return $this->errorPaginateResponse('Articles');
        } else {
            return $this->successPaginateResponse('Articles', $articles, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/article",
     *      operationId="filterArticles",
     *      tags={"ArticleControllerService"},
     *      summary="Filter list of articles",
     *      description="Returns list of filtered articles",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="number of pageSize",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="keyword",
     *     in="query",
     *     description="Keyword for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="fromdate",
     *     in="query",
     *     description="From Date for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="todate",
     *     in="query",
     *     description="To string for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="status",
     *     in="query",
     *     description="status for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="company_id",
     *     in="query",
     *     description="Company id for filter",
     *     @OA\Schema(type="string")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of filtered articles"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of articles")
     *    )
     */
    public function filter(Request $request)
    {
        error_log($this->controllerName.'Retrieving list of filtered articles.');
        // api/article/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $articles = $this->filterArticles($articles, $params);

        if ($this->isEmpty($articles)) {
            return $this->errorPaginateResponse('Articles');
        } else {
            return $this->successPaginateResponse('Articles', $articles, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }
    /**
     * @OA\Get(
     *   tags={"ArticleControllerService"},
     *   path="/api/article/{uid}",
     *   summary="Retrieves article by Uid.",
     *     operationId="getArticleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Article_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Article has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the article."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/article/{articleid} (GET)
        error_log($this->controllerName.'Retrieving article of uid:' . $uid);
        $article = $this->getArticle($uid);
        if ($this->isEmpty($article)) {
            $data['data'] = null;
            return $this->notFoundResponse('Article');
        } else {
            return $this->successResponse('Article', $article, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"ArticleControllerService"},
     *   path="/api/article",
     *   summary="Creates a article.",
     *   operationId="createArticle",
     * @OA\Parameter(
     * name="blogger_id",
     * in="query",
     * description="Article belongs To which Blogger",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="Article title",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Article description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="scope",
     * in="query",
     * description="Is this article public?",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * 	@OA\RequestBody(
*          @OA\MediaType(
*              mediaType="multipart/form-data",
*              @OA\Schema(
*                  @OA\Property(
*                      property="imgs[]",
*                      description="Article Images",
*                      type="file",
*                      @OA\Items(type="string", format="binary")
*                   ),
*               ),
*           ),
*       ),
     *   @OA\Response(
     *     response=200,
     *     description="Article has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the article."
     *   )
     * )
     */
    public function store(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/article (POST)
        $this->validate($request, [
            'title' => 'required|string',
            'blogger_id' => 'required|numeric',
        ]);
        error_log($this->controllerName.'Creating article.');
        $params = collect([
            'blogger_id' => $request->blogger_id,
            'title' => $request->title,
            'desc' => $request->desc,
            'scope' => $request->scope,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $article = $this->createArticle($params);
        if ($this->isEmpty($article)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $count = 0;
        if($request->file('imgs') != null){
            error_log('Article Images Is Detected');
            $imgs = $request->file('imgs');
            foreach($imgs as $img){
                error_log('Inside img');
                $count++;
                if($count > 6){
                    break;
                }
                $img = $this->uploadImage($img , "/Article/". $article->uid . "/imgs");
                error_log(collect($img));
                if(!$this->isEmpty($img)){
                    $proccessingimgids->push($img->publicid);

                    //Attach Image to ArticleImage
                    $params = collect([
                        'imgpath' => $img->imgurl,
                        'imgpublicid' => $img->publicid,
                        'article_id' => $article->id,
                    ]);
                    $params = json_decode(json_encode($params));
                    $articleimage = $this->createArticleImage($params);
                    if($this->isEmpty($articleimage)){
                        error_log('error here1');
                        DB::rollBack();
                        $this->deleteImages($proccessingimgids);
                        return $this->errorResponse();
                    }
                }else{
                    error_log('error here3');
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }
            }
        }

        DB::commit();
        return $this->successResponse('Article', $article, 'create');
    }

    /**
     * @OA\Put(
     *   tags={"ArticleControllerService"},
     *   path="/api/article/{uid}",
     *   summary="Update article by Uid.",
     *     operationId="updateArticleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Article_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="blogger_id",
     * in="query",
     * description="Article belongs To which Blogger",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="Article title",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Article description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="scope",
     * in="query",
     * description="Is this article public?",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Article has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the article."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/article/{articleid} (PUT)
        error_log($this->controllerName.'Updating article of uid: ' . $uid);
        $this->validate($request, [
            'title' => 'required|string',
            'blogger_id' => 'required|numeric',
        ]);
        $article = $this->getArticle($uid);
        if ($this->isEmpty($article)) {
            DB::rollBack();
            return $this->notFoundResponse('Article');
        }
        $params = collect([
            'blogger_id' => $request->blogger_id,
            'title' => $request->title,
            'desc' => $request->desc,
            'scope' => $request->scope,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $article = $this->updateArticle($article, $params);
        if ($this->isEmpty($article)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Article', $article, 'update');
        }
    }

    /**
     * @OA\Delete(
     *   tags={"ArticleControllerService"},
     *   path="/api/article/{uid}",
     *   summary="Set article's 'status' to 0.",
     *     operationId="deleteArticleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Article ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Article has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the article."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/article/{articleid} (DELETE)
        error_log($this->controllerName.'Deleting article of uid: ' . $uid);
        $article = $this->getArticle( $uid);
        if ($this->isEmpty($article)) {
            DB::rollBack();
            return $this->notFoundResponse('Article');
        }
        $article = $this->deleteArticle($article);
        if ($this->isEmpty($article)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Article', null, 'delete');
        }
    }




    /**
     * @OA\Get(
     *   tags={"ArticleControllerService"},
     *   path="/api/public/article/{uid}",
     *   summary="Retrieves public article by Uid.",
     *     operationId="getPublicArticleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Article ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Articles has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the articles."
     *   )
     * )
     */
    public function getPublicArticle(Request $request , $uid)
    {
        error_log($this->controllerName.'Retrieving public articles listing');
        $article = $this->getArticle($uid);
        $article = $this->setCommentCount($article);

        if ($this->isEmpty($article) && $article->scope != "public") {
            $data['data'] = null;
            return $this->notFoundResponse('Article');
        } else {
            return $this->successResponse('Article', $article, 'retrieve');
        }
    }


    /**
     * @OA\Get(
     *   tags={"ArticleControllerService"},
     *   path="/api/public/articles",
     *   summary="Retrieves all public articles.",
     *     operationId="getPublicArticles",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="number of pageSize",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Articles has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the articles."
     *   )
     * )
     */
    public function getPublicArticles(Request $request)
    {
        error_log($this->controllerName.'Retrieving public articles listing');
        $articles = $this->getAllPublicArticles();
        $articles->map(function($item){
            return $this->setCommentCount($item);
        });

        if ($this->isEmpty($articles)) {
            return $this->errorPaginateResponse('Articles');
        } else {
            return $this->successPaginateResponse('Articles', $articles, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *   tags={"ArticleControllerService"},
     *   path="/api/public/articles/filter",
     *   summary="Filter all public articles.",
     *     operationId="filterPublicArticles",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="number of pageSize",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="keyword",
     *     in="query",
     *     description="Keyword for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="fromdate",
     *     in="query",
     *     description="From Date for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="todate",
     *     in="query",
     *     description="To string for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="status",
     *     in="query",
     *     description="status for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Articles has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the articles."
     *   )
     * )
     */
    public function filterPublicArticles(Request $request)
    {
        error_log('Retrieving list of filtered articles.');
        // api/store/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'scope' => "public",
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $articles = $this->getAllPublicArticles();
        $articles = $this->filterArticles($articles, $params);
        $articles->map(function($item){
            return $this->setCommentCount($item);
        });

        if ($this->isEmpty($articles)) {
            return $this->errorPaginateResponse('Articles');
        } else {
            return $this->successPaginateResponse('Articles', $articles, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }


    /**
     * @OA\Get(
     *   tags={"ArticleControllerService"},
     *   path="/api/public/article/{uid}/comments",
     *   summary="Retrieves all public comments.",
     *     operationId="getPublicArticleComments",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Article ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="number of pageSize",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Comments has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the comments."
     *   )
     * )
     */
    public function getArticleComments(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving article comments listing');
        $article = $this->getArticle($uid);
        if ($this->isEmpty($article)) {
            DB::rollBack();
            return $this->notFoundResponse('Article');
        }

        $comments = $this->getCommentsByArticle($article);

        if ($this->isEmpty($comments)) {
            return $this->errorPaginateResponse('Comments');
        } else {
            return $this->successPaginateResponse('Comments', $comments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
}
