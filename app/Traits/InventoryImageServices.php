<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\InventoryImage;
use App\InventoryImageFamily;
use App\InventoryImageImage;
use App\ProductPromotion;
use App\Warranty;
use App\Shipping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait InventoryImageServices {

    use AllServices;

    private function getInventoryImages($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $inventories = $this->getInventoryImages($requester);
        foreach($inventories as $inventory){
            $data = $data->merge($inventory->images()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterInventoryImages($data , $params) {


        if($params->keyword){
            error_log('Filtering inventoryimages with keyword....');
            $keyword = $params->keyword;
            $data = $data->filter(function($item)use($keyword){
                //check string exist inside or not
                if(stristr($item->name, $keyword) == TRUE || stristr($item->uid, $keyword) == TRUE ) {
                    return true;
                }else{
                    return false;
                }

            });
        }


        if($params->fromdate){
            error_log('Filtering inventoryimages with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering inventoryimages with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering inventoryimages with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }

        if($params->onsale){
            error_log('Filtering inventoryimages with on sale status....');
            if($params->onsale == 'true'){
                $data = $data->where('onsale', true);
            }else if($params->onsale == 'false'){
                $data = $data->where('onsale', false);
            }else{
                $data = $data->where('onsale', '!=', null);
            }
        }


        $data = $data->unique('id');

        return $data;
    }

    private function getInventoryImage($uid) {

        $data = InventoryImage::where('uid', $uid)->where('status', true)->first();
        return $data;

    }
    
    private function getInventoryImageById($id) {

        $data = InventoryImage::where('id', $id)->where('status', true)->first();
        return $data;

    }
    
    //Make Sure InventoryImage is not empty when calling this function
    private function createInventoryImage($params) {

        $params = $this->checkUndefinedProperty($params , $this->inventoryImageAllCols());

        $inventory = $this->getInventoryById($params->inventory_id);
        if($this->isEmpty($inventory)){
            return null;
        }

        if($inventory->images()->count() >= 6){
            return null;
        }

        $data = new InventoryImage();
        $data->uid = Carbon::now()->timestamp . InventoryImage::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->imgpath = $params->imgpath;
        $data->imgpublicid = $params->imgpublicid;

        $data->inventory()->associate($inventory);

        if(!$this->saveModel($data)){
            return null;
        }
        
        return $data->refresh();
    }

    //Make Sure InventoryImage is not empty when calling this function
    private function updateInventoryImage($data,  $params) {


    }

    public function deleteInventoryImage($data)
    {
        error_log($data->imgpublicid);
        if($this->deleteImage($data->imgpublicid)){
            $data->delete();
            return true;
        }else{
            return false;
        }
    }


  
    //Relationship Deassociating
    //===============================================================================================================================================================================
    
    

    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    
    
    public function inventoryImageDefaultCols() {

        return ['id','uid', 'inventory_id', 'name' ,'desc', 'imgpublicid', 'imgpath' , 'status'];

    }

    public function inventoryImageAllCols() {

        return ['id','uid', 'inventory_id', 'name' ,'desc', 'imgpublicid', 'imgpath' , 'status'];

    }
    

    


}
