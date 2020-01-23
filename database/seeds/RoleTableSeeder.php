<?php

use Illuminate\Database\Seeder;
use App\Role;
use Carbon\Carbon;
use App\Module;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();  
        $checkid = false;
        $uid = '';
        while(!$checkid){
            $uid = Carbon::now()->timestamp. '-' . (Role::count() + 1);
            if (!Role::where('uid', '=', $uid)->exists()) {
                // user found
                $checkid = true;
            }
        }
        $role->uid = $uid;
        $role->name = 'superadmin';
        $role->desc = 'The highest authority of the system';
        $role->save();

        $role = new Role();  
        $checkid = false;
        $uid = '';
        while(!$checkid){
            $uid = Carbon::now()->timestamp. '-' . (Role::count() + 1);
            if (!Role::where('uid', '=', $uid)->exists()) {
                // user found
                $checkid = true;
            }
        }
        $role->uid = $uid;
        $role->name = 'admin';
        $role->desc = 'The highest authority of the management of company';
        $role->save();

        $role = new Role();  
        $checkid = false;
        $uid = '';
        while(!$checkid){
            $uid = Carbon::now()->timestamp. '-' . (Role::count() + 1);
            if (!Role::where('uid', '=', $uid)->exists()) {
                // user found
                $checkid = true;
            }
        }
        $role->uid = $uid;
        $role->name = 'headmanager';
        $role->desc = 'The highest authority of the resource management of company';
        $role->save();

        $role = new Role();  
        $checkid = false;
        $uid = '';
        while(!$checkid){
            $uid = Carbon::now()->timestamp. '-' . (Role::count() + 1);
            if (!Role::where('uid', '=', $uid)->exists()) {
                // user found
                $checkid = true;
            }
        }
        $role->uid = $uid;
        $role->name = 'videomanager';
        $role->desc = 'The manager of video resources of company';
        $role->save();

        $role = new Role();  
        $checkid = false;
        $uid = '';
        while(!$checkid){
            $uid = Carbon::now()->timestamp. '-' . (Role::count() + 1);
            if (!Role::where('uid', '=', $uid)->exists()) {
                // user found
                $checkid = true;
            }
        }
        $role->uid = $uid;
        $role->name = 'storemanager';
        $role->desc = 'The manager of store resources of company';
        $role->save();

        
        $role = new Role();  
        $checkid = false;
        $uid = '';
        while(!$checkid){
            $uid = Carbon::now()->timestamp. '-' . (Role::count() + 1);
            if (!Role::where('uid', '=', $uid)->exists()) {
                // user found
                $checkid = true;
            }
        }
        $role->uid = $uid;
        $role->name = 'groupmanager';
        $role->desc = 'The manager of resources of group';
        $role->save();

        $role = new Role();  
        $checkid = false;
        $uid = '';
        while(!$checkid){
            $uid = Carbon::now()->timestamp. '-' . (Role::count() + 1);
            if (!Role::where('uid', '=', $uid)->exists()) {
                // user found
                $checkid = true;
            }
        }
        $role->uid = $uid;
        $role->name = 'user';
        $role->desc = 'The normal user of application';
        $role->save();
    }
}
