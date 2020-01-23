<?php

namespace App\Traits;
use App\User;
use App\Group;
use App\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait GroupServices {

    use AllServices;

    private function getGroups($requester) {

        $data = collect();
        $companies = $requester->companies;
        foreach($companies as $company){
            $clearance = $this->checkClearance($requester, $company ,  $this->checkModule('group','index'));
            error_log($clearance);
            switch ($clearance) {
                //System Wide
                case 1:
                    $temp = Group::where('status', true)->get();
                    $data = $data->merge($temp);
                    break;
                //Company Wide
                case 2:
                //Group Wide
                case 3:
                    $data = $data->merge($company->groups()->where('status',true)->get());
                    break;
                //Own Wide
                case 4:
                    $data = $data->merge($requester->groups()->where('status',true)->get());
                    break;
                default:
                    break;
            }

        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterGroups($data , $params) {

        error_log('Filtering groups....');

        if($params->keyword){
            error_log('Filtering groups with keyword....');
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
            error_log('Filtering groups with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering groups with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering groups with status....');
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

    private function getGroup($uid) {
        $data = Group::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getGroupById($id) {
        $data = Group::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createGroup($params) {

        $params = $this->checkUndefinedProperty($params , $this->groupAllCols());

        $data = new Group();
        $data->uid = Carbon::now()->timestamp . Group::count();
        $data->name = $params->name;
        $data->desc = $params->desc;

        $company = $this->getCompanyById($params->company_id);
        if($this->isEmpty($company)){
            return null;
        }
        $data->company()->associate($company);

        $data->status = true;

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Group is not empty when calling this function
    private function updateGroup($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->groupAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;

        $company = $this->getCompanyById($params->company_id);
        if($this->isEmpty($company)){
            return null;
        }
        $data->company()->associate($company);

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    private function deleteGroup($data) {
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }


    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function groupAllCols() {

        return ['id','uid', 'company_id', 'name', 'desc', 'status'];
    }
}
