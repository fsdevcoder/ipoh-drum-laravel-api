<?php

namespace App\Traits;
use App\User;
use App\VoucherCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait VoucherCodeServices {

    use AllServices;

    private function getVoucherCodes($requester) {

        $data = collect();
        //Role Based Retrieve Done in TicketService
        $vouchers = $this->getVouchers($requester);
        foreach($vouchers as $voucher){
            $data = $data->merge($voucher->vouchercodes()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterVoucherCodes($data , $params) {

        error_log('Filtering vouchercodes....');

        if($params->keyword){
            error_log('Filtering vouchercodes with keyword....');
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
            error_log('Filtering vouchercodes with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering vouchercodes with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering vouchercodes with status....');
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

    private function getVoucherCode($uid) {
        $data = VoucherCode::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getVoucherCodeById($id) {
        $data = VoucherCode::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createVoucherCode($params) {

        
        $params = $this->checkUndefinedProperty($params , $this->voucherCodeAllCols());

        $data = new VoucherCode();
        $data->uid = Carbon::now()->timestamp . VoucherCode::count();
        
        
        $count = VoucherCode::count();
        $data->status = true;
 
        $voucher = $this->getVoucherById($params->voucher_id);
        if($this->isEmpty($voucher)){
            return null;
        }
        $data->voucher()->associate($voucher);
        
        //Unique number
        $count = $voucher->vouchercodes()->count();
        $hexval = strtoupper(\dechex($count));
        //Random String
        $randstr = strtoupper($this->generateRandomString(4));
        $data->code = substr_replace($randstr, $hexval, 1, 0);

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure VoucherCode is not empty when calling this function
    private function updateVoucherCode($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->voucherCodeAllCols());

        $data->code = $params->code;
        $data->status = true;
 
        $ticket = $this->getTicketById($params->ticket_id);
        if($this->isEmpty($ticket)){
            return null;
        }
        $data->ticket()->associate($ticket);

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    private function deleteVoucherCode($data) {
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
    public function voucherCodeAllCols() {

        return ['id','uid', 'voucher_id', 'code', 'status'];

    }

}
