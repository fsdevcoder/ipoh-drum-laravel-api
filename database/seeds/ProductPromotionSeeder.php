<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\ProductPromotion;
use Carbon\Carbon;

class ProductPromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $productpromotion = new ProductPromotion();
        $productpromotion->uid =  Carbon::now()->timestamp . '-' . (ProductPromotion::count() + 1);
        $productpromotion->name =  'Year End Sales';
        $productpromotion->desc = 'All Price Discount up to 50%';
        $productpromotion->discpctg = 0.5;
        $productpromotion->disc = 0.00;
        $productpromotion->discbyprice = false;
        $productpromotion->qty = 0;
        $productpromotion->promostartdate = Carbon::now();
        $productpromotion->promoenddate = Carbon::now()->addDays(18);
        $productpromotion->status = true;
        $productpromotion->save();

        $productpromotion = new ProductPromotion();
        $productpromotion->uid =  Carbon::now()->timestamp . '-' . (ProductPromotion::count() + 1);
        $productpromotion->name =  'Happy Eleven Eleven';
        $productpromotion->desc = 'All Price Discount RM 11';
        $productpromotion->discpctg = 0.00;
        $productpromotion->disc = 11.00;
        $productpromotion->discbyprice = true;
        $productpromotion->qty = 0;
        $productpromotion->promostartdate = Carbon::now();
        $productpromotion->promoenddate = Carbon::now()->addDays(18);
        $productpromotion->status = true;
        $productpromotion->save();
        
        $productpromotion = new ProductPromotion();
        $productpromotion->uid =  Carbon::now()->timestamp . '-' . (ProductPromotion::count() + 1);
        $productpromotion->name =  'Crazy Store Clearance';
        $productpromotion->desc = 'All Item discount up to 70% and limited stock';
        $productpromotion->discpctg = 0.7;
        $productpromotion->disc = 11.00;
        $productpromotion->discbyprice = false;
        $productpromotion->qty = 10;
        $productpromotion->promostartdate = Carbon::now();
        $productpromotion->promoenddate = Carbon::now()->addDays(18);
        $productpromotion->status = true;
        $productpromotion->save();
        
    }
}
