<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\StoreReview;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\StoreReviewServices;
use App\Traits\PatternServices;
use App\Traits\LogServices;

class StoreReviewController extends Controller
{
    use GlobalFunctions, NotificationFunctions, StoreReviewServices, LogServices;
    private $controllerName = '[StoreReviewController]';
     /**
     * @OA\Get(
     *      path="/api/storereview",
     *      operationId="getStoreReviews",
     *      tags={"StoreReviewControllerService"},
     *      summary="Get list of storereviews",
     *      description="Returns list of storereviews",
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
     *          description="Successfully retrieved list of storereviews"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of storereviews")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of storereviews.');
        // api/storereview (GET)
        $storereviews = $this->getStoreReviews($request->user());
        if ($this->isEmpty($storereviews)) {
            return $this->errorPaginateResponse('Store Reviews');
        } else {
            return $this->successPaginateResponse('Store Reviews', $storereviews, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/storereview",
     *      operationId="filterStoreReviews",
     *      tags={"StoreReviewControllerService"},
     *      summary="Filter list of storereviews",
     *      description="Returns list of filtered storereviews",
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
     *     description="To date for filter",
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
     *          description="Successfully retrieved list of filtered storereviews"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of storereviews")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered storereviews.');
        // api/storereview/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'storereview_id' => $request->storereview_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $storereviews = $this->getStoreReviews($request->user());
        $storereviews = $this->filterStoreReviews($storereviews, $params);

        if ($this->isEmpty($storereviews)) {
            return $this->errorPaginateResponse('Store Reviews');
        } else {
            return $this->successPaginateResponse('Store Reviews', $storereviews, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

   
    /**
     * @OA\Get(
     *   tags={"StoreReviewControllerService"},
     *   path="/api/storereview/{uid}",
     *   summary="Retrieves storereview by Uid.",
     *     operationId="getStoreReviewByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="StoreReview_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="StoreReview has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the storereview."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/storereview/{storereviewid} (GET)
        error_log('Retrieving storereview of uid:' . $uid);
        $storereview = $this->getStoreReview($uid);
        if ($this->isEmpty($storereview)) {
            return $this->notFoundResponse('StoreReview');
        } else {
            return $this->successResponse('StoreReview', $storereview, 'retrieve');
        }
    }

  
    
    /**
     * @OA\Post(
     *   tags={"StoreReviewControllerService"},
     *   path="/api/storereview",
     *   summary="Creates a storereview.",
     *   operationId="createStoreReview",
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="StoreReview title",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="store_id",
     * in="query",
     * description="Store ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Review description",
     * @OA\Schema(
     *              type="string"
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
     * @OA\Parameter(
     * name="rating",
     * in="query",
     * description="Review rating",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="StoreReview has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the storereview."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/storereview (POST)

        $this->validate($request, [
            'title' => 'required|string|max:191',
            'desc' => 'required|string',
            'rating' => 'required|numeric',
        ]);
        error_log($this->controllerName.'Creating storereview.');
        $params = collect([
            'title' => $request->title,
            'desc' => $request->desc,
            'store_id' => $request->store_id,
            'rating' => $request->rating,
            'user_id' => $request->user()->id,
            'img' => '',
        ]);
        $params = json_decode(json_encode($params));
        $params->img = $request->file('img');
        $storereview = $this->createStoreReview($params);
        if ($this->isEmpty($storereview)) {
            DB::rollBack();
            return $this->errorResponse();
        }
    
        $this->createLog($request->user()->id , [$storereview->id], 'create', 'storereview');
        DB::commit();

        return $this->successResponse('StoreReview', $storereview, 'create');
    }


    /**
     * @OA\Put(
     *   tags={"StoreReviewControllerService"},
     *   path="/api/storereview/{uid}",
     *   summary="Update storereview by Uid.",
     *     operationId="updateStoreReviewByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="StoreReview_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="StoreReview title",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="store_id",
     * in="query",
     * description="Store ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Review description",
     * @OA\Schema(
     *              type="string"
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
     * @OA\Parameter(
     * name="rating",
     * in="query",
     * description="Review rating",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="StoreReview has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the storereview."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/storereview/{storereviewid} (PUT)
        error_log($this->controllerName.'Updating storereview of uid: ' . $uid);
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'desc' => 'required|string',
            'rating' => 'required|numeric',
        ]);
        $storereview = $this->getStoreReview($uid);
        if ($this->isEmpty($storereview)) {
            DB::rollBack();
            return $this->notFoundResponse('StoreReview');
        }

        $params = collect([
            'title' => $request->title,
            'desc' => $request->desc,
            'store_id' => $request->store_id,
            'rating' => $request->rating,
            'user_id' => $request->user()->id,
            'img' => '',
        ]);
        $params = json_decode(json_encode($params));
        $params->img = $request->file('img');
        $storereview = $this->updateStoreReview($storereview, $params);
        if ($this->isEmpty($storereview)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $this->createLog($request->user()->id , [$storereview->id], 'update', 'storereview');
        DB::commit();

        return $this->successResponse('StoreReview', $storereview, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"StoreReviewControllerService"},
     *   path="/api/storereview/{uid}",
     *   summary="Set storereview's 'status' to 0.",
     *     operationId="deleteStoreReviewByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="StoreReview ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="StoreReview has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the storereview."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/storereview/{storereviewid} (DELETE)
        error_log('Deleting storereview of uid: ' . $uid);
        $storereview = $this->getStoreReview($uid);
        if ($this->isEmpty($storereview)) {
            DB::rollBack();
            return $this->notFoundResponse('StoreReview');
        }
        $storereview = $this->deleteStoreReview($storereview);
        if ($this->isEmpty($storereview)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            $this->createLog($request->user()->id , [$storereview->id], 'delete', 'storereview');
            DB::commit();
            return $this->successResponse('StoreReview', $storereview, 'delete');
        }
    }

}
