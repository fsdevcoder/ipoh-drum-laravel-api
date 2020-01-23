<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ChannelSale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait ChannelSaleServices {

    use AllServices;

    private function getChannelSales($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $stores = $this->getStores($requester);
        foreach($stores as $store){
            $data = $data->merge($store->channelsales()->where('status',true)->get());
        }


        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterChannelSales($data , $params) {

        error_log('Filtering channelsales....');

        if($params->keyword){
            error_log('Filtering channelsales with keyword....');
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
            error_log('Filtering channelsales with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering channelsales with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering channelsales with status....');
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

    private function getChannelSale($uid) {
        $data = ChannelSale::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getChannelSaleById($id) {
        $data = ChannelSale::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createChannelSale($params) {

        $params = $this->checkUndefinedProperty($params , $this->channelsaleAllCols());

        $data = new ChannelSale();
        $data->uid = Carbon::now()->timestamp . ChannelSale::count();
        
        $video = $this->getVideoById($params->video_id);
        if($this->isEmpty($video)){
            return null;
        }
        $data->video()->associate($video);
        
        $channel = $video->channel;
        if($this->isEmpty($channel)){
            return null;
        }
        $data->channel()->associate($channel);
        
        $user = $this->getUserById($params->user_id);
        if($this->isEmpty($user)){
            return null;
        }
        $data->user()->associate($user);

        $video = $this->calculateVideoPromotionPrice($video);
        error_log($video->promoprice);
        $data->disc = $this->toDouble($video->price - $video->promoprice);
        $data->totalprice = $this->toDouble($video->price);
        $data->charge =  $this->getChargedPrice(($data->totalprice - $data->disc));
        $data->net = $this->toDouble($data->totalprice - $data->disc - $data->charge);
        $data->grandtotal = $this->toDouble($data->totalprice - $data->disc);
        $data->remark = $params->remark;

        $data->status = true;
        $video->purchaseusers()->syncWithoutDetaching([$user->id]);
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure ChannelSale is not empty when calling this function
    private function updateChannelSale($data,  $params) {

        
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

    private function deleteChannelSale($data) {
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
    public function channelsaleAllCols() {

        return ['id','uid', 'user_id' ,'channel_id','video_id'  ,
         'totalprice' , 'charge' , 'disc' , 'net' , 'grandtotal' , 
        'remark', 'status' ];

    }

}
