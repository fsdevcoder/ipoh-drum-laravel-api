<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ArticleImage;
use App\ArticleImageFamily;
use App\ArticleImageImage;
use App\ProductPromotion;
use App\Warranty;
use App\Shipping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait ArticleImageServices {

    use AllServices;

    private function getArticleImages($requester) {

        $data = collect();

        //Role Based Retrieve Done in Store Services
        $inventories = $this->getArticleImages($requester);
        foreach($inventories as $article){
            $data = $data->merge($article->articleimages()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterArticleImages($data , $params) {


        if($params->keyword){
            error_log('Filtering articleimages with keyword....');
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
            error_log('Filtering articleimages with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering articleimages with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering articleimages with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }

        if($params->onsale){
            error_log('Filtering articleimages with on sale status....');
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

    private function getArticleImage($uid) {

        $data = ArticleImage::where('uid', $uid)->where('status', true)->first();
        return $data;

    }
    
    private function getArticleImageById($id) {

        $data = ArticleImage::where('id', $id)->where('status', true)->first();
        return $data;

    }
    
    //Make Sure ArticleImage is not empty when calling this function
    private function createArticleImage($params) {

        $params = $this->checkUndefinedProperty($params , $this->articleImageAllCols());

        $article = $this->getArticleById($params->article_id);
        if($this->isEmpty($article)){
            return null;
        }

        if($article->articleimages()->count() >= 6){
            return null;
        }

        $data = new ArticleImage();
        $data->uid = Carbon::now()->timestamp . ArticleImage::count();
        $data->title = $params->title;
        $data->desc = $params->desc;
        $data->imgpath = $params->imgpath;
        $data->imgpublicid = $params->imgpublicid;

        $data->article()->associate($article);

        if(!$this->saveModel($data)){
            return null;
        }
        
        return $data->refresh();
    }

    //Make Sure ArticleImage is not empty when calling this function
    private function updateArticleImage($data,  $params) {


    }

    public function deleteArticleImage($data)
    {
        error_log($data->imgpublicid);
        if($this->deleteImage($data->imgpublicid)){
            $data->delete();
            return true;
        }else{
            return false;
        }
    }


  
    //Relationship Deassociating
    //===============================================================================================================================================================================
    
    

    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    
    
    public function articleImageDefaultCols() {

        return ['id','uid', 'article_id', 'title' ,'desc', 'imgpublicid', 'imgpath' , 'status'];

    }

    public function articleImageAllCols() {

        return ['id','uid', 'article_id', 'title' ,'desc', 'imgpublicid', 'imgpath', 'like', 'dislike', 'coverimage' , 'status'];

    }
    

    


}
