<?php

namespace App\Traits;
use App\User;
use App\Role;
use App\Inventory;
use App\InventoryBatch;
use App\Module;
use App\Batch;
use App\PurchaseItem;
use App\SaleItem;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;

trait GlobalFunctions {


    public function checkAccessibility($user, $company ,  $clearance) {

        $usermodule = $user->role->modules()->wherePivot('module_id',$module->id)->wherePivot('role_id',$user->role->id)->first();
        if(empty($usermodule)){
            return false;
        }else{

            //Get User company for wide checking
            $groups = $user->groups;
            $companies = collect();
            foreach($groups as $group){
                $companies = $companies->push($group->company);
                foreach($group->company->branches as $branch){
                    $companies = $companies->push($branch);
                }
            }

            $ownwide = false;
            $companywide = false;
            //Performance that affected the wide

            $owner = $item->company;
            foreach($usercompanies as $usercompany){
                if($usercompany->contains('id',$owner->id)){
                    $owner = true;
                    $companywide = true;
                    break;
                }
            }

            //Check The minimum authority of this operation
            $maxclearance = 1;
            //Own wide
            if($ownwide){
                $maxclearance = 3;
            }else if($companywide){
                //Company wide
                $maxclearance = 2;
            }else {
                //System wide
                $maxclearance = 1;
            }

            //Check the request user got the authority to do this operation or not
            if($usermodule->pivot->clearance <= $maxclearance){
                return true;
            }else{
                return false;
            }


        }



    }

    public function checkClearance($user, $company, $module) {
        if($user == null || $module == null){
            return null;
        }
        if($company == null){
            $roles = $user->roles()->where('name' , 'superadmin')->get();
            if(empty($roles)){
                return null;
            }else{
                // Is super admin return highest authority level
                return 1;
            }
        }else{
            $role = $user->roles()->wherePivot('company_id', $company->id)->first();
            $module = $role->modules()->wherePivot('module_id',$module->id)->first();
            if(empty($module)){
                return null;
            }else{
                return $module->pivot->clearance;
            }

        }
    }

    public function checkModule($provider,$name) {

        $module = Module::where('provider',$provider)->where('name',$name)->first();
        if(empty($module)){
            return null;
        }else{
            return $module;
        }
    }

    //Page Pagination
    public function paginateResult($data , $result , $page){

        if($result == null || $result == "" || $result == 0){
            $result = 10;
        }
        if($page == null || $page == "" || $page == 0){
            $page = 1;
        }
        $data = $data->slice(($page-1) * $result)->take($result)->flatten(1);

        return $data;
    }

    //Get Maximun Pages
    public function getMaximumPaginationPage($dataNo , $result){

        if($result == null  || $result == "" || $result == 0){
            $result = 10;
        }

        $maximunPage = ceil($dataNo / $result);

        return $maximunPage;
    }

    //Get Maximun Pages
    public function isEmpty($collection){

        $collection = collect($collection);
        if($collection == null  || empty($collection) || $collection->count() == 0){
            return true;
        }else{
            return false;
        }
    }


    //Split the string to array
    public function splitToArray($data){
        if($this->isEmpty($data)){
            return null;
        }else{
            if(stristr($data, ':') == TRUE){
                $data = collect(explode(',' , $data));
                $finalarray = collect();
                $data->each(function ($item, $key)use($finalarray){
                    $temp = explode(':' , $item);
                    $finalarray->put($temp[0], $temp[1]);
                });
                return $finalarray->toArray();
            }else{
                $data = collect(explode(',' , $data));
                $data = $data->map(function ($item) {
                    return trim($item);
                });
            }
            return $data->toArray();
        }
    }

    
    //Split the string to object
    public function splitToObject($data){
        return  (object) $this->splitToArray($data);
    }


    //convert string to double
    public function toDouble($data){
        if($this->isEmpty($data)){
            return 0.00;
        }else{
            return number_format((float)($data), 2,'.','');
        }
    }

    //convert string to double
    public function toInt($data){
        if($this->isEmpty($data)){
            return 0;
        }else{
            return (int)$data;
        }
    }

    //convert string to double
    public function toDate($data){
        if($this->isEmpty($data)){
            return null;
        }else{
            return Carbon::parse($data);
        }
    }


    //pluck cols inside single data
    public function itemPluckCols($data , $cols){
        $data = collect($data);
        if($this->isEmpty($data) ||$this->isEmpty($cols) ){
            return null;
        }else{
            $data = $data->only($cols);
            return $data;
        }
    }

    //pluck cols inside multiple data
    public function itemsPluckCols($data , $cols){
        $data = collect($data);
        if($this->isEmpty($data) ||$this->isEmpty($cols) ){
            return null;
        }else{
            $data = $data->map(function($item)use($cols){
                return $item->only($cols);
            });

            return $data;
        }
    }
    

    //saveModel
    public function saveModel($data){
        try {
            $data->save();
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    //deleteModel
    public function forceDeleteModel($data){
        try {
            $data->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }

    }
    
    public function checkUndefinedProperty($data , $properties){
        $data = (object) $data;
        foreach($properties as $property){
            if(!isset($data->{$property})){
                $data->{$property} = null;
            }
        }
        return $data;
    }

    public function generateRandomString($length){
        return Str::random($length);
    }

    public function withinTimeRange($startdate , $enddate){
        

        $startdate = $this->toDate($startdate);
        $enddate = $this->toDate($enddate);
        if($this->isEmpty($startdate) ||$this->isEmpty($startdate) ){
            return false;
        }else{
            $currenttime = Carbon::now();
            if($currenttime <= $enddate && $currenttime >= $startdate ){
                return true;
            }else{
                return false;
            }
        }
    }

    public function globalFilter($data , $params){

        $data = collect($data);
        $params = $this->checkUndefinedProperty($params , $this->globalFilterCols());

        if($params->keyword){
            error_log('Filtering channels with keyword....');
            $keyword = $params->keyword;
            $data = $data->filter(function($item)use($keyword){
                //check string exist inside or not
                if(stristr($item->name, $keyword) == TRUE || stristr($item->uid, $keyword) == TRUE || stristr($item->title, $keyword) == TRUE ) {
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


        $data = $data->unique('id');

        return $data;
    }
    
    public function globalFilterCols() {

        return ['keyword','fromdate' ,'todate', 'status'];

    }
}
