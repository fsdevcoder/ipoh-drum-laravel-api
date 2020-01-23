<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Store;
use App\Company;
use Carbon\Carbon;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for($x=0 ; $x<50 ; $x++){
            $store = new Store();
            $store->uid =  Carbon::now()->timestamp . '-' . (Store::count() + 1);
            $store->name =  $faker->unique()->jobTitle;
            $store->contact =  $faker->unique()->jobTitle;
            $store->email =  $faker->unique()->jobTitle;
            $store->rating =  $faker->numberBetween($min = 0.0, $max = 5.0);;
            $store->address = $faker->sentence;
            $store->state = $faker->unique()->jobTitle;
            $store->desc = $faker->sentence;
            $store->postcode = $faker->unique()->jobTitle;
            $store->country = $faker->unique()->jobTitle;
            $store->status = true;
            $company = Company::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
            $store->company()->associate($company);
            $store->save();
        }
    }
}
