<?php

use Illuminate\Database\Seeder;
use App\Module;
use Carbon\Carbon;

class ModuleVideoSeeder extends Seeder
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
        $module->desc = 'Method that retrieving a list of the videos';
        $module->provider = 'video';
        $module->save();
        
        $module = new Module();
        $module->name = 'store';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that create the video';
        $module->provider = 'video';
        $module->save();
        
        $module = new Module();
        $module->name = 'show';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show the video details';
        $module->provider = 'video';
        $module->save();
        
        $module = new Module();
        $module->name = 'update';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that update video details';
        $module->provider = 'video';
        $module->save();
        
        $module = new Module();
        $module->name = 'destroy';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that delete video';
        $module->provider = 'video';
        $module->save();
        
        $module = new Module();
        $module->name = 'filter';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that filter video listing';
        $module->provider = 'video';
        $module->save();

        $module = new Module();
        $module->name = 'pluckedIndex';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that retrieving a list of videos with specific columns';
        $module->provider = 'video';
        $module->save();
        
        $module = new Module();
        $module->name = 'pluckedShow';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show a video details with specific columns';
        $module->provider = 'video';
        $module->save();
    }
}
