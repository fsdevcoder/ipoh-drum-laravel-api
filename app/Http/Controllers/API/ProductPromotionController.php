<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\ProductPromotion;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\ProductPromotionServices;
use App\Traits\PatternServices;
use App\Traits\LogServices;

class ProductPromotionController extends Controller
{
    use GlobalFunctions, NotificationFunctions, ProductPromotionServices, LogServices;
    private $controllerName = '[ProductPromotionController]';
     /**
     * @OA\Get(
     *      path="/api/productpromotion",
     *      operationId="getProductPromotions",
     *      tags={"ProductPromotionControllerService"},
     *      summary="Get list of productpromotions",
     *      description="Returns list of productpromotions",
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
     *          description="Successfully retrieved list of productpromotions"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of productpromotions")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of productpromotions.');
        // api/productpromotion (GET)
        $productpromotions = $this->getProductPromotions($request->user());

        if ($this->isEmpty($productpromotions)) {
            return $this->errorPaginateResponse('Product Promotions');
        } else {
            return $this->successPaginateResponse('Product Promotions', $productpromotions, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/productpromotion",
     *      operationId="filterProductPromotions",
     *      tags={"ProductPromotionControllerService"},
     *      summary="Filter list of productpromotions",
     *      description="Returns list of filtered productpromotions",
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
     *   @OA\Parameter(
     *     name="store_id",
     *     in="query",
     *     description="store id for filter",
     *     @OA\Schema(type="integer")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of filtered productpromotions"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of productpromotions")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered productpromotions.');
        // api/productpromotion/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'store_id' => $request->store_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $productpromotions = $this->getProductPromotions($request->user());
        $productpromotions = $this->filterProductPromotions($productpromotions, $params);
        $productpromotions = $productpromotions->merge(ProductPromotion::where('store_id' , null)->get());
        $productpromotions = $productpromotions->unique('id')->sortBy('id')->flatten(1);

        if ($this->isEmpty($productpromotions)) {
            return $this->errorPaginateResponse('Product Promotions');
        } else {
            return $this->successPaginateResponse('Product Promotions', $productpromotions, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }


    /**
     * @OA\Get(
     *   tags={"ProductPromotionControllerService"},
     *   path="/api/productpromotion/{uid}",
     *   summary="Retrieves productpromotion by ProductPromotion's uid.",
     *     operationId="getProductPromotionByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="ProductPromotion's uid, NOT 'id'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ProductPromotion has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the productpromotion."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/productpromotion/{productpromotionid} (GET)
        error_log('Retrieving productpromotion of uid:' . $uid);
        $productpromotion = $this->getProductPromotion($uid);
        if ($this->isEmpty($productpromotion)) {
            return $this->notFoundResponse('ProductPromotion');
        } else {
            return $this->successResponse('ProductPromotion', $productpromotion, 'retrieve');
        }
    }



    /**
     * @OA\Post(
     *   tags={"ProductPromotionControllerService"},
     *   path="/api/productpromotion",
     *   summary="Creates a productpromotion.",
     *   operationId="createProductPromotion",
     * @OA\Parameter(
     * name="store_id",
     * in="query",
     * description="Store ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="ProductPromotionname",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Promotion description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="qty",
     * in="query",
     * description="Limited Qty",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="disc",
     * in="query",
     * description="Promotion Discount",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="discpctg",
     * in="query",
     * description="Promotion Discount Percentage",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="discbyprice",
     * in="query",
     * description="Promotion discount by price",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="promostartdate",
     * in="query",
     * description="Promotion Start Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="promoenddate",
     * in="query",
     * description="Promotion End Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="ProductPromotion has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the productpromotion."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/productpromotion (POST)

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'discbyprice' =>'required|boolean',
        ]);
        error_log($this->controllerName.'Creating productpromotion.');
        $params = collect([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'desc' => $request->desc,
            'qty' => $request->qty,
            'disc' => $request->disc,
            'discpctg' => $request->discpctg,
            'discbyprice' => $request->discbyprice,
            'promostartdate' => $request->promostartdate,
            'promoenddate' => $request->promoenddate,
        ]);
        $params = json_decode(json_encode($params));
        $productpromotion = $this->createProductPromotion($params);
        if ($this->isEmpty($productpromotion)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $this->createLog($request->user()->id , [$productpromotion->id], 'create', 'productpromotion');
        DB::commit();

        return $this->successResponse('ProductPromotion', $productpromotion, 'create');
    }


    /**
     * @OA\Put(
     *   tags={"ProductPromotionControllerService"},
     *   path="/api/productpromotion/{uid}",
     *   summary="Update productpromotion by Uid.",
     *     operationId="updateProductPromotionByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="ProductPromotion_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="ProductPromotionname",
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
     * description="Promotion description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="qty",
     * in="query",
     * description="Limited Qty",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="disc",
     * in="query",
     * description="Promotion Discount",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="discpctg",
     * in="query",
     * description="Promotion Discount Percentage",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="discbyprice",
     * in="query",
     * description="Promotion discount by price",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="promostartdate",
     * in="query",
     * description="Promotion Start Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="promoenddate",
     * in="query",
     * description="Promotion End Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="ProductPromotion has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the productpromotion."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/productpromotion/{productpromotionid} (PUT)
        error_log($this->controllerName.'Updating productpromotion of uid: ' . $uid);
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'discbyprice' =>'required|boolean',
        ]);

        $productpromotion = $this->getProductPromotion($uid);
        if ($this->isEmpty($productpromotion)) {
            DB::rollBack();
            return $this->notFoundResponse('ProductPromotion');
        }

        $params = collect([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'desc' => $request->desc,
            'qty' => $request->qty,
            'disc' => $request->disc,
            'discpctg' => $request->discpctg,
            'discbyprice' => $request->discbyprice,
            'promostartdate' => $request->promostartdate,
            'promoenddate' => $request->promoenddate,
        ]);
        $params = json_decode(json_encode($params));
        $productpromotion = $this->updateProductPromotion($productpromotion, $params);
        if ($this->isEmpty($productpromotion)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $this->createLog($request->user()->id , [$productpromotion->id], 'update', 'productpromotion');
        DB::commit();

        return $this->successResponse('ProductPromotion', $productpromotion, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"ProductPromotionControllerService"},
     *   path="/api/productpromotion/{uid}",
     *   summary="Set productpromotion's 'status' to 0.",
     *     operationId="deleteProductPromotionByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="ProductPromotion ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ProductPromotion has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the productpromotion."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/productpromotion/{productpromotionid} (DELETE)
        error_log('Deleting productpromotion of uid: ' . $uid);
        $productpromotion = $this->getProductPromotion($uid);
        if ($this->isEmpty($productpromotion)) {
            DB::rollBack();
            return $this->notFoundResponse('ProductPromotion');
        }
        $productpromotion = $this->deleteProductPromotion($productpromotion);
        if ($this->isEmpty($productpromotion)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            $this->createLog($request->user()->id , [$productpromotion->id], 'delete', 'productpromotion');
            DB::commit();
            return $this->successResponse('ProductPromotion', null, 'delete');
        }
    }

}
