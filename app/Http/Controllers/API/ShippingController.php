<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Shipping;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\ShippingServices;
use App\Traits\LogServices;

class ShippingController extends Controller
{
    use GlobalFunctions, NotificationFunctions, ShippingServices, LogServices;
    private $controllerName = '[ShippingController]';
     /**
     * @OA\Get(
     *      path="/api/shipping",
     *      operationId="getShippings",
     *      tags={"ShippingControllerService"},
     *      summary="Get list of shippings",
     *      description="Returns list of shippings",
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
     *          description="Successfully retrieved list of shippings"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of shippings")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of shippings.');
        // api/shipping (GET)
        $shippings = $this->getShippings($request->user());
        if ($this->isEmpty($shippings)) {
            return $this->errorPaginateResponse('Shippings');
        } else {
            return $this->successPaginateResponse('Shippings', $shippings, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/shipping",
     *      operationId="filterShippings",
     *      tags={"ShippingControllerService"},
     *      summary="Filter list of shippings",
     *      description="Returns list of filtered shippings",
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
     *          description="Successfully retrieved list of filtered shippings"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of shippings")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered shippings.');
        // api/shipping/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'store_id' => $request->store_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $shippings = $this->getShippings($request->user());
        $shippings = $this->filterShippings($shippings, $params);
        $shippings = $shippings->merge(Shipping::where('store_id' , null)->get());
        $shippings = $shippings->unique('id')->sortBy('id')->flatten(1);

        if ($this->isEmpty($shippings)) {
            return $this->errorPaginateResponse('Shippings');
        } else {
            return $this->successPaginateResponse('Shippings', $shippings, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

   
    /**
     * @OA\Get(
     *   tags={"ShippingControllerService"},
     *   path="/api/shipping/{uid}",
     *   summary="Retrieves shipping by Uid.",
     *     operationId="getShippingByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Shipping_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Shipping has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the shipping."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/shipping/{shippingid} (GET)
        error_log('Retrieving shipping of uid:' . $uid);
        $shipping = $this->getShipping($uid);
        if ($this->isEmpty($shipping)) {
            return $this->notFoundResponse('Shipping');
        } else {
            return $this->successResponse('Shipping', $shipping, 'retrieve');
        }
    }

  
    
    /**
     * @OA\Post(
     *   tags={"ShippingControllerService"},
     *   path="/api/shipping",
     *   summary="Creates a shipping.",
     *   operationId="createShipping",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Shippingname",
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
     * description="Shipping description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="price",
     * in="query",
     * required=true,
     * description="Shipping price",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="maxweight",
     * in="query",
     * required=true,
     * description="Shipping maximum weight",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="maxdimension",
     * in="query",
     * required=true,
     * description="Shipping maximum dimension",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Shipping has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the shipping."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/shipping (POST)

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'price' => 'required|numeric',
            'maxweight' => 'required|numeric',
            'maxdimension' => 'required|numeric',
        ]);
        error_log($this->controllerName.'Creating shipping.');
        $params = collect([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'desc' => $request->desc,
            'price' => $request->price,
            'maxweight' => $request->maxweight,
            'maxdimension' => $request->maxdimension,
        ]);
        $params = json_decode(json_encode($params));
        $shipping = $this->createShipping($params);
        if ($this->isEmpty($shipping)) {
            DB::rollBack();
            return $this->errorResponse();
        }
    
        $this->createLog($request->user()->id , [$shipping->id], 'create', 'shipping');
        DB::commit();

        return $this->successResponse('Shipping', $shipping, 'create');
    }


    /**
     * @OA\Put(
     *   tags={"ShippingControllerService"},
     *   path="/api/shipping/{uid}",
     *   summary="Update shipping by Uid.",
     *     operationId="updateShippingByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Shipping_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Shippingname",
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
     * description="Shipping description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="price",
     * in="query",
     * required=true,
     * description="Shipping price",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="maxweight",
     * in="query",
     * required=true,
     * description="Shipping maximum weight",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="maxdimension",
     * in="query",
     * required=true,
     * description="Shipping maximum dimension",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Shipping has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the shipping."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/shipping/{shippingid} (PUT)
        error_log($this->controllerName.'Updating shipping of uid: ' . $uid);
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'price' => 'required|numeric',
            'maxweight' => 'required|numeric',
            'maxdimension' => 'required|numeric',
        ]);
        $shipping = $this->getShipping($uid);
        if ($this->isEmpty($shipping)) {
            DB::rollBack();
            return $this->notFoundResponse('Shipping');
        }

        $params = collect([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'desc' => $request->desc,
            'price' => $request->price,
            'maxweight' => $request->maxweight,
            'maxdimension' => $request->maxdimension,
        ]);
        $params = json_decode(json_encode($params));
        $shipping = $this->updateShipping($shipping, $params);
        if ($this->isEmpty($shipping)) {
            DB::rollBack();
            return $this->errorResponse();
        }

        $this->createLog($request->user()->id , [$shipping->id], 'update', 'shipping');
        DB::commit();

        return $this->successResponse('Shipping', $shipping, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"ShippingControllerService"},
     *   path="/api/shipping/{uid}",
     *   summary="Set shipping's 'status' to 0.",
     *     operationId="deleteShippingByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Shipping ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Shipping has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the shipping."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/shipping/{shippingid} (DELETE)
        error_log('Deleting shipping of uid: ' . $uid);
        $shipping = $this->getShipping($uid);
        if ($this->isEmpty($shipping)) {
            DB::rollBack();
            return $this->notFoundResponse('Shipping');
        }
        $shipping = $this->deleteShipping($shipping);
        if ($this->isEmpty($shipping)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            $this->createLog($request->user()->id , [$shipping->id], 'delete', 'shipping');
            DB::commit();
            return $this->successResponse('Shipping', null, 'delete');
        }
    }

}
