<?php

namespace App\Traits;
use App\ProductFeature;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\AllServices;

trait ProductFeatureServices {

    use AllServices;

    private function getProductFeatures($requester) {

        $data = collect();
        $temp = ProductFeature::where('status', true)->get();
        $data = $data->merge($temp);

        $data = $data->unique('id')->sortBy('id')->flatten(1);

        return $data;

    }


    private function filterProductFeatures($data , $params) {

        error_log('Filtering productfeatures....');

        if($params->keyword){
            error_log('Filtering productfeatures with keyword....');
            $keyword = $params->keyword;
            $data = $data->filter(function($item)use($keyword){
                //check string exist inside or not
                if(stristr($item->uid, $keyword) == TRUE || stristr($item->name, $keyword) == TRUE) {
                    return true;
                }else{
                    return false;
                }

            });
        }


        if($params->fromdate){
            error_log('Filtering productfeatures with fromdate....');
            $date = Carbon::parse($params->fromdate)->startOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) >= $date);
            });
        }

        if($params->todate){
            error_log('Filtering productfeatures with todate....');
            $date = Carbon::parse($request->todate)->endOfDay();
            $data = $data->filter(function ($item) use ($date) {
                return (Carbon::parse(data_get($item, 'created_at')) <= $date);
            });

        }

        if($params->status){
            error_log('Filtering productfeatures with status....');
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

    private function getProductFeature( $uid) {
        $data = ProductFeature::where('uid', $uid)->where('status', 1)->first();
        return $data;
    }

    private function getProductFeatureById( $id) {
        $data = ProductFeature::where('id', $id)->where('status', 1)->first();
        return $data;
    }

    private function createProductFeature($params) {

        $params = $this->checkUndefinedProperty($params , $this->productFeatureAllCols());

        $data = new ProductFeature();
        $data->uid = Carbon::now()->timestamp . ProductFeature::count();
        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->icon = $params->icon;
        $data->imgpath = $params->imgpath;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    //Make Sure ProductFeature is not empty when calling this function
    private function updateProductFeature($data,  $params) {

        $params = $this->checkUndefinedProperty($params , $this->productFeatureAllCols());

        $data->name = $params->name;
        $data->desc = $params->desc;
        $data->icon = $params->icon;
        $data->imgpath = $params->imgpath;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }

        return $data->refresh();
    }

    private function deleteProductFeature($data) {
        $data->status = false;
        if($this->saveModel($data)){
            return $data->refresh();
        }else{
            return null;
        }
        return $data->refresh();
    }

    
    private function addInventoryToProductFeature($inventory) {

      
        //Promotion
        if($inventory->promotion){
            $productfeature = $this->getProductFeatureById(2);
            if($this->isEmpty($productfeature)){
                return null;
            }
            $productfeature->inventories()->syncWithoutDetaching([$inventory->id]);
        }

        //Special Deal
        $productfeature = $this->getProductFeatureById(1);
        if($this->isEmpty($productfeature)){
            return null;
        }
        $productfeature->inventories()->syncWithoutDetaching([$inventory->id]);

        //Recommendation
        $productfeature = $this->getProductFeatureById(4);
        if($this->isEmpty($productfeature)){
            return null;
        }
        $productfeature->inventories()->syncWithoutDetaching([$inventory->id]);


        

        return $productfeature->refresh();
    }


    //Modifying Display Data
    // -----------------------------------------------------------------------------------------------------------------------------------------
    public function productFeatureAllCols() {
        
        return ['id','uid', 'name' ,'desc', 'imgpath', 'imgpublicid', 'icon', 'status'];
    }
}
