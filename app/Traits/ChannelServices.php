<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ProductPromotion;
use App\Channel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait ChannelServices {

    use AllServices;

    private function getChannels($requester) {

        $data = collect();
        //Role Based Retrieve Done in Store
        $companies = $this->getCompanies($requester);
        foreach($companies as $company){
            $data = $data->merge($company->channels()->with('company','user')->where('status',true)->get());
        }

        $data = $data->merge($requester->channels()->with('company','user')->where('status',true)->get());


        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterChannels($data , $params) {

        error_log('Filtering channels....');

        if($params->keyword){
            error_log('Filtering channels with keyword....');
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
            error_log('Filtering channels with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering channels with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering channels with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }

        if($params->onsale){
            error_log('Filtering channels with on sale status....');
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

    private function getChannel($uid) {
        $data = Channel::where('uid', $uid)->with('company','user')->where('status', 1)->first();
        return $data;
    }

    private function getChannelById($id) {
        $data = Channel::where('id', $id)->with('company','user')->where('status', 1)->first();
        return $data;
    }

    private function createChannel($params) {

        $params = $this->checkUndefinedProperty($params , $this->channelAllCols());

        $data = new Channel();
        $data->uid = Carbon::now()->timestamp . Channel::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->email = $params->email;
        $data->tel1 = $params->tel1;

        if($this->isEmpty( $params->companyBelongings)){
            return null;
        }else{
            $data->companyBelongings = $params->companyBelongings;
        }
        
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

    //Make Sure Channel is not empty when calling this function
    private function updateChannel($data,  $params) {
        
        $params = $this->checkUndefinedProperty($params , $this->channelAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->email = $params->email;
        $data->tel1 = $params->tel1;

        if($this->isEmpty( $params->companyBelongings)){
            return null;
        }else{
            $data->companyBelongings = $params->companyBelongings;
        }
        
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

    private function deleteChannel($data) {

        $videos = $data->videos;
        foreach($videos as $video){
            if(!$this->deleteVideo($video)){
                return null;
            }
        }
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
    public function channelAllCols() {

        return ['id','company_id', 'user_id', 'uid', 
        'name' , 'desc' , 'email'  , 'imgpublicid', 'imgpath' , 'tel1' , 'companyBelongings' , 
        'status'];

    }

    public function channelDefaultCols() {

        return ['id','company_id', 'user_id', 'uid', 
        'name' , 'desc' , 'email'  , 'imgpublicid', 'imgpath' , 'tel1' , 'companyBelongings' , 
        'status'];

    }

}
