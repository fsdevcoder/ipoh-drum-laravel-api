<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Channel;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\ChannelServices;
use App\Traits\LogServices;
use App\Traits\AllServices;

class ChannelController extends Controller
{
    use AllServices;
    private $controllerName = '[ChannelController]';

    /**
     * @OA\Get(
     *      path="/api/channel",
     *      operationId="getChannels",
     *      tags={"ChannelControllerService"},
     *      summary="Get list of channels",
     *      description="Returns list of channels",
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
     *          description="Successfully retrieved list of channels"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of channels")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of channels.');
        // api/channel (GET)
        $channels = $this->getChannels($request->user());
        if ($this->isEmpty($channels)) {
            return $this->errorPaginateResponse('Channels');
        } else {
            return $this->successPaginateResponse('Channels', $channels, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/channel",
     *      operationId="filterChannels",
     *      tags={"ChannelControllerService"},
     *      summary="Filter list of channels",
     *      description="Returns list of filtered channels",
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
     *          description="Successfully retrieved list of filtered channels"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of channels")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered channels.');
        // api/channel/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $channels = $this->getChannels($request->user());
        $channels = $this->filterChannels($request->user(), $params);

        if ($this->isEmpty($channels)) {
            return $this->errorPaginateResponse('Channels');
        } else {
            return $this->successPaginateResponse('Channels', $channels, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

    /**
     * @OA\Get(
     *   tags={"ChannelControllerService"},
     *   path="/api/channel/{uid}",
     *   summary="Retrieves channel by Uid.",
     *     operationId="getChannelByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Channel_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Channel has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the channel."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/channel/{channelid} (GET)
        error_log('Retrieving channel of uid:' . $uid);
        $channel = $this->getChannel($uid);
        if ($this->isEmpty($channel)) {
            $data['data'] = null;
            return $this->notFoundResponse('Channel');
        } else {
            return $this->successResponse('Channel ', $channel, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"ChannelControllerService"},
     *   path="/api/channel",
     *   summary="Creates a channel.",
     *   operationId="createChannel",
     * @OA\Parameter(
     * name="company_id",
     * in="query",
     * description="Channel belongs to which company",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Channel Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Channel Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email",
     * in="query",
     * description="Channel Email",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="tel1",
     * in="query",
     * description="Channel contact",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="companyBelongings",
     * in="query",
     * description="Is This Channel Belongs To Company?",
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
     *     description="Channel has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the channel."
     *   )
     * )
     */
    public function store(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/channel (POST)
        
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'companyBelongings' => 'required|boolean',
        ]);
        error_log('Creating channel.');
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
        $channel = $this->createChannel($params);
        if ($this->isEmpty($channel)) {
            DB::rollBack();
            return $this->errorResponse();
        } 

        //Associating Image Relationship
        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Channel/". $channel->uid);
            if(!$this->isEmpty($img)){
                $channel->imgpath = $img->imgurl;
                $channel->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($channel)){
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
        return $this->successResponse('Channel', $channel, 'create');
    }


    /**
     * @OA\Post(
     *   tags={"ChannelControllerService"},
     *   path="/api/channel/{uid}",
     *   summary="Update channel by Uid.",
     *     operationId="updateChannelByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Channel_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="user_id",
     * in="query",
     * description="Channel belongs to which user",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="company_id",
     * in="query",
     * description="Channel belongs to which company",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Channel Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Channel Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email",
     * in="query",
     * description="Channel Email",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="tel1",
     * in="query",
     * description="Channel telephone",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="companyBelongings",
     * in="query",
     * description="Is This Channel Belongs To Company?",
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
     *     description="Channel has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the channel."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // api/channel/{channelid} (PUT) 
        error_log('Updating channel of uid: ' . $uid);
        
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'companyBelongings' => 'required|boolean',
        ]);

        $channel = $this->getChannel($uid);
        if ($this->isEmpty($channel)) {
            DB::rollBack();
            $data['data'] = null;
            return $this->notFoundResponse('Channel');
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
        $channel = $this->updateChannel($channel, $params);
        if ($this->isEmpty($channel)) {
            DB::rollBack();
            return $this->errorResponse();
        } 
        
        //Associating Image Relationship
        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Channel/". $channel->uid);
            if(!$this->isEmpty($img)){
                error_log('inside edi');
                //Delete Previous Image
                if($channel->imgpublicid){
                    if(!$this->deleteImage($channel->imgpublicid)){
                        error_log('wrong 7 edi');
                        DB::rollBack();
                        return $this->errorResponse();
                    }
                }

                $channel->imgpath = $img->imgurl;
                $channel->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($channel)){
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
        return $this->successResponse('Channel ', $channel, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"ChannelControllerService"},
     *   path="/api/channel/{uid}",
     *   summary="Set channel's 'status' to 0.",
     *     operationId="deleteChannelByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Channel ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Channel has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the channel."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/channel/{channelid} (DELETE)
        error_log('Deleting channel of uid: ' . $uid);
        $channel = $this->getChannel($uid);
        if ($this->isEmpty($channel)) {
            DB::rollBack();
            return $this->notFoundResponse('Channel ');
        }
        $channel = $this->deleteChannel($channel);
        if ($this->isEmpty($channel)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Channel ', null, 'delete');
        }
    }
    
    /**
     * @OA\Get(
     *   tags={"ChannelControllerService"},
     *   path="/api/channel/{uid}/videos",
     *   summary="Retrieves blog videos by Uid.",
     *     operationId="getVideosByChannelUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Channel ID, NOT 'ID'.",
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
     *     description="Videos has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the videos."
     *   )
     * )
     */
    public function getVideos(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving channel videos by uid:' . $uid);
        $channel = $this->getChannel($uid);
        if ($this->isEmpty($channel)) {
            DB::rollBack();
            return $this->notFoundResponse('Channel');
        }
        $videos = $channel->videos()->where('status' , true)->get();

        if ($this->isEmpty($videos)) {
            return $this->errorPaginateResponse('Videos');
        } else {
            return $this->successPaginateResponse('Videos', $videos, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

}
