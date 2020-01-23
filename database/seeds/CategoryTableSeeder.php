<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Category;
use Carbon\Carbon;

class CategoryTableSeeder extends Seeder
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
            $category = new Category();
            $category->uid =  Carbon::now()->timestamp . '-' . (Category::count() + 1);
            $category->name =  $faker->unique()->jobTitle;
            $category->desc = $faker->sentence;
            $category->status = true;
            $category->save();
        }
        
    }
}
