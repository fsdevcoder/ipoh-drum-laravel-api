<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ProductPromotion;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait TicketServices {

    use AllServices;

    private function getTickets($requester) {

        $data = collect();
        //Role Based Retrieve Done in Store
        $stores = $this->getStores($requester);
        foreach($stores as $store){
            $data = $data->merge($store->tickets()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterTickets($data , $params) {

        error_log('Filtering tickets....');

        if($params->keyword){
            error_log('Filtering tickets with keyword....');
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
            error_log('Filtering tickets with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering tickets with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering tickets with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }

        if($params->onsale){
            error_log('Filtering tickets with on sale status....');
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

    private function getTicket($uid) {
        $data = Ticket::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getTicketById($id) {
        $data = Ticket::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createTicket($params) {

        $params = $this->checkUndefinedProperty($params , $this->ticketAllCols());

        $data = new Ticket();
        $data->uid = Carbon::now()->timestamp . Ticket::count();
        $data->name = $params->name;
        $data->code = $params->code;
        $data->sku = $params->sku;
        $data->desc = $params->desc;
        $data->price = $this->toDouble($params->price);
        $data->enddate = $this->toDate($params->enddate);
        $data->qty = $this->toInt($params->qty);
        $data->salesqty = 0;
        $data->stockthreshold = $this->toInt($params->stockthreshold);
        $data->onsale = $params->onsale;

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);
           
        $promotion = $this->getProductPromotionById($params->product_promotion_id);
        if($this->isEmpty($promotion)){
            return null;
        }else{
            if($promotion->qty > 0){
                $data->promoendqty = $data->salesqty + $promotion->qty;
            }
        }

        $data->promotion()->associate($promotion);

        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Ticket is not empty when calling this function
    private function updateTicket($data,  $params) {
        
        $params = $this->checkUndefinedProperty($params , $this->ticketAllCols());

        $data->name = $params->name;
        $data->code = $params->code;
        $data->sku = $params->sku;
        $data->desc = $params->desc;
        $data->price = $this->toDouble($params->price);
        $data->enddate = $this->toDate($params->enddate);
        $data->qty = $this->toInt($params->qty);
        $data->salesqty = 0;
        $data->stockthreshold = $this->toInt($params->stockthreshold);
        $data->onsale = $params->onsale;

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);
           
        $promotion = $this->getProductPromotionById($params->product_promotion_id);
        if($this->isEmpty($promotion)){
            return null;
        }else{
            if($promotion->qty > 0){
                $data->promoendqty = $data->salesqty + $promotion->qty;
            }
        }

        $data->promotion()->associate($promotion);

        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    private function deleteTicket($data) {
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function ticketAllCols() {

        return ['id','store_id', 'product_promotion_id', 'uid', 
        'code' , 'sku' , 'name'  , 'imgpublicid', 'imgpath' , 'desc' , 'rating' , 
        'price' , 'qty','promoendqty','salesqty','stockthreshold','status','onsale'];

    }

    public function ticketDefaultCols() {

        return ['id','uid' ,'onsale', 'onpromo', 'name' , 'desc' , 'price' , 'disc' , 
        'discpctg' , 'promoprice' , 'promostartdate' , 'promoenddate', 'enddate' , 
        'stock', 'salesqty' ];

    }

}
