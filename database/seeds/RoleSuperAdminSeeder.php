<?php

use Illuminate\Database\Seeder;
use App\Module;
use App\Role;

class RoleSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //All Modules
        $modules = Module::all();
        
        //Super Admin
         $role = Role::where('name' , 'superadmin')->first();
        foreach($modules as $module){
            $role->modules()->attach($module->id, ['clearance' => 1]);
        }
    }
}
