<?php

namespace App\Traits;
use App\Blogger;
use App\Company;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait BloggerServices {

    use AllServices;

    private function getBloggers($requester) {
        $data = collect();

        //Role Based Retrieved Done in Company Services
        $companies = $this->getCompanies($requester);
        foreach($companies as $company){
            $data = $data->merge($company->bloggers()->with('company')->with('user')->where('status',true)->get());
        }

        $data = $data->merge($requester->bloggers()->with('company')->with('user')->where('status',true)->get());

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterBloggers($data , $params) {

        error_log('Filtering bloggers....');

        if($params->keyword){
            error_log('Filtering bloggers with keyword....');
            $keyword = $params->keyword;
            $data = $data->filter(function($item)use($keyword){
                //check string exist inside or not
                if(stristr($item->name, $keyword) == TRUE || stristr($item->regno, $keyword) == TRUE || stristr($item->uid, $keyword) == TRUE ) {
                    return true;
                }else{
                    return false;
                }

            });
        }


        if($params->fromdate){
            error_log('Filtering bloggers with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering bloggers with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering bloggers with status....');
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

    private function getBlogger($uid) {
        $data = Blogger::where('uid', $uid)->with('company')->with('user')->where('status', 1)->first();
        return $data;
    }

    private function getBloggerById($id) {
        $data = Blogger::where('id', $id)->with('company')->with('user')->where('status', 1)->first();
        return $data;
    }

    private function createBlogger($params) {

        $params = $this->checkUndefinedProperty($params , $this->bloggerAllCols());

        $data = new Blogger();
        $data->uid = Carbon::now()->timestamp . Blogger::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->email = $params->email;
        $data->companyBelongings = $params->companyBelongings;
        $data->status = true;

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

    //Make Sure Blogger is not empty when calling this function
    private function updateBlogger($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->bloggerAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->tel1 = $params->tel1;
        $data->email = $params->email;
        $data->status = true;

        if($this->isEmpty($params->companyBelongings)){
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

    private function deleteBlogger($data) {

        $articles = $data->articles;
        foreach($articles as $article){
            if(!$this->deleteArticle($article)){
                return null;
            }
        }
        
        $data->status = false;
        if(!$this->saveModel($data)){
            return null;
        }

        return $data->refresh();
    }


    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------

    public function bloggerAllCols() {

        return ['id','company_id', 'user_id', 'uid' ,'name', 'tel1', 'desc' , 
        'imgpath' , 'imgpublicid'  , 'email' ,'status','companyBelongings'];

    }

}
