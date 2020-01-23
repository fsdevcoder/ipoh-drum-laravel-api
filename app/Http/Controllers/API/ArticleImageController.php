<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\ArticleImage;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class ArticleImageController extends Controller
{
    use AllServices;
    private $controllerName = '[ArticleImageController]';


    public function index(Request $request)
    {

    }
    
    public function filter(Request $request)
    {

    }

   
    public function show(Request $request, $uid)
    {
        
    }

  
      
    /**
     * @OA\Post(
     *   tags={"ArticleImageControllerService"},
     *   path="/api/articleimage",
     *   summary="Creates a articleimage.",
     *   operationId="createArticleImage",
     * @OA\Parameter(
     * name="article_id",
     * in="query",
     * description="Article Id",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * 	@OA\RequestBody(
*          required=true,
*          @OA\MediaType(
*              mediaType="multipart/form-data",
*              @OA\Schema(
*                  @OA\Property(
*                      property="img",
*                      description="Image",
*                      type="file",
*                      @OA\Items(type="string", format="binary")
*                   ),
*               ),
*           ),
*       ),
     *   @OA\Response(
     *     response=200,
     *     description="ArticleImage has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the articleimage."
     *   )
     * )
     */
    public function store(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/articleimage (POST)
        
        $this->validate($request, [
            'img' => 'required',
            'article_id' => 'required',
        ]);
        error_log('Creating articleimage.');
        
        $article = $this->getArticleById($request->article_id);
        if ($this->isEmpty($article)) {
            DB::rollBack();
            return $this->notFoundResponse('Article');
        }

        $params = collect();

        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Article/". $article->uid);
            if(!$this->isEmpty($img)){
                $params = collect([
                    'imgpath' => $img->imgurl,
                    'imgpublicid' => $img->publicid,
                    'article_id' => $request->article_id,
                ]);
                $proccessingimgids->push($img->publicid);
            }else{
                DB::rollBack();
                $this->deleteImages($proccessingimgids);
                return $this->errorResponse();
            }
        }
        
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $articleimage = $this->createArticleImage($params);

        if ($this->isEmpty($articleimage)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('ArticleImage', $articleimage, 'create');
        }
    }


    public function update(Request $request, $uid)
    {
       
    }


    /**
     * @OA\Delete(
     *   tags={"ArticleImageControllerService"},
     *   path="/api/articleimage/{uid}",
     *   summary="Set articleimage's 'status' to 0.",
     *     operationId="deleteArticleImageByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="ArticleImage ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ArticleImage has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the articleimage."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/articleimage/{articleimageid} (DELETE)
        error_log('Deleting articleimage of uid: ' . $uid);
        $articleimage = $this->getArticleImage($uid);
        if ($this->isEmpty($articleimage)) {
            DB::rollBack();
            return $this->notFoundResponse('ArticleImage');
        }

        if (!$this->deleteArticleImage($articleimage)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('ArticleImage', null, 'delete');
        }
    }


}
