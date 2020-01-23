<?php

namespace App\Traits;
use App\User;
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
use App\ErrorLog;
use App\Payment;
use App\Video;
use Carbon\Carbon;
use DB;

trait LogServices {


    private function getLogListing() {
        $users = User::where('status', true)->get();
        return $users;
    
    }

    private function getLog() {
        
       
    }

    private function createLog($operatorid , $affectorids , $action , $model) {
        
        $operator = User::find($operatorid);
        $affectors = User::find($affectorids);


        foreach($affectors as $affector){
            $affector->affectedlogs()->attach($operator->id , ['action' => $action , 'model' => $model]);
        }
       
    }

    private function createErrorLog($file , $method , $desc , $exception) {
        
        $errorlog = new ErrorLog();
        $errorlog->file = $file;
        $errorlog->method = $method;
        $errorlog->desc = $desc;
        $errorlog->exception = $exception;

        $errorlog->save();

       
    }

    private function updateLog() {
        
       
    }

    private function destroyLog() {
        
    }
}