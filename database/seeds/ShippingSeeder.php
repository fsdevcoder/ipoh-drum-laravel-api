<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Shipping;
use Carbon\Carbon;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {  
        $faker = Faker::create();

        $shipping = new Shipping();
        $shipping->uid =  Carbon::now()->timestamp . '-' . (Shipping::count() + 1);
        $shipping->name =  'Pos Laju Delivery';
        $shipping->desc = 'Delivery By Post Laju';
        $shipping->price = 4.80;
        $shipping->maxweight = 10.00;
        $shipping->maxdimension = 20;
        $shipping->status = true;
        $shipping->save();

        $shipping = new Shipping();
        $shipping->uid =  Carbon::now()->timestamp . '-' . (Shipping::count() + 1);
        $shipping->name =  'Ninja Delivery';
        $shipping->desc = 'Delivery By Ninja';
        $shipping->price = 5.00;
        $shipping->maxweight = 10.00;
        $shipping->maxdimension = 20;
        $shipping->status = true;
        $shipping->save();
    }
}
