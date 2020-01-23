<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait SaleServices {

    use AllServices;

    private function getSales($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $stores = $this->getStores($requester);
        foreach($stores as $store){
            $data = $data->merge($store->sales()->with('user','saleitems','store')->where('status',true)->get());
        }


        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterSales($data , $params) {

        error_log('Filtering sales....');

        if($params->keyword){
            error_log('Filtering sales with keyword....');
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
            error_log('Filtering sales with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering sales with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering sales with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }


        $data = $data->unique('id');

        return $data;
    }

    private function getSale($uid) {
        $data = Sale::where('uid', $uid)->with('user','saleitems','store')->where('status', 1)->first();
        return $data;
    }

    private function getSaleById($id) {
        $data = Sale::where('id', $id)->with('user','saleitems','store')->where('status', 1)->first();
        return $data;
    }

    private function createSale($params) {

        $params = $this->checkUndefinedProperty($params , $this->saleAllCols());

        $data = new Sale();
        $data->uid = Carbon::now()->timestamp . Sale::count();
        $data->email = $params->email;
        $data->contact = $params->contact;
        $params = $this->checkUndefinedProperty($params , $this->saleAllCols());
        if(!$this->saveModel($data)){
            return null;
        }

        $totalqty = 0;
        $totalprice = 0;
        $totaldisc = 0;
        $totalcost = 0;
        foreach($params->saleitems as $saleitem){
            $saleitem->sale_id = $data->refresh()->id;
            $saleitem = $this->createSaleItem($saleitem);
            if($this->isEmpty($saleitem)){
                return null;
            }
            $totalqty += $this->toInt($saleitem->qty);
            $totalprice += $this->toDouble($saleitem->totalprice);
            $totaldisc += $this->toDouble($saleitem->disc);
            $totalcost += $this->toDouble($saleitem->totalcost);
        }

        // $data->sono = $params->sono;
        $data->qty = $this->toInt($totalqty);
        $data->totalcost = $this->toDouble($totalcost);
        $data->totalprice = $this->toDouble($totalprice);
        
        $voucher = $this->getVoucherById($params->voucher_id);
        if($this->isEmpty($voucher)){
            $data->disc = $this->toDouble($totaldisc);
        }else{
            
            if($voucher->discbyprice){
                $data->disc = $this->toDouble($totaldisc + $voucher->disc);
            }else{
                $data->disc = $this->toDouble($totaldisc + (($voucher->discpctg /100) *  $data->totalprice));
            }
        }

        $data->charge =  $this->getChargedPrice(($totalprice - $data->disc));
        $data->net = $this->toDouble($data->totalprice - $data->disc - $data->charge - $data->totalcost);
        $data->grandtotal = $this->toDouble($data->totalprice - $data->totaldisc);
        $data->remark = $params->remark;

        if(!$this->isEmpty($params->user_id)){
            $data->pos = false;
            $user = $this->getUserById($params->user_id);
            if($this->isEmpty($user)){
                return null;
            }
            $data->user()->associate($user);
        }else{
            $data->pos = true;
        }

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);


        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Sale is not empty when calling this function
    private function updateSale($data,  $params) {

        
        // $data->sono = $params->sono;
        $data->totalqty = $this->toInt($params->totalqty);
        $data->totalcost = $this->toDouble($params->totalcost);
        $data->totalprice = $this->toDouble($params->totalprice);
        $data->totaldisc = $this->toDouble($params->totaldisc);
        $data->discpctg = $this->toInt($this->toDouble($data->totaldisc / $data->totalprice) * 100 );
        // $data->charge = $this->toDouble($params->price);
        $data->grandtotal = $this->toDouble($data->totalprice - $data->totaldisc);
        $data->payment = $this->toDouble($params->payment);
        $data->outstanding = $this->toDouble($params->outstanding);
        $data->docdate = $this->toDate($params->docdate);
        $data->remark = $params->remark;

        if(!$this->isEmpty($params->user_id)){
            $data->pos = false;
            $user = $this->getUserById($params->user_id);
            if($this->isEmpty($user)){
                return null;
            }
            $data->user()->associate($user);
        }else{
            $data->pos = true;
        }

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);

        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    private function deleteSale($data) {
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function saleAllCols() {

        return ['id','uid', 'user_id' ,'store_id','voucher_id', 'sono', 'email', 'contact' , 'qty' , 
        'totalcost' , 'totalprice' , 'charge' , 'disc' , 'net' , 'grandtotal' , 
        'remark', 'pos', 'status' ];

    }

}
