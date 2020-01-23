<?php

namespace App\Traits;
use App\User;
use App\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait RoleServices {

    use AllServices;

    private function getRoles($requester) {


        $data = collect();

        //Role Based Retrieve Done in Company Services
        $companies = $this->getCompanies($requester);
        foreach($companies as $company){
            $data = $data->merge($company->roles()->where('roles.status',true)->get());
        }

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterRoles($data , $params) {

        error_log('Filtering roles....');

        if($params->keyword){
            error_log('Filtering roles with keyword....');
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
            error_log('Filtering roles with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering roles with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering roles with status....');
            if($params->status == 'true'){
                $data = $data->where('status', true);
            }else if($params->status == 'false'){
                $data = $data->where('status', false);
            }else{
                $data = $data->where('status', '!=', null);
            }
        }

        if($params->onsale){
            error_log('Filtering roles with on sale status....');
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

    private function getRole($uid) {
        $data = Role::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getRoleById($id) {
        $data = Role::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createRole($params) {

        $params = $this->checkUndefinedProperty($params , $this->roleAllCols());

        $data = new Role();
        $data->uid = Carbon::now()->timestamp . Role::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->status = true;

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    //Make Sure Role is not empty when calling this function
    private function updateRole($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->roleAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    private function deleteRole($data) {
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
    public function roleAllCols() {

        return ['id','uid', 'name' ,'desc', 'status' ];

    }

}
