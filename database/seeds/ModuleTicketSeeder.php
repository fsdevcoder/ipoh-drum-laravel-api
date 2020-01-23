<?php

use Illuminate\Database\Seeder;
use App\Module;
use Carbon\Carbon;

class ModuleTicketSeeder extends Seeder
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
        $module->desc = 'Method that retrieving a list of the tickets';
        $module->provider = 'ticket';
        $module->save();
        
        $module = new Module();
        $module->name = 'store';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that create the ticket';
        $module->provider = 'ticket';
        $module->save();
        
        $module = new Module();
        $module->name = 'show';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show the ticket details';
        $module->provider = 'ticket';
        $module->save();
        
        $module = new Module();
        $module->name = 'update';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that update ticket details';
        $module->provider = 'ticket';
        $module->save();
        
        $module = new Module();
        $module->name = 'destroy';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that delete ticket';
        $module->provider = 'ticket';
        $module->save();
        
        $module = new Module();
        $module->name = 'filter';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that filter ticket listing';
        $module->provider = 'ticket';
        $module->save();

        $module = new Module();
        $module->name = 'pluckedIndex';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that retrieving a list of tickets with specific columns';
        $module->provider = 'ticket';
        $module->save();
        
        $module = new Module();
        $module->name = 'pluckedShow';
        $module->uid = Carbon::now()->timestamp .'-' .( Module::count() + 1);
        $module->desc = 'Method that show a ticket details with specific columns';
        $module->provider = 'ticket';
        $module->save();
    }
}
