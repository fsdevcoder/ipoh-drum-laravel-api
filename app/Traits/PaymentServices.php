<?php

namespace App\Traits;
use App\Payment;
use App\Company;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Stripe;
use App\Traits\AllServices;

trait PaymentServices {

    use AllServices;

    private function getPayments($requester) {
        $data = collect();

        //Role Based Retrieved Done in Company Services
        $sales = $this->getSales($requester);
        foreach($sales as $sale){
            $data = $data->merge($sale->payments()->where('status',true)->get());
        }

        $data = $data->merge($requester->payments()->where('status',true)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterPayments($data , $params) {

        error_log('Filtering payments....');

        if($params->keyword){
            error_log('Filtering payments with keyword....');
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
            error_log('Filtering payments with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering payments with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering payments with status....');
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

    private function getPayment($uid) {
        $data = Payment::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getPaymentById($id) {
        $data = Payment::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createPayment($params) {

        $params = $this->checkUndefinedProperty($params , $this->paymentAllCols());
        
        $data = new Payment();

        $data->uid = Carbon::now()->timestamp . Payment::count();
        $data->desc = $params->desc;
        $data->type = $params->type;
        $data->method = $params->method;
        $data->reference = $params->reference;
        $data->email = $params->email;
        $data->contact = $params->contact;
        $data->remark = $params->remark;
        $data->saletype = $params->saletype;
        $data->amount = $this->toDouble($params->amount);
        $data->charge = $this->toDouble($this->getChargedPrice($data->amount));
        $data->net = $this->toDouble($data->amount - $data->charge);

        if($data->saletype == 'sale'){
            
            $sale = $this->getSaleById($params->sale_id);
            if($this->isEmpty($sale)){
                return null;
            }
            $data->sale()->associate($sale);
        }

        if($data->saletype == 'channelsale'){
            
            $sale = $this->getChannelSaleById($params->channel_sale_id);
            if($this->isEmpty($sale)){
                return null;
            }
            $data->channelsale()->associate($sale);
        }

        $user = $this->getUserById($params->user_id);
        if($this->isEmpty($user)){
            return null;
        }
        $data->user()->associate($user);
        
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
   
    }

    //Make Sure Payment is not empty when calling this function
    private function updatePayment($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->paymentAllCols());

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

    private function deletePayment($data) {

        $reviews = $data->reviews;
        foreach($reviews as $review){
            if(!$this->deletePaymentReview($review)){
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

        $vouchers = $data->vouchers;
        foreach($vouchers as $voucher){
            if(!$this->deleteVoucher($voucher)){
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

    public function paymentAllCols() {

        return ['id','sale_id', 'user_id', 'uid' ,'desc', 'type', 'method' , 
        'reference' , 'email', 'saletype'  , 'contact', 'amount' , 'charge' , 'net',
        'remark' , 'status' , 'card_id' ];

    }

    // Charge Data
    // -----------------------------------------------------------------------------------------------------------------------------------------

    
    public function getStripeServiceTax() {

        return 0.06;
    }

    public function getStripeChargePercentage() {

        return 0.034;
    }

    public function getStripeChargePrice() {

        return 2;
    }

    public function getAppChargePrice() {

        return 0;
    }

    public function getAppChargePercentage() {

        return 0;
    }

    public function getChargedPrice($price) {

        $charge = $this->toDouble(($price * $this->getStripeChargePercentage()) + ($price * $this->getAppChargePercentage()) + $this->getAppChargePrice() + $this->getStripeChargePrice());
        $servicetax = $this->toDouble($charge * $this->getStripeServiceTax());

        return $charge + $servicetax;
    }

    // Stripe Services
    // -----------------------------------------------------------------------------------------------------------------------------------------

    public function createStripeCustomer($params) {

        try{
            $customer = Stripe::customers()->create([
                'email' => $params->email,
            ]);
            return (object) $customer;
        }catch(Exception $e){
            return null;
        }
    }

    public function createStripeCard($params) {

        try{
            $card = Stripe::cards()->create($params->customer_id, $params->token);
            return (object) $card;
        }catch(Exception $e){
            return null;
        }
    }
    
    public function createStripeToken($params) {
        try{
            $token = Stripe::tokens()->create([
                'customer' =>$params->customer_id,
                'source' =>$params->card_id,
            ]);
            return (object) $token;
        }catch(Exception $e){
            return null;
        }
    }
    
    public function createStripeCharge($params) {

        try{
            $charge = Stripe::charges()->create([
                'amount' => $params->amount,
                'currency' => $params->currency,
                'customer' => $params->customer,
                'source' => $params->source,
                'description' => $params->description,
                'receipt_email' => $params->receipt_email,
            ]);
            return  (object) $charge;
        }catch(Exception $e){
            return null;
        }
    }
}
