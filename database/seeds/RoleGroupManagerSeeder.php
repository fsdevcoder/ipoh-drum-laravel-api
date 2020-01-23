<?php

use Illuminate\Database\Seeder;
use App\Module;
use App\Role;


class RoleGroupManagerSeeder extends Seeder
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
        
        //User
        $role = Role::where('name' , 'groupmanager')->first();
        foreach($modules as $module){
            $role->modules()->attach($module->id, ['clearance' => 3]);
        }
    }
}
