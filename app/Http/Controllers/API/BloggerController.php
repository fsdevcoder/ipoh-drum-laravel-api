<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Blogger;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\BloggerServices;
use App\Traits\LogServices;

class BloggerController extends Controller
{
    use GlobalFunctions, NotificationFunctions, BloggerServices, LogServices;
    private $controllerName = '[BloggerController]';

    /**
     * @OA\Get(
     *      path="/api/blogger",
     *      operationId="getBloggers",
     *      tags={"BloggerControllerService"},
     *      summary="Get list of bloggers",
     *      description="Returns list of bloggers",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number.",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="Page size.",
     *     @OA\Schema(type="integer")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of bloggers"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of bloggers")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of bloggers.');
        // api/blogger (GET)
        $bloggers = $this->getBloggers($request->user());
        if ($this->isEmpty($bloggers)) {
            return $this->errorPaginateResponse('Bloggers');
        } else {
            return $this->successPaginateResponse('Bloggers', $bloggers, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/blogger",
     *      operationId="filterBloggers",
     *      tags={"BloggerControllerService"},
     *      summary="Filter list of bloggers",
     *      description="Returns list of filtered bloggers",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="Page size",
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
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of filtered bloggers"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of bloggers")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered bloggers.');
        // api/blogger/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $bloggers = $this->getBloggers($request->user());
        $bloggers = $this->filterBloggers($request->user(), $params);

        if ($this->isEmpty($bloggers)) {
            return $this->errorPaginateResponse('Bloggers');
        } else {
            return $this->successPaginateResponse('Bloggers', $bloggers, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

    /**
     * @OA\Get(
     *   tags={"BloggerControllerService"},
     *   path="/api/blogger/{uid}",
     *   summary="Retrieves blogger by Uid.",
     *     operationId="getBloggerByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Blogger_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Blogger has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the blogger."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/blogger/{bloggerid} (GET)
        error_log('Retrieving blogger of uid:' . $uid);
        $blogger = $this->getBlogger($uid);
        if ($this->isEmpty($blogger)) {
            $data['data'] = null;
            return $this->notFoundResponse('Blogger');
        } else {
            return $this->successResponse('Blogger ', $blogger, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"BloggerControllerService"},
     *   path="/api/blogger",
     *   summary="Creates a blogger.",
     *   operationId="createBlogger",
     * @OA\Parameter(
     * name="company_id",
     * in="query",
     * description="Blogger belongs to which company",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Blogger Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Blogger Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email",
     * in="query",
     * description="Blogger Email",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="companyBelongings",
     * in="query",
     * description="Is This Blogger Belongs To Company?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * 	@OA\RequestBody(
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
     *     description="Blogger has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the blogger."
     *   )
     * )
     */
    public function store(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/blogger (POST)
        
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'companyBelongings' => 'required|boolean',
        ]);
        error_log('Creating blogger.');
        $params = collect([
            'user_id' => $request->user()->id,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'desc' => $request->desc,
            'email' => $request->email,
            'tel1' => $request->tel1,
            'companyBelongings' => $request->companyBelongings,
        ]);

        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $blogger = $this->createBlogger($params);
        if ($this->isEmpty($blogger)) {
            DB::rollBack();
            return $this->errorResponse();
        } 

        //Associating Image Relationship
        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Blogger/". $blogger->uid);
            if(!$this->isEmpty($img)){
                $blogger->imgpath = $img->imgurl;
                $blogger->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($blogger)){
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }
            }else{
                DB::rollBack();
                $this->deleteImages($proccessingimgids);
                return $this->errorResponse();
            }
        }

        DB::commit();
        return $this->successResponse('Blogger', $blogger, 'create');
    }


    /**
     * @OA\Post(
     *   tags={"BloggerControllerService"},
     *   path="/api/blogger/{uid}",
     *   summary="Update blogger by Uid.",
     *     operationId="updateBloggerByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Blogger_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="user_id",
     * in="query",
     * description="Blogger belongs to which user",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="company_id",
     * in="query",
     * description="Blogger belongs to which company",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Blogger Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Blogger Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email",
     * in="query",
     * description="Blogger Email",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="tel1",
     * in="query",
     * description="Blogger telephone",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="companyBelongings",
     * in="query",
     * description="Is This Blogger Belongs To Company?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     *   @OA\Parameter(
     *     name="_method",
     *     in="query",
     *     description="For spoofing purposes.",
     *     required=false,
     *     example="PUT",
     *     @OA\Schema(type="string")
     *    ),
     * 	@OA\RequestBody(
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
     *     description="Blogger has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the blogger."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // api/blogger/{bloggerid} (PUT) 
        error_log('Updating blogger of uid: ' . $uid);
        
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'companyBelongings' => 'required|boolean',
        ]);

        $blogger = $this->getBlogger($uid);
        if ($this->isEmpty($blogger)) {
            DB::rollBack();
            $data['data'] = null;
            return $this->notFoundResponse('Blogger');
        }
        
        $params = collect([
            'user_id' => $request->user()->id,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'desc' => $request->desc,
            'email' => $request->email,
            'companyBelongings' => $request->companyBelongings,
        ]);

        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $blogger = $this->updateBlogger($blogger, $params);
        if ($this->isEmpty($blogger)) {
            DB::rollBack();
            return $this->errorResponse();
        } 
        
        //Associating Image Relationship
        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Blogger/". $blogger->uid);
            if(!$this->isEmpty($img)){
                error_log('inside edi');
                //Delete Previous Image
                if($blogger->imgpublicid){
                    if(!$this->deleteImage($blogger->imgpublicid)){
                        error_log('wrong 7 edi');
                        DB::rollBack();
                        return $this->errorResponse();
                    }
                }

                $blogger->imgpath = $img->imgurl;
                $blogger->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($blogger)){
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }
            }else{
                DB::rollBack();
                $this->deleteImages($proccessingimgids);
                return $this->errorResponse();
            }
        }

        DB::commit();
        return $this->successResponse('Blogger ', $blogger, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"BloggerControllerService"},
     *   path="/api/blogger/{uid}",
     *   summary="Set blogger's 'status' to 0.",
     *     operationId="deleteBloggerByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Blogger ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Blogger has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the blogger."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/blogger/{bloggerid} (DELETE)
        error_log('Deleting blogger of uid: ' . $uid);
        $blogger = $this->getBlogger($uid);
        if ($this->isEmpty($blogger)) {
            DB::rollBack();
            return $this->notFoundResponse('Blogger ');
        }
        $blogger = $this->deleteBlogger($blogger);
        if ($this->isEmpty($blogger)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Blogger ', null, 'delete');
        }
    }
    
    /**
     * @OA\Get(
     *   tags={"BloggerControllerService"},
     *   path="/api/blogger/{uid}/articles",
     *   summary="Retrieves blog articles by Uid.",
     *     operationId="getArticlesByBloggerUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Blogger ID, NOT 'ID'.",
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
     *     description="Articles has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the articles."
     *   )
     * )
     */
    public function getArticles(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving blogger articles by uid:' . $uid);
        $blogger = $this->getBlogger($uid);
        if ($this->isEmpty($blogger)) {
            DB::rollBack();
            return $this->notFoundResponse('Blogger');
        }
        $articles = $blogger->articles()->with('blogger', 'articleimages')->where('status' , true)->get();

        if ($this->isEmpty($articles)) {
            return $this->errorPaginateResponse('Articles');
        } else {
            return $this->successPaginateResponse('Articles', $articles, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

}
