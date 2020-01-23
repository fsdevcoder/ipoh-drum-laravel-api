<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\SecondComment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait SecondCommentServices {

    use AllServices;

    private function getSecondComments($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $comments = $this->getComments($requester);
        foreach($comments as $comment){
            $data = $data->merge($comment->secondcomments()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterSecondComments($data , $params) {


        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params , $this->secondcommentFilterCols());

        $data = $data->unique('id');

        return $data;
    }

    private function getSecondComment($uid) {

        $data = SecondComment::where('uid', $uid)->where('status', true)->first();
        return $data;

    }
    
    private function getSecondCommentById($id) {

        $data = SecondComment::where('id', $id)->where('status', true)->first();
        return $data;

    }

    //Make Sure SecondComment is not empty when calling this function
    private function createSecondComment($params) {

        // $params = $this->checkUndefinedProperty($params , $this->secondcommentAllCols());
        // $data = new SecondComment();
        
        // $data->uid = Carbon::now()->timestamp . SecondComment::count();
        // $data->name = $params->name;
        // $data->desc = $params->desc;
        // $data->price = $this->toDouble($params->price);
        // $data->maxweight = $this->toDouble($params->maxweight);
        // $data->maxdimension = $this->toDouble($params->maxdimension);

        // error_log($params->store_id);
        // if($params->store_id){
        //     $store = $this->getStoreById($params->store_id);
        //     if($this->isEmpty($store)){
        //         return null;
        //     }
        //     $data->store()->associate($store);

        // }
        
        // $data->status = true;

        // if(!$this->saveModel($data)){
        //     return null;
        // }
        
      
        // return $data->refresh();
    }

    //Make Sure SecondComment is not empty when calling this function
    private function updateSecondComment($data,  $params) {

        // $params = $this->checkUndefinedProperty($params , $this->secondcommentAllCols());

        // $data->name = $params->name;
        // $data->desc = $params->desc;
        // $data->price = $this->toDouble($params->price);
        // $data->maxweight = $this->toDouble($params->maxweight);
        // $data->maxdimension = $this->toDouble($params->maxdimension);

        // if($params->store_id){
        //     $store = $this->getStoreById($params->store_id);
        //     if($this->isEmpty($store)){
        //         return null;
        //     }
        //     $data->store()->associate($store);
        // }
        
        // $data->status = true;

        // if(!$this->saveModel($data)){
        //     return null;
        // }
        
      
        // return $data->refresh();

    }

    private function deleteSecondComment($data) {
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
    }

    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function secondcommentAllCols() {

        return ['id','uid', 'store_id', 'name' ,'desc', 'price', 'maxweight' , 'maxdimension' , 'status'];

    }
    
    public function secondcommentFilterCols() {

        return ['store_id'];

    }
    
    


}
