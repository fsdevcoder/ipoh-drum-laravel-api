<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ProductPromotion;
use App\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait VideoServices {

    use AllServices;

    private function getVideos($requester) {

        $data = collect();
        //Role Based Retrieve Done in Store
        $channels = $this->getChannels($requester);
        foreach($channels as $channel){
            $data = $data->merge($channel->videos()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterVideos($data , $params) {
        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params , $this->videoFilterCols());

        if($params->scope){
            error_log('Filtering videos with scope....');
            $scope = $params->scope;
            if($scope == 'private'){
                $data = $data->filter(function ($item){
                    return $item->scope == 'private';
                });
            }else{
                $data = $data->filter(function ($item){
                    return $item->scope == 'public';
                });
            }
        }

        $data = $data->unique('id');

        return $data;
    }

    private function getVideo($uid) {
        $data = Video::where('uid', $uid)->with('channel', 'comments.secondcomments')->where('status', 1)->first();
        return $data;
    }

    private function getVideoById($id) {
        $data = Video::where('id', $id)->with('channel', 'comments.secondcomments')->where('status', 1)->first();
        return $data;
    }

    private function createVideo($params) {

        $params = $this->checkUndefinedProperty($params , $this->videoAllCols());

        $data = new Video();
        $data->uid = Carbon::now()->timestamp . Video::count();
        $data->title  = $params->title ;
        $data->desc = $params->desc;
        $data->videopath = $params->videopath;
        $data->videopublicid = $params->videopublicid;
        $data->totallength = $params->totallength;
        $data->agerestrict = false;
        $data->like = 0;
        $data->dislike = 0;
        $data->view = 0;
        
        if($params->scope == 'private'){
            $data->scope = $params->scope;
        }else{
            $data->scope = 'public';
        }
        
        if($this->isEmpty( $params->free)){
            return null;
        }else{
            $data->free = $params->free;
            if($data->free){
                $data->price = 0;
                $data->disc = 0;
                $data->discpctg = 0;
            }else{
                $data->price = $this->toDouble($params->price);
                if($this->isEmpty( $params->discbyprice)){
                    return null;
                }else{
                    if($data->discbyprice){
                        $data->disc = $this->toDouble($params->disc);
                        $data->discpctg = $this->toInt($this->toDouble($data->disc / $data->price) * 100 );
                    }else{
                        $data->discpctg = $this->toInt($params->discpctg);
                        $data->disc = $this->toDouble($data->price * ($data->discpctg / 100));
                    }
                }
            }
        }

        $channel = $this->getChannelById($params->channel_id);
        if($this->isEmpty($channel)){
            error_log('here');
            return null;
        }
        $data->channel()->associate($channel);

        return $data->refresh();
    }

    //Make Sure Video is not empty when calling this function
    private function updateVideo($data,  $params) {
        
        $params = $this->checkUndefinedProperty($params , $this->videoAllCols());

        $data->title  = $params->title ;
        $data->desc = $params->desc;
        $data->videopath = $params->videopath;
        $data->videopublicid = $params->videopublicid;
        $data->totallength = $params->totallength;
        $data->agerestrict = false;
        
        if($params->scope == 'private'){
            $data->scope = $params->scope;
        }else{
            $data->scope = 'public';
        }
        
        if($this->isEmpty($params->free)){
            return null;
        }else{
            $data->free = $params->free;
            if($data->free){
                $data->price = 0;
                $data->disc = 0;
                $data->discpctg = 0;
            }else{
                $data->price = $this->toDouble($params->price);
                if($this->isEmpty( $params->discbyprice)){
                    return null;
                }else{
                    if($data->discbyprice){
                        $data->disc = $this->toDouble($params->disc);
                        $data->discpctg = $this->toInt($this->toDouble($data->disc / $data->price) * 100 );
                    }else{
                        $data->discpctg = $this->toInt($params->discpctg);
                        $data->disc = $this->toDouble($data->price * ($data->discpctg / 100));
                    }
                }
            }
        }

        $channel = $this->getChannelById($params->channel_id);
        if($this->isEmpty($channel)){
            error_log('here');
            return null;
        }
        $data->channel()->associate($channel);

        return $data->refresh();
    }

    private function deleteVideo($data) {
        
        $comments = $data->comments;
        foreach($comments as $comment){
            if(!$this->deleteComment($comment)){
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

    public function calculateVideoPromotionPrice($data) {
        if(!$data->free){
            if($data->discbyprice &&  $data->disc > 0){
                $data->promoprice =  $this->toDouble($data->price - $data->disc);
                $data->promopctg =  $this->toInt($this->toDouble($data->promoprice / $data->price ) * 100);
            }else if( $data->discpctg > 0){
                $data->promopctg =  $this->toInt($data->discpctg);
                $data->promoprice =  $this->toDouble($data->price - ($data->price * ($data->promopctg / 100)));
            }else{
                $data->promoprice = $data->price;
                $data->promopctg = 0;
            }
        }

        return $data;
    }
    private function getAllPublicVideos() {
        
        $data = Video::where('status', true)->where('scope','public')->get();

        return $data;
    }

    private function likeVideo($video) {
        
        $video->like += 1;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        

        return true;
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function videoAllCols() {

        return ['id','channel_id', 'playlist_id', 'uid', 
        'title' , 'desc' , 'videopath', 'videopublicid'  , 'imgpublicid', 'imgpath' , 'totallength' , 'view' , 
        'like' , 'dislike','price','discpctg','disc','discbyprice','free','salesqty','scope',
        'agerestrict','status'];

    }

    public function videoDefaultCols() {

        return ['id','channel_id', 'playlist_id', 'uid', 
        'title' , 'desc' , 'videopath', 'videopublicid'  , 'imgpublicid', 'imgpath' , 'totallength' , 'view' , 
        'like' , 'dislike','price','discpctg','disc','discbyprice','free','salesqty','scope',
        'agerestrict','status'];

    }
    public function videoFilterCols() {

        return ['scope'];

    }

    
    private function validateUserPurchasedVideo($user , $video) {
        
        if($video->free){
            return false;
        }
        
        $purchasedvideos = $user->purchasevideos()->wherePivot('status' , true)->get();

        $ids = $purchasedvideos->pluck('id');

        if($ids->search($video->id)){
            return false;
        }
        
        return true;
        
    }

}
