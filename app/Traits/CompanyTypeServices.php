<?php

namespace App\Traits;
use App\User;
use App\CompanyType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\AllServices;

trait CompanyTypeServices {

    use AllServices;

    private function getCompanyTypes($requester) {

        $data = collect();

        $temp = CompanyType::where('status', true)->get();
        $data = $data->merge($temp);

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterCompanyTypes($data , $params) {

        error_log('Filtering companytypes....');

        if($params->keyword){
            error_log('Filtering companytypes with keyword....');
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
            error_log('Filtering companytypes with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering companytypes with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering companytypes with status....');
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


    private function getCompanyType($uid) {
        $data = CompanyType::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getCompanyTypeById($id) {
        $data = CompanyType::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createCompanyType($params) {

        $params = $this->checkUndefinedProperty($params , $this->companyTypeAllCols());

        $data = new CompanyType();
        $data->uid = Carbon::now()->timestamp . CompanyType::count();
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

    //Make Sure CompanyType is not empty when calling this function
    private function updateCompanyType($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->companyTypeAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;

        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    private function deleteCompanyType($data) {
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
    public function companyTypeAllCols() {

        return ['id','uid', 'name', 'desc', 'status'];
    }

}
