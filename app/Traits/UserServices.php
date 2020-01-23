<?php

namespace App\Traits;
use App\User;
use App\Company;
use App\Role;
use App\Inventory;
use App\InventoryBatch;
use App\Module;
use App\PurchaseItem;
use App\SaleItem;
use App\Sale;
use App\Article;
use App\Category;
use App\CompanyType;
use App\Group;
use App\Log;
use App\Payment;
use App\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait UserServices {

    use AllServices;

    private function getUsers($requester) {

        $data = collect();
        $companies = $requester->companies;
        foreach($companies as $company){
            $clearance = $this->checkClearance($requester, $company ,  $this->checkModule('user','index'));
            error_log($clearance);
            switch ($clearance) {
                //System Wide
                case 1:
                    $temp = User::where('status', true)->get();
                    $data = $data->merge($temp);
                    break 2;
                //Company Wide
                case 2:
                    $temp = $company->users()->where('status',true)->get();
                    $data = $data->merge($temp);
                    break;
                //Group Wide
                case 3:
                    $groups = $requester->groups;
                    foreach($groups as $group){
                        $data = $data->merge($group->users()->where('status',true)->get());
                    }
                    break;
                //Own Wide
                case 4:
                    return $data = $data->push($requester);
                    break;
                default:
                    break;
            }
    
        }
        
        $data = $data->unique('id');

        return $data;
    
    }


    private function filterUsers($data , $params) {

        error_log('Filtering users....');

        if($params->keyword){
            error_log('Filtering users with keyword....');
            $keyword = $params->keyword;
            $data = $data->filter(function($item)use($keyword){
                //check string exist inside or not
                if(stristr($item->name, $keyword) == TRUE || stristr($item->email, $keyword) == TRUE || stristr($item->icno, $keyword) == TRUE ) {
                    return true;
                }else{
                    return false;
                }
            
            });
        }

             
        if($params->fromdate){
            error_log('Filtering users with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering users with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });
            
        } 

        if($params->status){
            error_log('Filtering users with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }
        
        if($params->company_id){
            error_log('Filtering users with company id....');
            $company_id = $params->company_id;
            $data = $data->filter(function ($item) use($company_id) {
                return $item->companies->contains('id' , $company_id);
            });
        }

       
        $data = $data->unique('id');

        return $data;
    }


    private function getUser($uid) {
        $data = User::with('roles', 'groups.company')->where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getUserById($id) {
        $data = User::with('roles', 'groups.company')->where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createUser($params) {


        $params = $this->checkUndefinedProperty($params , $this->userAllCols());

        $data = new User();
        $data->uid = Carbon::now()->timestamp . User::count();
        $data->name = $params->name;
        $data->email = $params->email;
        $data->icno = $params->icno;
        $data->tel1 = $params->tel1;
        $data->tel2 = $params->tel2;
        $data->address1 = $params->address1;
        $data->address2 = $params->address2;
        $data->postcode = $params->postcode;
        $data->city = $params->city;
        $data->state = $params->state;
        $data->country = $params->country;
        $data->password = Hash::make($params->password);
        $data->status = true;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    
        
    }

    //Make Sure User is not empty when calling this function
    private function updateUser($data,  $params) {
        
        $params = $this->checkUndefinedProperty($params , $this->userAllCols());

        $grouparr = [];
        $data->name = $params->name;
        $data->email = $params->email;
        $data->icno = $params->icno;
        $data->tel1 = $params->tel1;
        $data->tel2 = $params->tel2;
        $data->address1 = $params->address1;
        $data->address2 = $params->address2;
        $data->postcode = $params->postcode;
        $data->city = $params->city;
        $data->state = $params->state;
        $data->country = $params->country;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    private function deleteUser($data) {
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    // Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function userAllCols() {

        return ['id','uid', 'name', 'imgpath', 'imgpublicid','email','icno',
        'tel1','tel2','address1','address2','postcode','city','state','country',
        'password','last_login','last_active','status'];

    }
    
}