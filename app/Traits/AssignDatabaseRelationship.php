<?php

namespace App\Traits;
use App\User;
use App\Role;
use App\Module;
use App\Inventory;
use App\Ticket;
use App\InventoryBatch;
use App\Batch;
use App\PurchaseItem;
use App\SaleItem;
use App\Sale;
use Carbon\Carbon;
use DB;

trait AssignDatabaseRelationship {

    //One To One Or One To Many Relationship
    private function assignCompanyType($data , $param){
        $model = CompanyType::where('status', true)->where('id' , $param->id)->first();
        if($this->isEmpty($model)){
            return false;
        }
        $data->companytype()->associate($model);
        try {
            $data->save();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


    private function assignCompany($data , $param){
        $model = Company::where('status', true)->where('id' , $param->id)->first();
        if($this->isEmpty($model)){
            return false;
        }
        $data->company()->associate($model);
        try {
            $data->save();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    private function assignStore($data , $param){
        $model = Store::where('status', true)->where('id' , $param->id)->first();
        if($this->isEmpty($model)){
            return false;
        }
        $data->store()->associate($model);
        try {
            $data->save();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


    //--------------------------------------------------------------------------------------------------------------------------------


    //2 Tables Many To Many Relationship Assignment

    //Data has Many Users
    private function assignUsersToMany($data , $params){
        foreach($params as $param){
            $model = User::where('status', true)->where('id' , $param->id)->first();
            if($this->isEmpty($model)){
                return false;
            }
            $data->users()->syncWithoutDetaching([$param->id]);
        }
        return true;
    }

    //Data has Many Inventories
    private function assignInventoriesToMany($data , $params){
        foreach($params as $param){
            $model = Inventory::where('status', true)->where('id' , $param->id)->first();
            if($this->isEmpty($model)){
                return false;
            }
            $data->inventories()->syncWithoutDetaching([$param->id]);
        }
        return true;
    }

    //Data has Many Tickets
    private function assignTicketsToMany($data , $params){
        foreach($params as $param){
            $model = Ticket::where('status', true)->where('id' , $param->id)->first();
            if($this->isEmpty($model)){
                return false;
            }
            $data->tickets()->syncWithoutDetaching([$param->id]);
        }
        return true;
    }

    //Data has Many Categories
    private function assignCategoriesToMany($data , $params){
        foreach($params as $param){
            $model = Ticket::where('status', true)->where('id' , $param->id)->first();
            if($this->isEmpty($model)){
                return false;
            }
            $data->tickets()->syncWithoutDetaching([$param->id]);
        }
        return true;
    }

    //3 Tables Many To Many Relationship
    //
    private function assignRolesToManyWithCompany($data , $params){
        foreach($params as $param){
            $model = Role::where('status', true)->where('id' , $param->id)->first();
            if($this->isEmpty($model)){
                return false;
            }
            $data->roles()->syncWithoutDetaching([$param->id => ['user_id' => $param->userid , 'assigned_by' => $param->assigned_by, 'assigned_at' => Carbon::now() ]]);
        }
        return true;
    }


    //Company assign users
    private function assignUsersToManyWithCompany($data , $params){
        foreach($params as $param){
            $model = User::where('status', true)->where('id' , $param->id)->first();
            if($this->isEmpty($model)){
                return false;
            }
            $data->roles()->syncWithoutDetaching([$param->id => ['role_id' => $param->roleid , 'assigned_by' => $param->assigned_by, 'assigned_at' => Carbon::now() ]]);
        }
        return true;
    }
    //Company assign roles
    // private function detachRolesToManyWithUser($data , $params){
    //     foreach($params as $param){

    //         //Model Validation
    //         $model = Role::where('status', true)->where('id' , $param->id)->first();
    //         if($this->isEmpty($model)){
    //             return false;
    //         }
    //         $data->roles()->wherePivot('company_id' , $param->companyid )->detach($param->id);
    //     }
    //     return true;
    // }


}
