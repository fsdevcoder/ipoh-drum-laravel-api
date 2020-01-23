<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\InventoryImage;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\InventoryImageServices;
use App\Traits\InventoryServices;
use App\Traits\LogServices;

class InventoryImageController extends Controller
{
    use GlobalFunctions, NotificationFunctions, InventoryImageServices, InventoryServices, LogServices;
    private $controllerName = '[InventoryImageController]';


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
     *   tags={"InventoryImageControllerService"},
     *   path="/api/inventoryimage",
     *   summary="Creates a inventoryimage.",
     *   operationId="createInventoryImage",
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
     *     description="InventoryImage has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the inventoryimage."
     *   )
     * )
     */
    public function store(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/inventoryimage (POST)
        
        $this->validate($request, [
            'img' => 'required',
            'inventory_id' => 'required',
        ]);
        error_log('Creating inventoryimage.');
        
        $inventory = $this->getInventoryById($request->inventory_id);
        if ($this->isEmpty($inventory)) {
            DB::rollBack();
            return $this->notFoundResponse('Inventory');
        }

        if($request->file('img') != null){
            $img = $this->uploadImage($request->file('img') , "/Inventory/". $inventory->uid);
            if(!$this->isEmpty($img)){
                $params = collect([
                    'name' => $request->name,
                    'desc' => $request->desc,
                    'imgpath' => $img->imgurl,
                    'imgpublicid' => $img->publicid,
                    'inventory_id' => $request->inventory_id,
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
        $inventoryimage = $this->createInventoryImage($params);

        if ($this->isEmpty($inventoryimage)) {
            error_log("here");
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('InventoryImage', $inventoryimage, 'create');
        }
    }


    public function update(Request $request, $uid)
    {
       
    }


    /**
     * @OA\Delete(
     *   tags={"InventoryImageControllerService"},
     *   path="/api/inventoryimage/{uid}",
     *   summary="Set inventoryimage's 'status' to 0.",
     *     operationId="deleteInventoryImageByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="InventoryImage ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="InventoryImage has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the inventoryimage."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/inventoryimage/{inventoryimageid} (DELETE)
        error_log('Deleting inventoryimage of uid: ' . $uid);
        $inventoryimage = $this->getInventoryImage($uid);
        if ($this->isEmpty($inventoryimage)) {
            DB::rollBack();
            return $this->notFoundResponse('InventoryImage');
        }

        if (!$this->deleteInventoryImage($inventoryimage)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('InventoryImage', null, 'delete');
        }
    }


}
