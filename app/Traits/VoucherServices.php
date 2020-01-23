<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\Voucher;
use App\InventoryFamily;
use App\ProductPromotion;
use App\Warranty;
use App\Shipping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait VoucherServices {

    use AllServices;

    private function getVouchers($requester) {

        $data = collect();
        //Role Based Retrieve Done in Store Services
        $stores = $this->getStores($requester);
        foreach($stores as $store){
            $data = $data->merge($store->vouchers()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterVouchers($data , $params) {


        if($params->keyword){
            error_log('Filtering vouchers with keyword....');
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
            error_log('Filtering vouchers with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering vouchers with todate....');
            $date = Carbon::parse($params->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering vouchers with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }

        if($params->onsale){
            error_log('Filtering vouchers with on sale status....');
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

    private function getVoucher($uid) {

        $data = Voucher::where('uid', $uid)->where('status', true)->first();
        return $data;

    }
    
    private function getVoucherById($id) {

        $data = Voucher::where('id', $id)->where('status', true)->first();
        return $data;

    }

    //Make Sure Voucher is not empty when calling this function
    private function createVoucher($params) {

        $params = $this->checkUndefinedProperty($params , $this->voucherAllCols());

        $data = new Voucher();

        $data->uid = Carbon::now()->timestamp . Voucher::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->unlimited  = $params->unlimited;
        $data->discbyprice  = $params->discbyprice;
        $data->minpurchase  = $this->toDouble($params->minpurchase);
        $data->minqty  = $this->toInt($params->minqty);
        $data->minvariety = $this->toInt($params->minvariety);
        $data->startdate = $this->toDate($params->startdate);
        $data->enddate = $this->toDate($params->enddate);
        
        if($data->unlimited){
            $data->qty = 0;
        }else{
            $data->qty = $this->toInt($params->qty);
        }

        if($data->discbyprice){
            $data->disc = $this->toDouble($params->disc);
            $data->discpctg = 0;
        }else{
            $data->discpctg = $this->toDouble($params->discpctg / 100);
            $data->disc = 0;
        }

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);
        
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Voucher is not empty when calling this function
    private function updateVoucher($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->voucherAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->unlimited  = $params->unlimited;
        $data->discbyprice  = $params->discbyprice;
        $data->minpurchase  = $this->toDouble($params->minpurchase);
        $data->minqty  = $this->toInt($params->minqty);
        $data->minvariety = $this->toInt($params->minvariety);
        $data->startdate = $this->toDate($params->startdate);
        $data->enddate = $this->toDate($params->enddate);
        
        if($data->unlimited){
            $data->qty = 0;
        }else{
            $data->qty = $this->toInt($params->qty);
        }

        if($data->discbyprice){
            $data->disc = $this->toDouble($params->disc);
            $data->discpctg = 0;
        }else{
            $data->discpctg = $this->toInt($params->discpctg);
            $data->disc = 0;
        }

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);
        
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        
        return $data->refresh();
    }

    private function deleteVoucher($data) {
        $vouchercodes = $data->vouchercodes;
        foreach($vouchercodes as $vouchercode){
            $this->deleteVoucherCode($vouchercode);
        }
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
    }


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function voucherAllCols() {

        return ['id','uid', 'store_id', 'name', 'desc', 
        'qty', 'redeemqty', 'disc', 'discpctg', 'discbyprice', 'startdate', 'enddate', 
        'minpurchase', 'minqty', 'minvariety', 'status'];

    }

}
