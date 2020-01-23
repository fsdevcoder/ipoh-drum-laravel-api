<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\ProductFeature;
use Carbon\Carbon;

class ProductFeatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();

        $productfeature = new ProductFeature();
        $productfeature->uid =  Carbon::now()->timestamp . '-' . (ProductFeature::count() + 1);
        $productfeature->name =  'Special Deals';
        $productfeature->desc = $faker->sentence;
        $productfeature->status = true;
        $productfeature->save();

        $productfeature = new ProductFeature();
        $productfeature->uid =  Carbon::now()->timestamp . '-' . (ProductFeature::count() + 1);
        $productfeature->name =  'Promotion';
        $productfeature->desc = $faker->sentence;
        $productfeature->status = true;
        $productfeature->save();

        $productfeature = new ProductFeature();
        $productfeature->uid =  Carbon::now()->timestamp . '-' . (ProductFeature::count() + 1);
        $productfeature->name =  'Flash Sale';
        $productfeature->desc = $faker->sentence;
        $productfeature->status = true;
        $productfeature->save();

        $productfeature = new ProductFeature();
        $productfeature->uid =  Carbon::now()->timestamp . '-' . (ProductFeature::count() + 1);
        $productfeature->name =  'Recommendation';
        $productfeature->desc = $faker->sentence;
        $productfeature->status = true;
        $productfeature->save();
    }
}
