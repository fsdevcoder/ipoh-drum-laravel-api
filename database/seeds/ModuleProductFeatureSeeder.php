<?php

use Illuminate\Database\Seeder;
use App\Module;
use Carbon\Carbon;

class ModuleProductFeatureSeeder extends Seeder
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
        $module->desc = 'Method that retrieving a list of the productfeatures';
        $module->provider = 'productfeature';
        $module->save();
        
        $module = new Module();
        $module->name = 'store';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that create the productfeature';
        $module->provider = 'productfeature';
        $module->save();
        
        $module = new Module();
        $module->name = 'show';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show the productfeature details';
        $module->provider = 'productfeature';
        $module->save();
        
        $module = new Module();
        $module->name = 'update';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that update productfeature details';
        $module->provider = 'productfeature';
        $module->save();
        
        $module = new Module();
        $module->name = 'destroy';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that delete productfeature';
        $module->provider = 'productfeature';
        $module->save();
        
        $module = new Module();
        $module->name = 'filter';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that filter productfeature listing';
        $module->provider = 'productfeature';
        $module->save();

        $module = new Module();
        $module->name = 'pluckedIndex';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that retrieving a list of productfeatures with specific columns';
        $module->provider = 'productfeature';
        $module->save();
        
        $module = new Module();
        $module->name = 'pluckedShow';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show a productfeature details with specific columns';
        $module->provider = 'productfeature';
        $module->save();
    }
}
