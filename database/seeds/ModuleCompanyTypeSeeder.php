<?php

use Illuminate\Database\Seeder;
use App\Module;
use Carbon\Carbon;

class ModuleCompanyTypeSeeder extends Seeder
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
        $module->desc = 'Method that retrieving a list of the company types';
        $module->provider = 'companytype';
        $module->save();
        
        $module = new Module();
        $module->name = 'store';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that create the company type';
        $module->provider = 'companytype';
        $module->save();
        
        $module = new Module();
        $module->name = 'show';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show the company type details';
        $module->provider = 'companytype';
        $module->save();
        
        $module = new Module();
        $module->name = 'update';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that update company type details';
        $module->provider = 'companytype';
        $module->save();
        
        $module = new Module();
        $module->name = 'destroy';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that delete company type';
        $module->provider = 'companytype';
        $module->save();
        
        $module = new Module();
        $module->name = 'filter';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that filter company type listing';
        $module->provider = 'companytype';
        $module->save();

        $module = new Module();
        $module->name = 'pluckedIndex';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that retrieving a list of company types with specific columns';
        $module->provider = 'companytype';
        $module->save();
        
        $module = new Module();
        $module->name = 'pluckedShow';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show a company type details with specific columns';
        $module->provider = 'companytype';
        $module->save();

    }
}
