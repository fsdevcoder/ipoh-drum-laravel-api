<?php

use Illuminate\Database\Seeder;
use App\Module;
use App\Role;

class RoleAdminSeeder extends Seeder
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
        
         $role = Role::where('name' , 'admin')->first();
         foreach($modules as $module){
             $role->modules()->attach($module->id , ['clearance' => 2]);
         }
    }
}
