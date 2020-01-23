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
use App\Traits\GlobalFunctions;
use DB;

trait NotificationFunctions {

    use GlobalFunctions;

    public function getRetrievedSuccessMsg($provider){
        return $provider. ' retrieved successfully.';
    }
 
    public function getNotFoundMsg($provider){
        return $provider. ' Not Found. Please Try Again Later';
    }
    
    public function getCreatedSuccessMsg($provider){
        return $provider. ' created successfully.';
    }

    public function getUpdatedSuccessMsg($provider){
        return $provider. ' updated successfully.';
    }
    
    public function getDeletedSuccessMsg($provider){
        return $provider. ' deleted successfully.';
    }

    public function getErrorMsg(){
        return 'Something went wrong. Please Try Again Later.';
    }
    
    
    public function errorResponse(){
        $response['data'] = null;
        $response['status'] = 'error';
        $response['msg'] = $this->getErrorMsg();
        $response['code'] = 404;
        return response()->json($response, 404);
    }
    
    public function notFoundSuccessResponse($provider){
        $response['data'] = null;
        $response['status'] = 'success';
        $response['msg'] = $this->getNotFoundMsg($provider);
        $response['code'] = 200;
        return response()->json($response, 200);
    }

    public function notFoundResponse($provider){
        $response['data'] = null;
        $response['status'] = 'error';
        $response['msg'] = $this->getNotFoundMsg($provider);
        $response['code'] = 404;
        return response()->json($response, 404);
    }
    
    public function successResponse($provider , $data , $type){

        switch($type){
            case 'create' : 
                $response['status'] = 'success';
                $response['msg'] = $this->getCreatedSuccessMsg($provider);
                $response['data'] = $data;
                $response['code'] = 200;
                break;

            case 'update' : 
                $response['status'] = 'success';
                $response['msg'] = $this->getUpdatedSuccessMsg($provider);
                $response['data'] = $data;
                $response['code'] = 200;
                break;

            case 'retrieve' : 
                $response['status'] = 'success';
                $response['msg'] = $this->getRetrievedSuccessMsg($provider);
                $response['data'] = $data;
                $response['code'] = 200;
                break;

            case 'delete' : 
                $response['status'] = 'success';
                $response['msg'] = $this->getDeletedSuccessMsg($provider);
                $response['data'] = $data;
                $response['code'] = 200;
                break;

            default :
                $response['status'] = 'success';
                $response['msg'] = 'Operation success';
                $response['data'] = $data;
                $response['code'] = 200;
                break;

        }

        return response()->json($response, 200);
    }

    public function successPaginateResponse($provider , $data , $pageSize, $pageNumber){

        //Page Pagination Result List
        //Default return 10
        $paginateddata = $this->paginateResult($data, $pageSize, $pageNumber);
        $response['status'] = 'success';
        $response['data'] = $paginateddata;
        $response['maximumPages'] = $this->getMaximumPaginationPage($data->count(), $pageSize);
        $response['totalResult'] = $data->count();
        $response['msg'] = $this->getRetrievedSuccessMsg($provider);
        $response['code'] = 200;

        return response()->json($response, 200);
    }
    
    public function errorPaginateResponse($provider){

        $response['status'] = 'success';
        $response['data'] = null;
        $response['maximumPages'] = 0;
        $response['totalResult'] = 0;
        $response['msg'] = $this->getNotFoundMsg($provider);
        $response['code'] = 200;

        return response()->json($response, 200);
    }
}