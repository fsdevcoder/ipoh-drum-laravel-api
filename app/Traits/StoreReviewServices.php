<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\StoreReview;
use App\Inventory;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;


trait StoreReviewServices {

    use AllServices;

    private function getStoreReviews($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $stores = $this->getStores($requester);
        foreach($stores as $store){
            $data = $data->merge($store->reviews()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterStoreReviews($data , $params) {


        if($params->keyword){
            error_log('Filtering storereviews with keyword....');
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
            error_log('Filtering storereviews with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering storereviews with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering storereviews with status....');
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

    private function getStoreReview($uid) {

        $data = StoreReview::where('uid', $uid)->where('status', true)->first();
        return $data;

    }
    
    private function getStoreReviewById($id) {

        $data = StoreReview::where('id', $id)->where('status', true)->first();
        return $data;

    }

    //Make Sure StoreReview is not empty when calling this function
    private function createStoreReview($params) {
        
        $params = $this->checkUndefinedProperty($params , $this->storeReviewAllCols());
        $data = new StoreReview();

        $data->uid = Carbon::now()->timestamp . StoreReview::count();
        $data->title = $params->title;
        $data->desc = $params->desc;
        $data->rating = $this->toDouble($params->rating);
        $data->like = 0;
        $data->dislike = 0;  

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);
        
        

        $user = $this->getUserById($params->user_id);
        if($this->isEmpty($user)){
            return null;
        }
        $data->user()->associate($user);


        
        $data->status = true;

        if($params->img){ 
            $img = $this->uploadImage($params->img , '/Store/'. $data->uid);
            if(!$this->isEmpty($img)){
                $data->imgpath = $img->imgurl;
                $data->imgpublicid = $img->publicid;
            }else{
                return null;
            }

        }
        if(!$this->saveModel($data)){
            return null;
        }
        
      
        return $data->refresh();
    }

    //Make Sure StoreReview is not empty when calling this function
    private function updateStoreReview($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->storeReviewAllCols());

        $data->title = $params->title;
        $data->desc = $params->desc;
        $data->rating = $this->toDouble($params->rating);

       
        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);

        $user = $this->getUserById($params->user_id);
        if($this->isEmpty($user)){
            return null;
        }
        $data->user()->associate($user);


        
        if($params->img){ 
            $this->deleteImage($data->imgpublicid);
            $img = $this->uploadImage($params->img , '/Store/'. $data->uid);
            if(!$this->isEmpty($img)){
                $data->imgpath = $img->imgurl;
                $data->imgpublicid = $img->publicid;
            }else{
                return null;
            }

        }else{
            error_log('empty');
        }
        
        $data->status = true;

        if(!$this->saveModel($data)){
            return null;
        }
        
      
        return $data->refresh();

    }

    private function deleteStoreReview($data) {

        //Cancel review
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
    }

    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function storeReviewAllCols() {

        return ['id','uid', 'store_id' ,'user_id', 'title', 'desc' , 'imgpath' , 'imgpublicid' , 'rating' , 'like' , 'dislike', 'status'];

    }
    
    


}
