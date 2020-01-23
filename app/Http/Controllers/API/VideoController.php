<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Video;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class VideoController extends Controller
{
    use AllServices;

    private $controllerName = '[VideoController]';
    /**
     * @OA\Get(
     *      path="/api/video",
     *      operationId="getVideos",
     *      tags={"VideoControllerService"},
     *      summary="Get list of videos",
     *      description="Returns list of videos",
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
     *          description="Successfully retrieved list of videos"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of videos")
     *    )
     */
    public function index(Request $request)
    {
        error_log($this->controllerName.'Retrieving list of videos.');
        // api/video (GET)
        $videos = $this->getVideos($request->user());
        if ($this->isEmpty($videos)) {
            return $this->errorPaginateResponse('Videos');
        } else {
            return $this->successPaginateResponse('Videos', $videos, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/video",
     *      operationId="filterVideos",
     *      tags={"VideoControllerService"},
     *      summary="Filter list of videos",
     *      description="Returns list of filtered videos",
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
     *          description="Successfully retrieved list of filtered videos"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of videos")
     *    )
     */
    public function filter(Request $request)
    {
        error_log($this->controllerName.'Retrieving list of filtered videos.');
        // api/video/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $videos = $this->filterVideos($videos, $params);

        if ($this->isEmpty($videos)) {
            return $this->errorPaginateResponse('Videos');
        } else {
            return $this->successPaginateResponse('Videos', $videos, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }
    /**
     * @OA\Get(
     *   tags={"VideoControllerService"},
     *   path="/api/video/{uid}",
     *   summary="Retrieves video by Uid.",
     *     operationId="getVideoByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Video_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Video has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the video."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/video/{videoid} (GET)
        error_log($this->controllerName.'Retrieving video of uid:' . $uid);
        $video = $this->getVideo($uid);
        if ($this->isEmpty($video)) {
            $data['data'] = null;
            return $this->notFoundResponse('Video');
        } else {
            return $this->successResponse('Video', $video, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"VideoControllerService"},
     *   path="/api/video",
     *   summary="Creates a video.",
     *   operationId="createVideo",
     * @OA\Parameter(
     * name="channel_id",
     * in="query",
     * description="Video belongs To which Channel",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="Video title",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Video description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="scope",
     * in="query",
     * description="Is this video public?",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="videopath",
     * in="query",
     * description="Video Link",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="videopublicid",
     * in="query",
     * description="Video Link",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="totallength",
     * in="query",
     * description="Length Of Video",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="free",
     * in="query",
     * description="Is this video free?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="price",
     * in="query",
     * description="Video Price",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="discbyprice",
     * in="query",
     * description="Is this video discount by price?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="disc",
     * in="query",
     * description="Discount Price",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="discpctg",
     * in="query",
     * description="Discount Percentage",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * 	@OA\RequestBody(
*          required=true,
*          @OA\MediaType(
*              mediaType="multipart/form-data",
*              @OA\Schema(
*                  @OA\Property(
*                      property="img",
*                      description="Video Cover Image",
*                      type="file",
*                      @OA\Items(type="string", format="binary")
*                   ),
*               ),
*           ),
*       ),
     *   @OA\Response(
     *     response=200,
     *     description="Video has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the video."
     *   )
     * )
     */
    public function store(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/video (POST)
        $this->validate($request, [
            'title' => 'required|string',
            'channel_id' => 'required|numeric',
            'videopath' => 'required|string',
            'videopublicid' => 'required|string',
            'img' => 'required',
            'free' => 'required|boolean',
            'discbyprice' => 'required|boolean',
            'scope' => 'required|string',
        ]);
        error_log($this->controllerName.'Creating video.');
        $params = collect([
            'channel_id' => $request->channel_id,
            'title' => $request->title,
            'desc' => $request->desc,
            'scope' => $request->scope,
            'videopath' => $request->videopath,
            'videopublicid' => $request->videopublicid,
            'totallength' => $request->totallength,
            'free' => $request->free,
            'price' => $request->price,
            'discbyprice' => $request->discbyprice,
            'disc' => $request->disc,
            'discpctg' => $request->discpctg,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $video = $this->createVideo($params);
        if ($this->isEmpty($video)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        if($request->file('img') != null){
            error_log('Image Is Detected');
            $img = $this->uploadImage($request->file('img') , "/Video/". $video->uid);
            if(!$this->isEmpty($img)){
                $video->imgpath = $img->imgurl;
                $video->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($video)){
                    error_log('error here0');
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
        return $this->successResponse('Video', $video, 'create');
    }

    /**
     * @OA\Post(
     *   tags={"VideoControllerService"},
     *   path="/api/video/{uid}",
     *   summary="Update video by Uid.",
     *     operationId="updateVideoByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Video_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="channel_id",
     * in="query",
     * description="Video belongs To which Channel",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="Video title",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Video description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="scope",
     * in="query",
     * description="Is this video public?",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="videopath",
     * in="query",
     * description="Video Link",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="videopublicid",
     * in="query",
     * description="Video ID",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="totallength",
     * in="query",
     * description="Length Of Video",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="free",
     * in="query",
     * description="Is this video free?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="price",
     * in="query",
     * description="Video Price",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="discbyprice",
     * in="query",
     * description="Is this video discount by price?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="disc",
     * in="query",
     * description="Discount Price",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="discpctg",
     * in="query",
     * description="Discount Percentage",
     * @OA\Schema(
     *              type="number"
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
*                      description="Video Cover Image",
*                      type="file",
*                      @OA\Items(type="string", format="binary")
*                   ),
*               ),
*           ),
*       ),
     *   @OA\Response(
     *     response=200,
     *     description="Video has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the video."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        $proccessingimgids = collect();
        // api/video/{videoid} (PUT)
        error_log($this->controllerName.'Updating video of uid: ' . $uid);
        $this->validate($request, [
            'title' => 'required|string',
            'channel_id' => 'required|numeric',
            'videopath' => 'required|string',
            'videopublicid' => 'required|string',
            'free' => 'required|boolean',
            'discbyprice' => 'required|boolean',
            'scope' => 'required|string',
        ]);
        $video = $this->getVideo($uid);
        if ($this->isEmpty($video)) {
            DB::rollBack();
            return $this->notFoundResponse('Video');
        }
        $params = collect([
            'channel_id' => $request->channel_id,
            'title' => $request->title,
            'desc' => $request->desc,
            'scope' => $request->scope,
            'videopath' => $request->videopath,
            'videopublicid' => $request->videopublicid,
            'totallength' => $request->totallength,
            'free' => $request->free,
            'price' => $request->price,
            'discbyprice' => $request->discbyprice,
            'disc' => $request->disc,
            'discpctg' => $request->discpctg,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $video = $this->updateVideo($video, $params);
        if ($this->isEmpty($video)) {
            DB::rollBack();
            return $this->errorResponse();
        }
        error_log($params->free);
          //Associating Image Relationship
          if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Video/". $video->uid);
            if(!$this->isEmpty($img)){
                error_log('inside edi');
                //Delete Previous Image
                if($video->imgpublicid){
                    if(!$this->deleteImage($video->imgpublicid)){
                        error_log('wrong 7 edi');
                        DB::rollBack();
                        return $this->errorResponse();
                    }
                }

                $video->imgpath = $img->imgurl;
                $video->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($video)){
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
        return $this->successResponse('Video', $video, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"VideoControllerService"},
     *   path="/api/video/{uid}",
     *   summary="Set video's 'status' to 0.",
     *     operationId="deleteVideoByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Video ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Video has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the video."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/video/{videoid} (DELETE)
        error_log($this->controllerName.'Deleting video of uid: ' . $uid);
        $video = $this->getVideo( $uid);
        if ($this->isEmpty($video)) {
            DB::rollBack();
            return $this->notFoundResponse('Video');
        }
        $video = $this->deleteVideo($video);
        if ($this->isEmpty($video)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Video', null, 'delete');
        }
    }




    /**
     * @OA\Get(
     *   tags={"VideoControllerService"},
     *   path="/api/public/video/{uid}",
     *   summary="Retrieves public video by Uid.",
     *     operationId="getPublicVideoByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Video ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Videos has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the videos."
     *   )
     * )
     */
    public function getPublicVideo(Request $request , $uid)
    {
        error_log($this->controllerName.'Retrieving public videos listing');
        $video = $this->getVideo($uid);
        $video = $this->setCommentCount($video);
        $video = $this->calculateVideoPromotionPrice($video);

        if ($this->isEmpty($video) && $video->scope != "public") {
            $data['data'] = null;
            return $this->notFoundResponse('Video');
        } else {
            return $this->successResponse('Video', $video, 'retrieve');
        }
    }


    /**
     * @OA\Get(
     *   tags={"VideoControllerService"},
     *   path="/api/public/videos",
     *   summary="Retrieves all public videos.",
     *     operationId="getPublicVideos",
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
     *     description="Videos has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the videos."
     *   )
     * )
     */
    public function getPublicVideos(Request $request)
    {
        error_log($this->controllerName.'Retrieving public videos listing');
        $videos = $this->getAllPublicVideos();
        $videos->map(function($item){
            $item = $this->calculateVideoPromotionPrice($item);
            return $this->setCommentCount($item);
        });

        if ($this->isEmpty($videos)) {
            return $this->errorPaginateResponse('Videos');
        } else {
            return $this->successPaginateResponse('Videos', $videos, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    
    /**
     * @OA\Get(
     *   tags={"VideoControllerService"},
     *   path="/api/public/videos/filter",
     *   summary="Filter all public videos.",
     *     operationId="filterPublicVideos",
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
     *     description="Videos has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the videos."
     *   )
     * )
     */
    public function filterPublicVideos(Request $request)
    {
        error_log('Retrieving list of filtered videos.');
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
        $videos = $this->getAllPublicVideos();
        $videos = $this->filterVideos($videos, $params);
        $videos->map(function($item){
            $item = $this->calculateVideoPromotionPrice($item);
            return $this->setCommentCount($item);
        });

        if ($this->isEmpty($videos)) {
            return $this->errorPaginateResponse('Videos');
        } else {
            return $this->successPaginateResponse('Videos', $videos, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }


    /**
     * @OA\Get(
     *   tags={"VideoControllerService"},
     *   path="/api/public/video/{uid}/comments",
     *   summary="Retrieves all public comments.",
     *     operationId="getPublicVideoComments",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Video ID, NOT 'ID'.",
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
    public function getVideoComments(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving video comments listing');
        $video = $this->getVideo($uid);
        $params = collect([
            'scope' => 'public',
            'status' => true,
        ]);
        if ($this->isEmpty($video)) {
            return $this->notFoundResponse('Video');
        }

        $comments = $this->getCommentsByVideo($video);

        if ($this->isEmpty($comments)) {
            return $this->errorPaginateResponse('Comments');
        } else {
            return $this->successPaginateResponse('Comments', $comments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Post(
     *   tags={"VideoControllerService"},
     *   path="/api/public/video/like/edit",
     *   summary="Set like for video",
     *     operationId="setVideoLikeById",
     *   @OA\Parameter(
     *     name="video_id",
     *     in="query",
     *     description="Video id",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="type",
     *     in="query",
     *     description="Like or dislike",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Like was set"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to set like"
     *   )
     * )
     */
    public function setVideoLike(Request $request)
    {
        DB::beginTransaction();
        error_log($this->controllerName.'setting video like');
        $this->validate($request, [
            'type' => 'required|in:like,dislike',
            'video_id' => 'required|numeric',
        ]);
        $video = $this->getVideoById($request->video_id);
        if ($this->isEmpty($video)) {
            DB::rollBack();
            return $this->notFoundResponse('Video');
        }

        
        if($request->type == 'like'){
            if(!$this->likeVideo($video)){
                DB::rollBack();
                return $this->errorResponse();
            }
        }else{
            if(!$this->dislikeVideo($video)){
                DB::rollBack();
                return $this->errorResponse();
            }
        }
        

        if ($this->isEmpty($comments)) {
            return $this->errorPaginateResponse('Comments');
        } else {
            return $this->successPaginateResponse('Comments', $comments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    
       /**
     * @OA\Get(
     *   tags={"VideoControllerService"},
     *   path="/api/uservideos",
     *   summary="User Ordered Video",
     *     operationId="userVideos",
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
     *     description="Video has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the video."
     *   )
     * )
     */
    public function userVideos(Request $request)
    {
        error_log('Retrieving user video');
        $user = $this->getUser($request->user()->uid);
        if ($this->isEmpty($user)) {
            return $this->errorResponse();
        }

        $videos = $user->purchasevideos()->wherePivot('status',true)->get();
        
        if ($this->isEmpty($videos)) {
            return $this->errorPaginateResponse('Videos');
        } else {
            return $this->successPaginateResponse('Videos', $videos, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
}
