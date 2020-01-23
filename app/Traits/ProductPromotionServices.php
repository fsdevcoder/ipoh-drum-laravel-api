<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ProductPromotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait ProductPromotionServices {

    use AllServices;

    private function getProductPromotions($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $stores = $this->getStores($requester);
        foreach($stores as $store){
            $data = $data->merge($store->promotions()->where('status',true)->get());
        }

        $data = $data->merge(ProductPromotion::where('store_id',null)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterProductPromotions($data , $params) {

        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params , $this->productPromotionFilterCols());

        if($params->store_id){
            error_log('Filtering promotion with store_id....');
            $store_id = $params->store_id;
            $data = $data->filter(function ($item) use ($store_id) {
                return $item->store_id == $store_id;
            });
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getProductPromotion($uid) {

        $data = ProductPromotion::where('uid', $uid)->where('status', true)->first();
        return $data;

    }

    private function getProductPromotionById($id) {

        $data = ProductPromotion::where('id', $id)->where('status', true)->first();
        return $data;

    }

    //Make Sure ProductPromotion is not empty when calling this function
    private function createProductPromotion($params) {

        $params = $this->checkUndefinedProperty($params , $this->productPromotionAllCols());
        $data = new ProductPromotion();

        $data->uid = Carbon::now()->timestamp . ProductPromotion::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->qty = $this->toInt($params->qty);
        $data->discbyprice = $params->discbyprice;
        $data->promostartdate = $this->toDate($params->promostartdate);
        $data->promoenddate = $this->toDate($params->promoenddate);

        if($data->discbyprice){
            $data->disc = $this->toDouble($params->disc);
            $data->discpctg = 0;
        }else{
            $data->discpctg = $this->toInt($params->discpctg);
            $data->disc = 0;
        }

        
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

    //Make Sure ProductPromotion is not empty when calling this function
    private function updateProductPromotion($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->productPromotionAllCols());

       $data->name = $params->name;
        $data->desc = $params->desc;
        $data->qty = $this->toInt($params->qty);
        $data->discbyprice = $params->discbyprice;
        $data->promostartdate = $this->toDate($params->promostartdate);
        $data->promoenddate = $this->toDate($params->promoenddate);

        if($data->discbyprice){
            $data->disc = $this->toDouble($params->disc);
            $data->discpctg = 0;
        }else{
            $data->discpctg = $this->toInt($params->discpctg);
            $data->disc = 0;
        }
        
        
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

    private function deleteProductPromotion($data) {

        //Remove the relationship
        $inventories = $data->inventories;
        foreach($inventories as $inventory){
            $inventory->promotion()->dissociate();
            if(!$this->saveModel($inventory)){
                return null;
            }
        }
        $tickets = $data->tickets;
        foreach($tickets as $ticket){
            $ticket->promotion()->dissociate();
            if(!$this->saveModel($ticket)){
                return null;
            }
        }
        //Cancel promotion
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
    }

    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function productPromotionAllCols() {

        return ['id','uid', 'store_id', 'name' ,'desc', 'qty', 'disc' , 'discpctg' , 
        'discbyprice' , 'promostartdate', 'promoenddate' , 'status' ];

    }
    
    
    public function productPromotionFilterCols() {

        return ['store_id'];

    }


}
