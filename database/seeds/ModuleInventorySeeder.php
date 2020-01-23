<?php

use Illuminate\Database\Seeder;
use App\Module;
use Carbon\Carbon;

class ModuleInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $module = new Module();
        $module->name = 'index';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1) ;
        $module->desc = 'Method that retrieving a list of the inventories';
        $module->provider = 'inventory';
        $module->save();
        
        $module = new Module();
        $module->name = 'store';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that create the inventory';
        $module->provider = 'inventory';
        $module->save();
        
        $module = new Module();
        $module->name = 'show';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show the inventory details';
        $module->provider = 'inventory';
        $module->save();
        
        $module = new Module();
        $module->name = 'update';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that update inventory details';
        $module->provider = 'inventory';
        $module->save();
        
        $module = new Module();
        $module->name = 'destroy';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that delete inventory';
        $module->provider = 'inventory';
        $module->save();
        
        $module = new Module();
        $module->name = 'filter';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that filter inventory listing';
        $module->provider = 'inventory';
        $module->save();

        $module = new Module();
        $module->name = 'pluckedIndex';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that retrieving a list of inventories with specific columns';
        $module->provider = 'inventory';
        $module->save();
        
        $module = new Module();
        $module->name = 'pluckedShow';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show a inventory details with specific columns';
        $module->provider = 'inventory';
        $module->save();

    }
}
