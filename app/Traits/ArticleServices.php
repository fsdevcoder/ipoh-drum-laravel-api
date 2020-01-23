<?php

namespace App\Traits;
use App\User;
use App\Store;
use App\ProductPromotion;
use App\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait ArticleServices {

    use AllServices;

    private function getArticles($requester) {

        $data = collect();
        //Role Based Retrieve Done in Store
        $bloggers = $this->getBloggers($requester);
        foreach($bloggers as $blogger){
            $data = $data->merge($blogger->articles()->where('status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterArticles($data , $params) {

        $data = $this->globalFilter($data, $params);
        $params = $this->checkUndefinedProperty($params , $this->articleFilterCols());

        if($params->scope){
            error_log('Filtering articles with scope....');
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

    private function getArticle($uid) {
        $data = Article::where('uid', $uid)->with('blogger', 'articleimages')->where('status', 1)->first();
        return $data;
    }

    private function getArticleById($id) {
        $data = Article::where('id', $id)->with('blogger', 'articleimages')->where('status', 1)->first();
        return $data;
    }

    private function createArticle($params) {

        $params = $this->checkUndefinedProperty($params , $this->articleAllCols());

        $data = new Article();
        $data->uid = Carbon::now()->timestamp . Article::count();
        $data->title = $params->title;
        $data->desc = $params->desc;
        $data->view = 0;
        $data->like = 0;
        $data->dislike = 0;
        if($params->scope == 'private'){
            $data->scope = $params->scope;
        }else{
            $data->scope = 'public';
        }
        $data->agerestrict = false;

        $blogger = $this->getBloggerById($params->blogger_id);
        if($this->isEmpty($blogger)){
            return null;
        }
        $data->blogger()->associate($blogger);

        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Article is not empty when calling this function
    private function updateArticle($data,  $params) {
        
        $params = $this->checkUndefinedProperty($params , $this->articleAllCols());
        $data->title = $params->title;
        $data->desc = $params->desc;
        if($params->scope == 'private'){
            $data->scope = $params->scope;
        }else{
            $data->scope = 'public';
        }
        $data->agerestrict = false;

        $blogger = $this->getBloggerById($params->blogger_id);
        if($this->isEmpty($blogger)){
            return null;
        }
        $data->blogger()->associate($blogger);

        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    private function deleteArticle($data) {
        $articleimages = $data->articleimages;
        foreach($articleimages as $articleimage){
            if(!$this->deleteArticleImage($articleimage)){
                return null;
            }
        }
        
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

    private function getAllPublicArticles() {
        
        $data = Article::where('status', true)->where('scope', 'public')->with('articleimages','blogger')->get();

        return $data;
    }

    
    private function setCommentCount($data) {
        
        $data->commentcount = $data->comments()->count();

        return $data;
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function articleAllCols() {

        return ['id','blogger_id', 'title', 'desc', 
        'view' , 'like' , 'dislike'  , 'scope', 'agerestrict' , 'status'];

    }

    public function articleDefaultCols() {

        return ['id','uid' ,'onsale', 'onpromo', 'name' , 'desc' , 'price' , 'disc' , 
        'discpctg' , 'promoprice' , 'promostartdate' , 'promoenddate', 'enddate' , 
        'stock', 'salesqty' ];

    }
    public function articleFilterCols() {

        return ['scope'];

    }

}
