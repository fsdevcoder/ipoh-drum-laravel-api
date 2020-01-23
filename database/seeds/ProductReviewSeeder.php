<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\ProductReview;
use App\Inventory;
use Carbon\Carbon;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $imgs = [
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573966125/Inventory/media_i1h1g9.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573966094/Inventory/media_tk8i6z.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573966064/Inventory/492_wthrw8.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965837/Inventory/20170904004753_tt47au.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965818/Inventory/d2729373_d5gdle.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965780/Inventory/544a32fb-1559013872-4742b6b7d1b9e1f5a7fb351a52dc2b0d_fzig8a.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965764/Inventory/3700811bde38eb4991174e373f6ea99464c5f124_tbli2q.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965417/Inventory/white-pomeranian-long-1024x555_mrks2o.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573966034/Inventory/hqdefault_op3wyk.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965858/Inventory/maxresdefault_imfbdp.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965837/Inventory/20170904004753_tt47au.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965780/Inventory/544a32fb-1559013872-4742b6b7d1b9e1f5a7fb351a52dc2b0d_fzig8a.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1573965745/Inventory/DFEgU7QVwAA8NnG_noulzy.jpg",
        ];

        for($x=0; $x<50; $x++){
            $productreview = new ProductReview();
            $productreview->uid =  Carbon::now()->timestamp . '-' . (ProductReview::count() + 1);
            $productreview->title =  $faker->unique()->jobTitle;
            $productreview->desc = $faker->sentence;
            $productreview->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
            $productreview->rating = $faker->randomElement([0,1,2,3,4,5]);
            $productreview->like = $faker->randomDigit;
            $productreview->dislike = $faker->randomDigit;
            $productreview->status = true;
            $inventory = Inventory::find($faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12]));
            $productreview->inventory()->associate($inventory);
            $productreview->save();
        }
    }
}
