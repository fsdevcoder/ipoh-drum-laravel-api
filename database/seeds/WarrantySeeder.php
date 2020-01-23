<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Warranty;
use Carbon\Carbon;

class WarrantySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {  
        $faker = Faker::create();

        $warranty = new Warranty();
        $warranty->uid =  Carbon::now()->timestamp . '-' . (Warranty::count() + 1);
        $warranty->name =  '1 Year Warranty';
        $warranty->desc = '1 Year Warranty';
        $warranty->policy = 'Terms and condition apply';
        $warranty->period = 12;
        $warranty->status = true;
        $warranty->save();

        $warranty = new Warranty();
        $warranty->uid =  Carbon::now()->timestamp . '-' . (Warranty::count() + 1);
        $warranty->name =  '2 Year Warranty';
        $warranty->desc = '2 Year Warranty';
        $warranty->policy = 'Terms and condition apply';
        $warranty->period = 24;
        $warranty->status = true;
        $warranty->save();
    }
}
