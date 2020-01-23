<?php

namespace App\Traits;
use App\Store;
use App\Company;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait StoreServices {

    use AllServices;

    private function getStores($requester) {
        $data = collect();

        //Role Based Retrieved Done in Company Services
        $companies = $this->getCompanies($requester);
        foreach($companies as $company){
            $data = $data->merge($company->stores()->with('company')->with('user')->where('status',true)->get());
        }

        $data = $data->merge($requester->stores()->with('company')->with('user')->where('status',true)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterStores($data , $params) {

        error_log('Filtering stores....');

        if($params->keyword){
            error_log('Filtering stores with keyword....');
            $keyword = $params->keyword;
            $data = $data->filter(function($item)use($keyword){
                //check string exist inside or not
                if(stristr($item->name, $keyword) == TRUE || stristr($item->regno, $keyword) == TRUE || stristr($item->uid, $keyword) == TRUE ) {
                    return true;
                }else{
                    return false;
                }

            });
        }


        if($params->fromdate){
            error_log('Filtering stores with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering stores with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering stores with status....');
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

    private function getStore($uid) {
        $data = Store::where('uid', $uid)->with('company')->with('user')->where('status', 1)->first();
        return $data;
    }

    private function getStoreById($id) {
        $data = Store::where('id', $id)->with('company')->with('user')->where('status', 1)->first();
        return $data;
    }

    private function createStore($params) {

        $params = $this->checkUndefinedProperty($params , $this->storeAllCols());

        $data = new Store();
        $data->uid = Carbon::now()->timestamp . Store::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->contact = $params->contact;
        $data->email = $params->email;
        $data->address = $params->address;
        $data->postcode = $params->postcode;
        $data->rating = 0;
        $data->city = $params->city;
        $data->state = $params->state;
        $data->country = $params->country;
        $data->companyBelongings = $params->companyBelongings;
        $data->status = true;

       //Assign Owner
       if($data->companyBelongings){
            $company = $this->getCompanyById($params->company_id);
            error_log($params->company_id);
            error_log($company);
            if($this->isEmpty($company)){
                return null;
            }
            $data->company()->associate($company);
            $data->user_id = null;
        }else{
            $user = $this->getUserById($params->user_id);
            error_log($user);
            if($this->isEmpty($user)){
                return null;
            }
            $data->user()->associate($user);
            $data->company_id = null;
        }
        
        if(!$this->saveModel($data)){
            return null;
        }
            

        return $data->refresh();
    }

    //Make Sure Store is not empty when calling this function
    private function updateStore($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->storeAllCols());

        $data->name = $params->name;
        $data->contact = $params->contact;
        $data->desc = $params->desc;
        $data->email = $params->email;
        $data->address = $params->address;
        $data->postcode = $params->postcode;
        $data->city = $params->city;
        $data->state = $params->state;
        $data->country = $params->country;
        $data->companyBelongings = $params->companyBelongings;

        //Assign Owner
        if($data->companyBelongings){
            $company = $this->getCompanyById($params->company_id);
            if($this->isEmpty($company)){
                return null;
            }
            $data->company()->associate($company);
            $data->user_id = null;
        }else{
            $user = $this->getUserById($params->user_id);
            if($this->isEmpty($user)){
                return null;
            }
            $data->user()->associate($user);
            $data->company_id = null;
        }
        
        if(!$this->saveModel($data)){
            return null;
        }

        return $data->refresh();
    }

    private function deleteStore($data) {

        $vouchers = $data->vouchers;
        foreach($vouchers as $voucher){
            if(!$this->deleteVoucher($voucher)){
                return null;
            }
        }
        
        $reviews = $data->reviews;
        foreach($reviews as $review){
            if(!$this->deleteStoreReview($review)){
                return null;
            }
        }
        
        $inventories = $data->inventories;
        foreach($inventories as $inventory){
            if(!$this->deleteInventory($inventory)){
                return null;
            }
        }

        $tickets = $data->tickets;
        foreach($tickets as $ticket){
            if(!$this->deleteTicket($ticket)){
                return null;
            }
        }

        $promotions = $data->promotions;
        foreach($promotions as $promotion){
            if(!$this->deleteProductPromotion($promotion)){
                return null;
            }
        }

        $warranties = $data->warranties;
        foreach($warranties as $warranty){
            if(!$this->deleteWarranty($warranty)){
                return null;
            }
        }

        $shippings = $data->shippings;
        foreach($shippings as $shipping){
            if(!$this->deleteShipping($shipping)){
                return null;
            }
        }


        $data->status = false;
        if(!$this->saveModel($data)){
            return null;
        }

        return $data->refresh();
    }


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------

    public function storeAllCols() {

        return ['id','company_id', 'user_id', 'uid' ,'name', 'contact', 'desc' , 
        'imgpath' , 'imgpublicid'  , 'email', 'rating' , 'freeshippingminpurchase' , 
        'address' , 'state' , 'postcode' , 'city','country','status','companyBelongings'];

    }

}
