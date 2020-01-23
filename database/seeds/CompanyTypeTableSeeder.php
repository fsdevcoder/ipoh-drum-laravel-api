<?php

use Illuminate\Database\Seeder;
use App\CompanyType;
use Carbon\Carbon;

class CompanyTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companytype = new CompanyType();
        $checkid = false;
        $uid = '';
        while(!$checkid){
            $uid = '3' . Carbon::now()->timestamp;
            if (!CompanyType::where('uid', '=', $uid)->exists()) {
                $checkid = true;
            }
        }
        $companytype->uid = $uid;
        $companytype->name = 'management';
        $companytype->save();
        
        $companytype = new CompanyType();
        $uid = '';
        $checkid = false;
        while(!$checkid){
            $uid = '3' . Carbon::now()->timestamp;
            if (!CompanyType::where('uid', '=', $uid)->exists()) {
                $checkid = true;
            }
        }
        $companytype->uid = $uid;
        $companytype->name = 'client';
        $companytype->save();
        
        $companytype = new CompanyType();
        $uid = '';
        $checkid = false;
        while(!$checkid){
            $uid = '3' . Carbon::now()->timestamp;
            if (!CompanyType::where('uid', '=', $uid)->exists()) {
                $checkid = true;
            }
        }
        $companytype->uid = $uid;
        $companytype->name = 'supplier';
        $companytype->save();
    }
}
