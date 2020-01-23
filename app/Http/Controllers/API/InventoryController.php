<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Inventory;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

class InventoryController extends Controller
{
    use AllServices;
    private $controllerName = '[InventoryController]';
     /**
     * @OA\Get(
     *      path="/api/inventory",
     *      operationId="getInventories",
     *      tags={"InventoryControllerService"},
     *      summary="Get list of inventories",
     *      description="Returns list of inventories",
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
     *          description="Successfully retrieved list of inventories"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of inventories")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of inventories.');
        // api/inventory (GET)
        $inventories = $this->getInventories($request->user());
        if ($this->isEmpty($inventories)) {
            return $this->errorPaginateResponse('Inventories');
        } else {
            return $this->successPaginateResponse('Inventories', $inventories, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/inventory",
     *      operationId="filterInventories",
     *      tags={"InventoryControllerService"},
     *      summary="Filter list of inventories",
     *      description="Returns list of filtered inventories",
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
     *     name="onsale",
     *     in="query",
     *     description="On sale for filter",
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
     *          description="Successfully retrieved list of filtered inventories"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of inventories")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered inventories.');
        // api/inventory/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'onsale' => $request->onsale,
            'inventory_id' => $request->inventory_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $inventories = $this->getInventories($request->user());
        $inventories = $this->filterInventories($inventories, $params);

        if ($this->isEmpty($inventories)) {
            return $this->errorPaginateResponse('Inventories');
        } else {
            return $this->successPaginateResponse('Inventories', $inventories, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }


    /**
     * @OA\Get(
     *   tags={"InventoryControllerService"},
     *   path="/api/inventory/{uid}",
     *   summary="Retrieves inventory by Uid.",
     *     operationId="getInventoryByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Inventory_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Inventory has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the inventory."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/inventory/{inventoryid} (GET)
        error_log('Retrieving inventory of uid:' . $uid);
        $inventory = $this->getInventory($uid);
        if ($this->isEmpty($inventory)) {
            return $this->notFoundResponse('Inventory');
        } else {
            return $this->successResponse('Inventory', $inventory, 'retrieve');
        }
    }



    /**
     * @OA\Post(
     *   tags={"InventoryControllerService"},
     *   path="/api/inventory",
     *   summary="Creates a inventory.",
     *   operationId="createInventory",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Inventoryname",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="store_id",
     * in="query",
     * description="Store ID",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="product_promotion_id",
     * in="query",
     * description="Promotion ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="warranty_id",
     * in="query",
     * description="Warranty ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="shipping_id",
     * in="query",
     * description="Shipping ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="inventoryfamilies",
     * in="query",
     * description="Inventory Families",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="code",
     * in="query",
     * description="Code",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="sku",
     * in="query",
     * description="Sku",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Product Description",
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
*                  @OA\Property(
*                      property="sliders[]",
*                      description="Sliders Image",
*                      type="file",
*                      @OA\Items(type="string", format="binary")
*                   ),
*               ),
*           ),
*       ),
     * @OA\Parameter(
     * name="cost",
     * in="query",
     * description="Product Cost",
     * required=true,
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="price",
     * in="query",
     * description="Product Base Price",
     * required=true,
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="stockthreshold",
     * in="query",
     * description="Stock Threshold",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Inventory has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the inventory."
     *   )
     * )
     */
    public function store(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/inventory (POST)

        $this->validate($request, [
            'store_id' => 'required',
            'name' => 'required|string|max:191',
            'code' => 'nullable',
            'sku' => 'required|string|max:191',
            'desc' => 'nullable',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);
        error_log($this->controllerName.'Creating inventory.');
        $params = collect([
            'store_id' => $request->store_id,
            'product_promotion_id' => $request->product_promotion_id,
            'warranty_id' => $request->warranty_id,
            'shipping_id' => $request->shipping_id,
            'name' => $request->name,
            'code' => $request->code,
            'sku' => $request->sku,
            'desc' => $request->desc,
            'cost' => $request->cost,
            'price' => $request->price,
            'stockthreshold' => $request->stockthreshold,
        ]);
        $params = json_decode(json_encode($params));
        $inventory = $this->createInventory($params);
        if ($this->isEmpty($inventory)) {
            DB::rollBack();
            $this->deleteImages($proccessingimgids);
            return $this->errorResponse();
        }


        //Associating Image Relationship
        if($request->file('img') != null){
            error_log('Image Is Detected');
            $img = $this->uploadImage($request->file('img') , "/Inventory/". $inventory->uid);
            if(!$this->isEmpty($img)){
                $inventory->imgpath = $img->imgurl;
                $inventory->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($inventory)){
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

        $count = 0;
        if($request->file('sliders') != null){
            error_log('Slider Images Is Detected');
            $sliders = $request->file('sliders');
            foreach($sliders as $slider){
                error_log('Inside slider');
                $count++;
                if($count > 6){
                    break;
                }
                $img = $this->uploadImage($slider , "/Inventory/". $inventory->uid . "/sliders");
                error_log(collect($img));
                if(!$this->isEmpty($img)){
                    $proccessingimgids->push($img->publicid);

                    $params = collect([
                        'imgpath' => $img->imgurl,
                        'imgpublicid' => $img->publicid,
                        'inventory_id' => $inventory->refresh()->id,
                    ]);
                    $params = json_decode(json_encode($params));
                    //Attach Image to InventoryImage
                    $inventoryimage = $this->createInventoryImage($params);
                    if($this->isEmpty($inventoryimage)){
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

        //Associating Inventory Family Relationship
        $inventoryfamilies = json_decode($request->inventoryfamilies);
        if(!$this->isEmpty($inventoryfamilies)){
           foreach($inventoryfamilies as $inventoryfamily){
                $patterns = $inventoryfamily->patterns;

                $inventoryfamily->inventory_id = $inventory->refresh()->id;
                $inventoryfamily = $this->createInventoryFamily($inventoryfamily);
                if($this->isEmpty($inventoryfamily)){
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }

                $inventoryfamily->inventory()->associate($inventory);
                if(!$this->saveModel($inventoryfamily)){
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }
                $this->createLog($request->user()->id , [$inventoryfamily->id], 'create', 'inventoryfamily');

                //Patterns
                foreach($patterns as $pattern){

                    $pattern->inventory_family_id = $inventoryfamily->refresh()->id;
                    $pattern = $this->createPattern($pattern);
                    if($this->isEmpty($pattern)){
                        error_log("here");
                        DB::rollBack();
                        $this->deleteImages($proccessingimgids);
                        return $this->errorResponse();
                    }
                    $pattern->inventoryfamily()->associate($inventoryfamily);
                    if(!$this->saveModel($pattern)){
                        DB::rollBack();
                        $this->deleteImages($proccessingimgids);
                        return $this->errorResponse();
                    }
                    $this->createLog($request->user()->id , [$pattern->id], 'create', 'pattern');
                    if($this->isEmpty($pattern)){
                        DB::rollBack();
                        $this->deleteImages($proccessingimgids);
                        return $this->errorResponse();
                    }

                }
           }
        }else{
            error_log('error here4');
            DB::rollBack();
            $this->deleteImages($proccessingimgids);
            return $this->errorResponse();
        }

        if(!$this->syncInventoryById($inventory->id)){
            DB::rollBack();
            $this->deleteImages($proccessingimgids);
            return $this->errorResponse();
        }

        if(!$this->addInventoryToProductFeature($inventory)){
            DB::rollBack();
            $this->deleteImages($proccessingimgids);
            return $this->errorResponse();
        }
        

        $this->createLog($request->user()->id , [$inventory->id], 'create', 'inventory');
        DB::commit();

        return $this->successResponse('Inventory', $inventory, 'create');
    }


    /**
     * @OA\Put(
     *   tags={"InventoryControllerService"},
     *   path="/api/inventory/{uid}",
     *   summary="Update inventory by Uid.",
     *     operationId="updateInventoryByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Inventory_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Inventoryname",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="store_id",
     * in="query",
     * description="Store ID",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="product_promotion_id",
     * in="query",
     * description="Promotion ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="warranty_id",
     * in="query",
     * description="Warranty ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="shipping_id",
     * in="query",
     * description="Shipping ID",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="inventoryfamilies",
     * in="query",
     * description="Inventory Families",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="code",
     * in="query",
     * description="Code",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="sku",
     * in="query",
     * description="Sku",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Product Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="cost",
     * in="query",
     * description="Product Cost",
     * required=true,
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="price",
     * in="query",
     * description="Product Selling Price",
     * required=true,
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="qty",
     * in="query",
     * description="Stock Qty",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="stockthreshold",
     * in="query",
     * description="Stock Threshold",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="onsale",
     * in="query",
     * description="On Sale",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Inventory has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the inventory."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        error_log("test");
        $proccessingimgids = collect();
        DB::beginTransaction();
        // api/inventory/{inventoryid} (PUT)
        error_log($this->controllerName.'Updating inventory of uid: ' . $uid);
        $this->validate($request, [
            'store_id' => 'required',
            'name' => 'required|string|max:191',
            'code' => 'nullable',
            'sku' => 'required|string|max:191',
            'desc' => 'nullable',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|numeric|min:0',
            'onsale' => 'required|numeric',
        ]);

        $inventory = $this->getInventory($uid);
        if ($this->isEmpty($inventory)) {
            DB::rollBack();
            return $this->notFoundResponse('Inventory');
        }

        $params = collect([
            'store_id' => $request->store_id,
            'product_promotion_id' => $request->product_promotion_id,
            'warranty_id' => $request->warranty_id,
            'shipping_id' => $request->shipping_id,
            'name' => $request->name,
            'code' => $request->code,
            'sku' => $request->sku,
            'desc' => $request->desc,
            'cost' => $request->cost,
            'price' => $request->price,
            'qty' => $request->qty,
            'stockthreshold' => $request->stockthreshold,
            'onsale' => $request->onsale,
        ]);
        $params = json_decode(json_encode($params));

        //Updating inventory
        $inventory = $this->updateInventory($inventory, $params);
        if($this->isEmpty($inventory)){
            DB::rollBack();
            $this->deleteImages($proccessingimgids);
            return $this->errorResponse();
        }

        //Associating Inventory Family Relationship
        $inventoryfamilies = collect(json_decode($request->inventoryfamilies));
        $originvfamiliesids = $inventory->inventoryfamilies()->where('status',true)->pluck('id');
        $inventoryfamiliesids = $inventoryfamilies->pluck('id');
        $fordeleteids = $originvfamiliesids->diff($inventoryfamiliesids);


        foreach($inventoryfamilies as $inventoryfamily){
            //Insert New Inventory Family
            if($inventoryfamily->id == null){
                $patterns = $inventoryfamily->patterns;

                $inventoryfamily->inventory_id = $inventory->refresh()->id;
                $inventoryfamily = $this->createInventoryFamily($inventoryfamily);
                if($this->isEmpty($inventoryfamily)){
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }

                $inventoryfamily->inventory()->associate($inventory);
                if(!$this->saveModel($inventoryfamily)){
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }
                $this->createLog($request->user()->id , [$inventoryfamily->id], 'create', 'inventoryfamily');


                //Patterns
                foreach($patterns as $pattern){
                    $pattern->inventory_family_id = $inventoryfamily->refresh()->id;
                    $pattern = $this->createPattern($pattern);
                    if($this->isEmpty($pattern)){
                        error_log("here");
                        DB::rollBack();
                        $this->deleteImages($proccessingimgids);
                        return $this->errorResponse();
                    }
                    $pattern->inventoryfamily()->associate($inventoryfamily);
                    if(!$this->saveModel($pattern)){
                        DB::rollBack();
                        $this->deleteImages($proccessingimgids);
                        return $this->errorResponse();
                    }
                    
                    $this->createLog($request->user()->id , [$pattern->id], 'create', 'pattern');
                }

                
                
            }else{
                //Update Existing Inventory Family
                $oriinventoryfamily = $this->getInventoryFamilyById($inventoryfamily->id);
                if($this->isEmpty($oriinventoryfamily)){
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->notFoundResponse('InventoryFamily');
                }
                $oriinventoryfamily = $this->updateInventoryFamily($oriinventoryfamily , $inventoryfamily);
                if($this->isEmpty($oriinventoryfamily)){
                     DB::rollBack();
                     $this->deleteImages($proccessingimgids);
                     return $this->notFoundResponse('InventoryFamily');
                }
                
                $patterns = $inventoryfamily->patterns;
                foreach($patterns as $pattern){
                    if($pattern->id == null){
                        $pattern->inventory_family_id = $oriinventoryfamily->refresh()->id;
                        $pattern = $this->createPattern($pattern);
                        if($this->isEmpty($pattern)){
                            error_log("here");
                            DB::rollBack();
                            $this->deleteImages($proccessingimgids);
                            return $this->errorResponse();
                        }
                        $pattern->inventoryfamily()->associate($oriinventoryfamily);
                        if(!$this->saveModel($pattern)){
                            DB::rollBack();
                            $this->deleteImages($proccessingimgids);
                            return $this->errorResponse();
                        }
                        
                        $this->createLog($request->user()->id , [$pattern->id], 'create', 'pattern');
                    }else{
                        $oripattern = $this->getPatternById($pattern->id);
                        if($this->isEmpty($oripattern)){
                            DB::rollBack();
                            $this->deleteImages($proccessingimgids);
                            return $this->notFoundResponse('Pattern');
                        }
                        $oripattern = $this->updatePattern($oripattern , $pattern);
                        if($this->isEmpty($pattern)){
                            error_log("here");
                            DB::rollBack();
                            $this->deleteImages($proccessingimgids);
                            return $this->errorResponse();
                        }
                    }
                }

            }
       }
       error_log($fordeleteids);
       //Delete InventoryFamily
        foreach($fordeleteids as $id){
            $inventoryfamily = $this->getInventoryFamilyById($id);
            if($this->isEmpty($inventoryfamily)){
                 DB::rollBack();
                 $this->deleteImages($proccessingimgids);
                 return $this->notFoundResponse('InventoryFamily');
             }
            if(!$this->deleteInventoryFamily($inventoryfamily)){
                DB::rollBack();
                $this->deleteImages($proccessingimgids);
                return $this->errorResponse();
            }
        }


        if(!$this->syncInventoryById($inventory->id)){
            DB::rollBack();
            $this->deleteImages($proccessingimgids);
            return $this->errorResponse();
        }

        $this->createLog($request->user()->id , [$inventory->id], 'update', 'inventory');
        DB::commit();

        return $this->successResponse('Inventory', $inventory, 'update');
    }

    /**
     * @OA\Delete(
     *   tags={"InventoryControllerService"},
     *   path="/api/inventory/{uid}",
     *   summary="Set inventory's 'status' to 0.",
     *     operationId="deleteInventoryByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Inventory ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Inventory has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the inventory."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/inventory/{inventoryid} (DELETE)
        error_log('Deleting inventory of uid: ' . $uid);
        $inventory = $this->getInventory($uid);
        if ($this->isEmpty($inventory)) {
            DB::rollBack();
            return $this->notFoundResponse('Inventory');
        }
        $inventory = $this->deleteInventory($inventory);
        if ($this->isEmpty($inventory)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            $this->createLog($request->user()->id , [$inventory->id], 'delete', 'inventory');
            DB::commit();
            return $this->successResponse('Inventory', null, 'delete');
        }
    }


    /**
     * @OA\Get(
     *   tags={"InventoryControllerService"},
     *   path="/api/inventory/{uid}/onsale",
     *   summary="Retrieves onsale inventory by Uid.",
     *     operationId="getOnSaleInventoryByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Inventory_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Inventory has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the inventory."
     *   )
     * )
     */
    public function getOnSaleInventory(Request $request, $uid)
    {
        // api/inventory/{inventoryid} (GET)
        error_log($this->controllerName.'Retrieving onsale inventory of uid:' . $uid);
        $cols = $this->inventoryDefaultCols();
        $inventory = $this->getInventory($uid);
        if($inventory->onsale){
            $inventory = $this->itemPluckCols($inventory , $cols);
            $inventory = json_decode(json_encode($inventory));
            $inventory = $this->calculateInventoryPromotionPrice($inventory);
            $inventory = $this->countProductReviews($inventory);
        }else{
            $inventory = null;
        }
        if ($this->isEmpty($inventory)) {
            return $this->notFoundResponse('Inventory');
        } else {
            return $this->successResponse('Inventory', $inventory, 'retrieve');
        }
    }

    
    /**
     * @OA\Get(
     *   tags={"InventoryControllerService"},
     *   path="/api/inventories/onsale/filter",
     *   summary="Filter onsale inventories.",
     *     operationId="filterOnSaleInventories",
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
     *     description="Inventory has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the inventory."
     *   )
     * )
     */
    public function filterOnSaleInventories(Request $request)
    {
        // api/inventory/{inventoryid} (GET)
        error_log($this->controllerName.'Filtering Onsale Inventories');
        $inventories = $this->getAllOnSaleInventories();
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'onsale' => true,
        ]);
        $params = json_decode(json_encode($params));
        $inventories = $this->filterInventories($inventories, $params);
        $inventories = collect($inventories)->map(function ($item, $key) {
            return $this->calculateInventoryPromotionPrice($item);
        });

        if ($this->isEmpty($inventories)) {
            return $this->errorPaginateResponse('Inventories');
        } else {
            return $this->successPaginateResponse('Inventories', $inventories, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    

    /**
     * @OA\Post(
     *   tags={"InventoryControllerService"},
     *   path="/api/thumbnailupload/inventory",
     *   summary="Change inventory Thumbnail by Uid.",
     *     operationId="uploadInventoryThumbnail",
     * @OA\Parameter(
     * name="inventory_id",
     * in="query",
     * description="Inventory Id",
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
     *     description="Inventory has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the inventory."
     *   )
     * )
     */
    public function uploadThumbnail(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // api/inventory/{inventoryid} (GET)
        error_log($this->controllerName.' uploding thumbnail');
        $this->validate($request, [
            'inventory_id' => 'required',
            'img' => 'required',
        ]);
        //Associating Image Relationship
        $inventory = $this->getInventoryById($request->inventory_id);
        if ($this->isEmpty($inventory)) {
            DB::rollBack();
            return $this->notFoundResponse('Inventory');
        }

        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Inventory/". $inventory->uid);
            if(!$this->isEmpty($img)){
                //Delete Previous Image
                if($inventory->imgpublicid){
                    if(!$this->deleteImage($inventory->imgpublicid)){
                        error_log('wrong 7 edi');
                        DB::rollBack();
                        return $this->errorResponse();
                    }
                }
                $inventory->imgpath = $img->imgurl;
                $inventory->imgpublicid = $img->publicid;
                $proccessingimgids->push($img->publicid);
                if(!$this->saveModel($inventory)){
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
        return $this->successResponse('Inventory', $inventory, 'update');
    }
}
