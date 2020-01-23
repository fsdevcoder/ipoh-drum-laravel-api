<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\Warranty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait WarrantyServices {

    use AllServices;

    private function getWarranties($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $stores = $this->getStores($requester);
        foreach($stores as $store){
            $data = $data->merge($store->warranties()->where('status',true)->get());
        }

        $data = $data->merge(Warranty::where('store_id',null)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterWarranties($data , $params) {

        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params , $this->warrantyFilterCols());

        if($params->store_id){
            error_log('Filtering warranties with store_id....');
            $store_id = $params->store_id;
            $data = $data->filter(function ($item) use ($store_id) {
                return $item->store_id == $store_id;
            });
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getWarranty($uid) {

        $data = Warranty::where('uid', $uid)->where('status', true)->first();
        return $data;

    }

    private function getWarrantyById($id) {

        $data = Warranty::where('id', $id)->where('status', true)->first();
        return $data;

    }

    //Make Sure Warranty is not empty when calling this function
    private function createWarranty($params) {

        $params = $this->checkUndefinedProperty($params , $this->warrantyAllCols());
        $data = new Warranty();
        
        $data->uid = Carbon::now()->timestamp . Warranty::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->period = $this->toInt($params->period);
        $data->policy =  $params->policy;

        if($params->store_id){
            $store = $this->getStoreById($params->store_id);
            if($this->isEmpty($store)){
                return null;
            }
            $data->store()->associate($store);

        }
        
        $data->status = true;

        if(!$this->saveModel($data)){
            return null;
        }
        
      
        return $data->refresh();
    }

    //Make Sure Warranty is not empty when calling this function
    private function updateWarranty($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->warrantyAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->period = $this->toInt($params->period);
        $data->policy =  $params->policy;

        if($params->store_id){
            $store = $this->getStoreById($params->store_id);
            if($this->isEmpty($store)){
                return null;
            }
            $data->store()->associate($store);

        }
        
        $data->status = true;

        if(!$this->saveModel($data)){
            return null;
        }
        
      
        return $data->refresh();

    }

    private function deleteWarranty($data) {
        $inventories = $data->inventories;
        foreach($inventories as $inventory){
            $inventory->warranty()->dissociate();
            if(!$this->saveModel($inventory)){
                return null;
            }
        }
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
    }

    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function warrantyAllCols() {

        return ['id','uid', 'store_id', 'name' ,'desc', 'period', 'policy' , 'status'];

    }
    
    
    public function warrantyFilterCols() {

        return ['store_id'];

    }
    


}
