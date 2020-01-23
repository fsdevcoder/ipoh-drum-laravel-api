<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ProductReview;
use App\Inventory;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait ProductReviewServices {

    use AllServices;

    private function getProductReviews($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $inventories = $this->getInventories($requester);
        foreach($inventories as $inventory){
            $data = $data->merge($inventory->productreviews()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterProductReviews($data , $params) {


        if($params->keyword){
            error_log('Filtering productreviews with keyword....');
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
            error_log('Filtering productreviews with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering productreviews with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering productreviews with status....');
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

    private function getProductReview($uid) {

        $data = ProductReview::where('uid', $uid)->where('status', true)->first();
        return $data;

    }

    private function getProductReviewById($id) {

        $data = ProductReview::where('id', $id)->where('status', true)->first();
        return $data;

    }
    //Make Sure ProductReview is not empty when calling this function
    private function createProductReview($params) {
        
        $params = $this->checkUndefinedProperty($params , $this->productReviewAllCols());
        $data = new ProductReview();

        $data->uid = Carbon::now()->timestamp . ProductReview::count();
        $data->title = $params->title;
        $data->desc = $params->desc;
        $data->type = $params->type;
        $data->rating = $this->toDouble($params->rating);
        $data->like = 0;
        $data->dislike = 0;  

        if($data->type == 'inventory'){
            $inventory = $this->getInventoryById($params->inventory_id);
            if($this->isEmpty($inventory)){
                return null;
            }
            $data->inventory()->associate($inventory);
            $folderpath =   "/Inventory/". $inventory->uid. "/Review/";
        }else if($data->type == 'ticket'){
            $ticket = $this->getTicketById($params->ticket_id);
            if($this->isEmpty($ticket)){
                return null;
            }
            $data->ticket()->associate($ticket);
            $folderpath =   "/Ticket/". $ticket->uid. "/Review/";
        }else{
            return null;
        }
        
        $user = $this->getUserById($params->user_id);
        if($this->isEmpty($user)){
            return null;
        }
        $data->user()->associate($user);


        
        $data->status = true;

        if($params->img){ 
            $img = $this->uploadImage($params->img , $folderpath);
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

    //Make Sure ProductReview is not empty when calling this function
    private function updateProductReview($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->productReviewAllCols());

        $data->title = $params->title;
        $data->desc = $params->desc;
        $data->type = $params->type;
        $data->rating = $this->toDouble($params->rating);

        if($data->type == 'inventory'){
            $inventory = $this->getInventoryById($params->inventory_id);
            if($this->isEmpty($inventory)){
                return null;
            }
            $data->inventory()->associate($inventory);
            $folderpath =   "/Inventory/". $inventory->uid. "/Review/";
        }else if($data->type == 'ticket'){
            $ticket = $this->getTicketById($params->ticket_id);
            if($this->isEmpty($ticket)){
                return null;
            }
            $data->ticket()->associate($ticket);
            $folderpath =   "/Ticket/". $ticket->uid. "/Review/";
        }else{
            return null;
        }
        
        $user = $this->getUserById($params->user_id);
        if($this->isEmpty($user)){
            return null;
        }
        $data->user()->associate($user);
        
        if($params->img){ 
            $this->deleteImage($data->imgpublicid);
            $img = $this->uploadImage($params->img , $folderpath);
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

    private function deleteProductReview($data) {

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
    public function productReviewAllCols() {

        return ['id','uid', 'inventory_id', 'ticket_id' ,'user_id', 
        'title', 'desc' , 'imgpath' , 'imgpublicid' , 'type', 'rating' , 'like' , 'dislike', 'status'];

    }
    
    


}
