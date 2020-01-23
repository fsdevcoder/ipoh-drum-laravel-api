<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ProductPromotion;
use App\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait CommentServices {

    use AllServices;

    private function getComments($requester) {

        $data = collect();
        //Role Based Retrieve Done in Store
        $articles = $this->getArticles($requester);
        foreach($articles as $article){
            $data = $data->merge($article->comments()->where('status',true)->get());
        }

        $data = $data->merge($requester->comments()->where('status',true)->get());


        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterComments($data , $params) {

        error_log('Filtering comments....');

        if($params->keyword){
            error_log('Filtering comments with keyword....');
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
            error_log('Filtering comments with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering comments with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering comments with status....');
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

    private function getComment($uid) {
        $data = Comment::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getCommentById($id) {
        $data = Comment::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createComment($params) {

        $params = $this->checkUndefinedProperty($params , $this->commentAllCols());

        $data = new Comment();
        $data->uid = Carbon::now()->timestamp . Comment::count();
        $data->name = $params->name;
        $data->code = $params->code;
        $data->sku = $params->sku;
        $data->desc = $params->desc;
        $data->price = $this->toDouble($params->price);
        $data->enddate = $this->toDate($params->enddate);
        $data->qty = $this->toInt($params->qty);
        $data->salesqty = 0;
        $data->stockthreshold = $this->toInt($params->stockthreshold);
        $data->onsale = $params->onsale;

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);
           
        $promotion = $this->getProductPromotionById($params->product_promotion_id);
        if($this->isEmpty($promotion)){
            return null;
        }else{
            if($promotion->qty > 0){
                $data->promoendqty = $data->salesqty + $promotion->qty;
            }
        }

        $data->promotion()->associate($promotion);

        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Comment is not empty when calling this function
    private function updateComment($data,  $params) {
        
        $params = $this->checkUndefinedProperty($params , $this->commentAllCols());

        $data->name = $params->name;
        $data->code = $params->code;
        $data->sku = $params->sku;
        $data->desc = $params->desc;
        $data->price = $this->toDouble($params->price);
        $data->enddate = $this->toDate($params->enddate);
        $data->qty = $this->toInt($params->qty);
        $data->salesqty = 0;
        $data->stockthreshold = $this->toInt($params->stockthreshold);
        $data->onsale = $params->onsale;

        $store = $this->getStoreById($params->store_id);
        if($this->isEmpty($store)){
            return null;
        }
        $data->store()->associate($store);
           
        $promotion = $this->getProductPromotionById($params->product_promotion_id);
        if($this->isEmpty($promotion)){
            return null;
        }else{
            if($promotion->qty > 0){
                $data->promoendqty = $data->salesqty + $promotion->qty;
            }
        }

        $data->promotion()->associate($promotion);

        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    private function deleteComment($data) {
        
        $secondcomments = $data->secondcomments;
        foreach($secondcomments as $secondcomment){
            if(!$this->deleteSecondComment($secondcomment)){
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


    private function getCommentsByArticle($article) {

        $data = collect();
        $data = $data->merge($article->comments()->with('secondcomments')->where('status',true)->get());
        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }
    
    private function getCommentsByVideo($video) {

        $data = collect();
        $data = $data->merge($video->comments()->with('secondcomments')->where('status',true)->get());
        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }

    private function getCommentsByVideos($requester) {

        $data = collect();
        //Role Based Retrieve Done in Store
        $videos = $this->getVideos($requester);
        foreach($videos as $video){
            $data = $data->merge($video->comments()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;
    }

    
    
    // private function getArticleImageComments($requester) {

    //     $data = collect();
    //     //Role Based Retrieve Done in Store
    //     $articleimages = $this->getArticleImages($requester);
    //     foreach($articleimages as $articleimage){
    //         $data = $data->merge($articleimage->comments()->where('status',true)->get());
    //     }

    //     $data = $data->unique('id')->sortBy('id')->flatten(1);

    //     return $data;
    // }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function commentAllCols() {

        return ['id','store_id', 'product_promotion_id', 'uid', 
        'code' , 'sku' , 'name'  , 'imgpublicid', 'imgpath' , 'desc' , 'rating' , 
        'price' , 'qty','promoendqty','salesqty','stockthreshold','status','onsale'];

    }

    public function commentDefaultCols() {

        return ['id','uid' ,'onsale', 'onpromo', 'name' , 'desc' , 'price' , 'disc' , 
        'discpctg' , 'promoprice' , 'promostartdate' , 'promoenddate', 'enddate' , 
        'stock', 'salesqty' ];

    }

}
