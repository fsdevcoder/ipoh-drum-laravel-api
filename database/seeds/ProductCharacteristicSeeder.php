<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\ProductCharacteristic;
use Carbon\Carbon;

class ProductCharacteristicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {  
        $faker = Faker::create();

        $productcharacteristic = new ProductCharacteristic();
        $productcharacteristic->uid =  Carbon::now()->timestamp . '-' . (ProductCharacteristic::count() + 1);
        $productcharacteristic->name =  'Flammable';
        $productcharacteristic->desc = $faker->sentence;
        $productcharacteristic->save();

        
        $productcharacteristic = new ProductCharacteristic();
        $productcharacteristic->uid =  Carbon::now()->timestamp . '-' . (ProductCharacteristic::count() + 1);
        $productcharacteristic->name =  'Glasses';
        $productcharacteristic->desc = $faker->sentence;
        $productcharacteristic->save();
        
        $productcharacteristic = new ProductCharacteristic();
        $productcharacteristic->uid =  Carbon::now()->timestamp . '-' . (ProductCharacteristic::count() + 1);
        $productcharacteristic->name =  'Explode';
        $productcharacteristic->desc = $faker->sentence;
        $productcharacteristic->save();
    }
}
