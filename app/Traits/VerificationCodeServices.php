<?php

namespace App\Traits;
use App\User;
use App\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait VerificationCodeServices {

    use AllServices;

    private function getVerificationCodes($requester) {

        $data = collect();
        //Role Based Retrieve Done in TicketService
        $tickets = $this->getTickets($requester);
        foreach($tickets as $ticket){
            $data = $data->merge($ticket->verificationcodes()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterVerificationCodes($data , $params) {

        error_log('Filtering verificationcodes....');

        if($params->keyword){
            error_log('Filtering verificationcodes with keyword....');
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
            error_log('Filtering verificationcodes with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering verificationcodes with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering verificationcodes with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }

        if($params->onsale){
            error_log('Filtering verificationcodes with on sale status....');
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

    private function getVerificationCode($uid) {
        $data = VerificationCode::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getVerificationCodeById($id) {
        $data = VerificationCode::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createVerificationCode($params) {

        $params = $this->checkUndefinedProperty($params , $this->verificationCodeAllCols());

        $data = new VerificationCode();
        $data->uid = Carbon::now()->timestamp . VerificationCode::count();
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

    //Make Sure VerificationCode is not empty when calling this function
    private function updateVerificationCode($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->verificationCodeAllCols());

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

    private function deleteVerificationCode($data) {
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
    public function verificationCodeAllCols() {

        return ['id','uid', 'ticket_id', 'code', 'status'];

    }

}
