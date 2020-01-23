<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Store;
use App\ProductPromotion;
use App\Warranty;
use App\Shipping;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\StoreServices;
use App\Traits\LogServices;
use App\Traits\ImageHostingServices;

class StoreController extends Controller
{
    use GlobalFunctions, NotificationFunctions, StoreServices, LogServices, ImageHostingServices;
    private $controllerName = '[StoreController]';
    /**
     * @OA\Get(
     *      path="/api/store",
     *      operationId="getStores",
     *      tags={"StoreControllerService"},
     *      summary="Get list of stores",
     *      description="Returns list of stores",
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
     *          description="Successfully retrieved list of stores"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of stores")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of stores.');
        // api/store (GET)
        $stores = $this->getStores($request->user());
        if ($this->isEmpty($stores)) {
            return $this->errorPaginateResponse('Stores');
        } else {
            return $this->successPaginateResponse('Stores', $stores, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/store",
     *      operationId="filterStores",
     *      tags={"StoreControllerService"},
     *      summary="Filter list of stores",
     *      description="Returns list of filtered stores",
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
     *          description="Successfully retrieved list of filtered stores"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of stores")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered stores.');
        // api/store/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'store_id' => $request->store_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $stores = $this->getStores($request->user());
        $stores = $this->filterStores($stores, $params);

        if ($this->isEmpty($stores)) {
            return $this->errorPaginateResponse('Stores');
        } else {
            return $this->successPaginateResponse('Stores', $stores, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }


    /**
     * @OA\Get(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}",
     *   summary="Retrieves store by Uid.",
     *     operationId="getStoreByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Store has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the store."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/store/{storeid} (GET)
        error_log('Retrieving store of uid:' . $uid);
        $store = $this->getStore($uid);
        if ($this->isEmpty($store)) {
            return $this->notFoundResponse('Store');
        } else {
            return $this->successResponse('Store', $store, 'retrieve');
        }
    }



    /**
     * @OA\Post(
     *   tags={"StoreControllerService"},
     *   path="/api/store",
     *   summary="Creates a store.",
     *   operationId="createStore",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Storename",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="company_id",
     * in="query",
     * description="Company ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="user_id",
     * in="query",
     * description="User ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="companyBelongings",
     * in="query",
     * description="Store belongs to Company",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="contact",
     * in="query",
     * description="Contact",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email",
     * in="query",
     * description="Email",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="address",
     * in="query",
     * description="Address",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="postcode",
     * in="query",
     * description="Post Code",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="state",
     * in="query",
     * description="State",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="city",
     * in="query",
     * description="City",
     * @OA\Schema(
     *  type="string"
     *  )
     * ),
     * @OA\Parameter(
     * name="country",
     * in="query",
     * description="Country",
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
     *   @OA\Response(
     *     response=200,
     *     description="Store has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the store."
     *   )
     * )
     */
    public function store(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/store (POST)

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'desc' => 'required|string',
            'companyBelongings' => 'required|boolean',
        ]);
        error_log('Creating store.');
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'contact' => $request->contact,
            'email' => $request->email,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'companyBelongings' => $request->companyBelongings,
            'company_id' => $request->company_id,
            'user_id' => $request->user()->id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $store = $this->createStore($params);
        if ($this->isEmpty($store)) {
            DB::rollBack();
            $this->deleteImages($proccessingimgids);
            return $this->errorResponse();
        }

        //Associating Image Relationship
        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Store/". $store->uid);
            if(!$this->isEmpty($img)){
                $store->imgpath = $img->imgurl;
                $store->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($store)){
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

        $this->createLog($request->user()->id , [$store->id], 'create', 'store');
        DB::commit();
        return $this->successResponse('Store', $store, 'create');
    }


    /**
     * @OA\Post(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}",
     *   summary="Update store by Uid.",
     *     operationId="updateStoreByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
   * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Storename",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="company_id",
     * in="query",
     * description="Company ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="user_id",
     * in="query",
     * description="User ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="companyBelongings",
     * in="query",
     * description="Store belongs to Company",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="contact",
     * in="query",
     * description="Contact",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email",
     * in="query",
     * description="Email",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="address",
     * in="query",
     * description="Address",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="postcode",
     * in="query",
     * description="Post Code",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="state",
     * in="query",
     * description="State",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="city",
     * in="query",
     * description="City",
     * @OA\Schema(
     *  type="string"
     *  )
     * ),
     * @OA\Parameter(
     * name="country",
     * in="query",
     * description="Country",
     * @OA\Schema(
     *              type="string"
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
     *   @OA\Parameter(
     *     name="_method",
     *     in="query",
     *     description="For spoofing purposes.",
     *     required=false,
     *     example="PUT",
     *     @OA\Schema(type="string")
     *    ),
     *   @OA\Response(
     *     response=200,
     *     description="Store has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the store."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // api/store/{storeid} (PUT)
        error_log('Updating store of uid: ' . $uid);
        $store = $this->getStore($uid);
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'desc' => 'required|string',
            'companyBelongings' => 'required|boolean',
        ]);

        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->notFoundResponse('Store');
        }
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'contact' => $request->contact,
            'email' => $request->email,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'companyBelongings' => $request->companyBelongings,
            'company_id' => $request->company_id,
            'user_id' => $request->user()->id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $store = $this->updateStore($store, $params);

        if($this->isEmpty($store)){
            DB::rollBack();
            $this->deleteImages($proccessingimgids);
            return $this->errorResponse();
        }


        //Associating Image Relationship
        if($request->file('img') != null){
            error_log('got store image');
            $img = $this->uploadImage($request->file('img') , "/Store/". $store->uid);
            if(!$this->isEmpty($img)){
                error_log('inside edi');
                //Delete Previous Image
                if($store->imgpublicid){
                    if(!$this->deleteImage($store->imgpublicid)){
                        error_log('wrong 7 edi');
                        DB::rollBack();
                        return $this->errorResponse();
                    }
                }

                $store->imgpath = $img->imgurl;
                $store->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($store)){
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



        $this->createLog($request->user()->id , [$store->id], 'update', 'store');
        DB::commit();

        return $this->successResponse('Store', $store, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}",
     *   summary="Set store's 'status' to 0.",
     *     operationId="deleteStoreByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Store has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the store."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/store/{storeid} (DELETE)
        error_log('Deleting store of uid: ' . $uid);
        $store = $this->getStore($uid);
        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->notFoundResponse('Store');
        }
        $store = $this->deleteStore($store);
        $this->createLog($request->user()->id , [$store->id], 'delete', 'store');
        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            $this->createLog($request->user()->id , [$store->id], 'delete', 'store');
            DB::commit();
            return $this->successResponse('Store', null, 'delete');
        }
    }


    /**
     * @OA\Get(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}/promotions",
     *   summary="Retrieves store promotion plans by Uid.",
     *     operationId="getPromotionsByStoreUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store ID, NOT 'ID'.",
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
     *     description="Promotions has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the promotions."
     *   )
     * )
     */
    public function getPromotions(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving store promotion plans by uid:' . $uid);
        $store = $this->getStore($uid);
        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->notFoundResponse('Store');
        }
        $promotions = collect();
        $promotions = $promotions->merge($store->promotions()->where('status',true)->get());
        $promotions = $promotions->merge(ProductPromotion::where('store_id' , null)->get());
        $promotions = $promotions->unique('id')->sortBy('id')->flatten(1);


        if ($this->isEmpty($promotions)) {
            return $this->errorPaginateResponse('Promotions');
        } else {
            return $this->successPaginateResponse('Promotions', $promotions, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    /**
     * @OA\Get(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}/warranties",
     *   summary="Retrieves store warranties by Uid.",
     *     operationId="getWarrantiesByStoreUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store ID, NOT 'ID'.",
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
     *     description="Warranties has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the warranties."
     *   )
     * )
     */
    public function getWarranties(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving store warranties by uid:' . $uid);
        $store = $this->getStore($uid);
        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->notFoundResponse('Store');
        }
        $warranties = collect();
        $warranties = $warranties->merge($store->warranties()->where('status',true)->get());
        $warranties = $warranties->merge(Warranty::where('store_id' , null)->get());
        $warranties = $warranties->unique('id')->sortBy('id')->flatten(1);

        if ($this->isEmpty($warranties)) {
            return $this->errorPaginateResponse('Warranties');
        } else {
            return $this->successPaginateResponse('Warranties', $warranties, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}/shippings",
     *   summary="Retrieves store shippings by Uid.",
     *     operationId="getShippingsByStoreUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store ID, NOT 'ID'.",
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
     *     description="Shippings has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the shippings."
     *   )
     * )
     */
    public function getShippings(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving store shippings by uid:' . $uid);
        $store = $this->getStore($uid);
        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->notFoundResponse('Store');
        }
        $shippings = collect();
        $shippings = $shippings->merge($store->shippings()->where('status',true)->get());
        error_log($shippings->count());
        error_log($store->shippings);
        error_log($store->shippings()->where('status',true)->get());
        $shippings = $shippings->merge(Shipping::where('store_id' , null)->get());
        $shippings = $shippings->unique('id')->sortBy('id')->flatten(1);

        if ($this->isEmpty($shippings)) {
            return $this->errorPaginateResponse('Shippings');
        } else {
            return $this->successPaginateResponse('Shippigs', $shippings, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }


    /**
     * @OA\Get(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}/inventories",
     *   summary="Retrieves store inventories by Uid.",
     *     operationId="getInventoriesByStoreUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store ID, NOT 'ID'.",
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
     *     description="Inventories has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the inventories."
     *   )
     * )
     */
    public function getInventories(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving store inventories by uid:' . $uid);
        $store = $this->getStore($uid);
        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->notFoundResponse('Store');
        }
        $inventories = $store->inventories()->where('status' , true)->get();

        if ($this->isEmpty($inventories)) {
            return $this->errorPaginateResponse('Inventories');
        } else {
            return $this->successPaginateResponse('Inventories', $inventories, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}/vouchers",
     *   summary="Retrieves store vouchers by Uid.",
     *     operationId="getVouchersByStoreUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store ID, NOT 'ID'.",
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
     *     description="Vouchers has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the vouchers."
     *   )
     * )
     */
    public function getVouchers(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving store vouchers by uid:' . $uid);
        $store = $this->getStore($uid);
        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->notFoundResponse('Store');
        }
        $vouchers = $store->vouchers()->where('status' , true)->get();

        if ($this->isEmpty($vouchers)) {
            return $this->errorPaginateResponse('Vouchers');
        } else {
            return $this->successPaginateResponse('Vouchers', $vouchers, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    
    /**
     * @OA\Get(
     *   tags={"StoreControllerService"},
     *   path="/api/store/{uid}/sales",
     *   summary="Retrieves store sales by Uid.",
     *     operationId="getSalesByStoreUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Store ID, NOT 'ID'.",
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
     *     description="Sales has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieved the sales."
     *   )
     * )
     */
    public function getSales(Request $request, $uid)
    {
        error_log($this->controllerName.'Retrieving store sales by uid:' . $uid);
        $store = $this->getStore($uid);
        if ($this->isEmpty($store)) {
            DB::rollBack();
            return $this->notFoundResponse('Store');
        }
        $sales = collect();
        $sales = $sales->merge($store->sales()->where('status',true)->get());
        $sales = $sales->unique('id')->sortBy('id')->flatten(1);

        if ($this->isEmpty($sales)) {
            return $this->errorPaginateResponse('Sales');
        } else {
            return $this->successPaginateResponse('Sales', $sales, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }



}
