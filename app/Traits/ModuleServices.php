<?php

namespace App\Traits;
use App\User;
use App\Module;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\AllServices;

trait ModuleServices {

    use AllServices;

    private function getModules($requester) {

        $data = collect();
        //Role Based Retrieved Done in Role Services
        $roles = $this->getRoles($requester);
        foreach($roles as $role){
            $data = $data->merge($role->modules('modules.status',true)->get());
        }


        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }

    private function filterModules($data , $params) {

        error_log('Filtering modules....');

        if($params->keyword){
            error_log('Filtering modules with keyword....');
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
            error_log('Filtering modules with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering modules with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering modules with status....');
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


    private function getModule($uid) {
        $data = Module::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getModuleById($id) {
        $data = Module::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createModule($params) {

        $params = $this->checkUndefinedProperty($params , $this->moduleAllCols());

        $data = new Module();
        $data->uid = Carbon::now()->timestamp . Module::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->provider = $params->provider;
        $data->status = true;

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure Module is not empty when calling this function
    private function updateModule($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->moduleAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->provider = $params->provider;

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    private function deleteModule($data) {
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
    public function moduleAllCols() {
        
        return ['id','uid', 'name', 'desc' ,'provider', 'status'];
    }

}
