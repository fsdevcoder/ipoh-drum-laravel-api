<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\ProductReview;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\ProductReviewServices;
use App\Traits\PatternServices;
use App\Traits\LogServices;

class ProductReviewController extends Controller
{
    use GlobalFunctions, NotificationFunctions, ProductReviewServices, LogServices;
    private $controllerName = '[ProductReviewController]';
     /**
     * @OA\Get(
     *      path="/api/productreview",
     *      operationId="getProductReviews",
     *      tags={"ProductReviewControllerService"},
     *      summary="Get list of productreviews",
     *      description="Returns list of productreviews",
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
     *          description="Successfully retrieved list of productreviews"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of productreviews")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of productreviews.');
        // api/productreview (GET)
        $productreviews = $this->getProductReviews($request->user());
        if ($this->isEmpty($productreviews)) {
            return $this->errorPaginateResponse('Product Reviews');
        } else {
            return $this->successPaginateResponse('Product Reviews', $productreviews, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/productreview",
     *      operationId="filterProductReviews",
     *      tags={"ProductReviewControllerService"},
     *      summary="Filter list of productreviews",
     *      description="Returns list of filtered productreviews",
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
     *          description="Successfully retrieved list of filtered productreviews"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of productreviews")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered productreviews.');
        // api/productreview/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'productreview_id' => $request->productreview_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $productreviews = $this->getProductReviews($request->user());
        $productreviews = $this->filterProductReviews($productreviews, $params);

        if ($this->isEmpty($productreviews)) {
            return $this->errorPaginateResponse('Product Reviews');
        } else {
            return $this->successPaginateResponse('Product Reviews', $productreviews, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

   
    /**
     * @OA\Get(
     *   tags={"ProductReviewControllerService"},
     *   path="/api/productreview/{uid}",
     *   summary="Retrieves productreview by Uid.",
     *     operationId="getProductReviewByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="ProductReview_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ProductReview has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the productreview."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/productreview/{productreviewid} (GET)
        error_log('Retrieving productreview of uid:' . $uid);
        $productreview = $this->getProductReview($uid);
        if ($this->isEmpty($productreview)) {
            return $this->notFoundResponse('ProductReview');
        } else {
            return $this->successResponse('ProductReview', $productreview, 'retrieve');
        }
    }

  
    
    /**
     * @OA\Post(
     *   tags={"ProductReviewControllerService"},
     *   path="/api/productreview",
     *   summary="Creates a productreview.",
     *   operationId="createProductReview",
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="ProductReview title",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="inventory_id",
     * in="query",
     * description="Inventory ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="ticket_id",
     * in="query",
     * description="Ticket ID",
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
     * name="type",
     * in="query",
     * description="Review type",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
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
     *     description="ProductReview has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the productreview."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/productreview (POST)

        $this->validate($request, [
            'title' => 'required|string|max:191',
            'desc' => 'required|string',
            'type' => 'required|in:inventory,ticket',
            'rating' => 'required|numeric',
        ]);
        error_log($this->controllerName.'Creating productreview.');
        $params = collect([
            'title' => $request->title,
            'desc' => $request->desc,
            'inventory_id' => $request->inventory_id,
            'ticket_id' => $request->ticket_id,
            'rating' => $request->rating,
            'type' => $request->type,
            'user_id' => $request->user()->id,
            'img' => '',
        ]);
        $params = json_decode(json_encode($params));
        $params->img = $request->file('img');
        $productreview = $this->createProductReview($params);
        if ($this->isEmpty($productreview)) {
            DB::rollBack();
            return $this->errorResponse();
        }
    
        $this->createLog($request->user()->id , [$productreview->id], 'create', 'productreview');
        DB::commit();

        return $this->successResponse('ProductReview', $productreview, 'create');
    }


    /**
     * @OA\Put(
     *   tags={"ProductReviewControllerService"},
     *   path="/api/productreview/{uid}",
     *   summary="Update productreview by Uid.",
     *     operationId="updateProductReviewByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="ProductReview_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="ProductReview title",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="inventory_id",
     * in="query",
     * description="Inventory ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="ticket_id",
     * in="query",
     * description="Ticket ID",
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
     * name="type",
     * in="query",
     * description="Review type",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
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
     *     description="ProductReview has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the productreview."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/productreview/{productreviewid} (PUT)
        error_log($this->controllerName.'Updating productreview of uid: ' . $uid);
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'desc' => 'required|string',
            'type' => 'required|in:inventory,ticket',
            'rating' => 'required|numeric',
        ]);
        $productreview = $this->getProductReview($uid);
        if ($this->isEmpty($productreview)) {
            DB::rollBack();
            return $this->notFoundResponse('ProductReview');
        }

        $params = collect([
            'title' => $request->title,
            'desc' => $request->desc,
            'inventory_id' => $request->inventory_id,
            'ticket_id' => $request->ticket_id,
            'rating' => $request->rating,
            'type' => $request->type,
            'user_id' => $request->user()->id,
            'img' => '',
        ]);
        $params = json_decode(json_encode($params));
        if($request->file('img')){
            error_log('got');
        }
        $params->img = $request->file('img');
        $productreview = $this->updateProductReview($productreview, $params);
        if ($this->isEmpty($productreview)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $this->createLog($request->user()->id , [$productreview->id], 'update', 'productreview');
        DB::commit();

        return $this->successResponse('ProductReview', $productreview, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"ProductReviewControllerService"},
     *   path="/api/productreview/{uid}",
     *   summary="Set productreview's 'status' to 0.",
     *     operationId="deleteProductReviewByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="ProductReview ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ProductReview has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the productreview."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/productreview/{productreviewid} (DELETE)
        error_log('Deleting productreview of uid: ' . $uid);
        $productreview = $this->getProductReview($uid);
        if ($this->isEmpty($productreview)) {
            DB::rollBack();
            return $this->notFoundResponse('ProductReview');
        }
        $productreview = $this->deleteProductReview($productreview);
        if ($this->isEmpty($productreview)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            $this->createLog($request->user()->id , [$productreview->id], 'delete', 'productreview');
            DB::commit();
            return $this->successResponse('ProductReview', $productreview, 'delete');
        }
    }

}
