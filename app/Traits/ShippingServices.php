<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\Shipping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait ShippingServices {

    use AllServices;

    private function getShippings($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $stores = $this->getStores($requester);
        foreach($stores as $store){
            $data = $data->merge($store->shippings()->where('status',true)->get());
        }

        $data = $data->merge(Shipping::where('store_id',null)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterShippings($data , $params) {


        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params , $this->shippingFilterCols());

        if($params->store_id){
            error_log('Filtering shippings with store_id....');
            $store_id = $params->store_id;
            $data = $data->filter(function ($item) use ($store_id) {
                return $item->store_id == $store_id;
            });
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getShipping($uid) {

        $data = Shipping::where('uid', $uid)->where('status', true)->first();
        return $data;

    }
    
    private function getShippingById($id) {

        $data = Shipping::where('id', $id)->where('status', true)->first();
        return $data;

    }

    //Make Sure Shipping is not empty when calling this function
    private function createShipping($params) {

        $params = $this->checkUndefinedProperty($params , $this->shippingAllCols());
        $data = new Shipping();
        
        $data->uid = Carbon::now()->timestamp . Shipping::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->price = $this->toDouble($params->price);
        $data->maxweight = $this->toDouble($params->maxweight);
        $data->maxdimension = $this->toDouble($params->maxdimension);

        error_log($params->store_id);
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

    //Make Sure Shipping is not empty when calling this function
    private function updateShipping($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->shippingAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->price = $this->toDouble($params->price);
        $data->maxweight = $this->toDouble($params->maxweight);
        $data->maxdimension = $this->toDouble($params->maxdimension);

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

    private function deleteShipping($data) {
        $inventories = $data->inventories;
        foreach($inventories as $inventory){
            $inventory->shipping()->dissociate();
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
    public function shippingAllCols() {

        return ['id','uid', 'store_id', 'name' ,'desc', 'price', 'maxweight' , 'maxdimension' , 'status'];

    }
    
    public function shippingFilterCols() {

        return ['store_id'];

    }
    
    


}
