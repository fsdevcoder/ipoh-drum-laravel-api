<?php

use Illuminate\Database\Seeder;
use App\Ticket;
use App\TicketImage;
use App\Store;
use Faker\Factory as Faker;
use App\Company;
use App\Category;
use App\Type;
use App\ProductFeature;
use App\Batch;
use Carbon\Carbon;


class TicketTableSeeder extends Seeder
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
        for($x=0 ; $x<50 ; $x++){
            $ticket = new Ticket();
            $checkid = false;
            $uid = '';
            while(!$checkid){
                $uid = '4' . Carbon::now()->timestamp;
                if (!Ticket::where('uid', '=', $uid)->exists()) {
                    // user found
                    $checkid = true;
                }
            }

            $ticket->uid = $uid;
            $ticket->code = $faker->unique()->ean8;
            $ticket->name = $faker->unique()->jobTitle;
            $ticket->sku = $faker->unique()->ean8;
            $ticket->price = $faker->randomDigit;
            $ticket->desc = $faker->sentence;
            $ticket->qty = $faker->randomDigit;
            $ticket->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
            $ticket->stockthreshold = $faker->randomDigit;
            $ticket->salesqty = 0;
            $ticket->enddate = Carbon::now()->addDays(2);
            $store = Store::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
            $ticket->store()->associate($store);

            $ticket->save();
            $image = new TicketImage();
            $image->uid = Carbon::now()->timestamp . '-' . (TicketImage::count() + 1);
            $image->name = $faker->unique()->jobTitle;
            $image->imgpath = $ticket->imgpath;
            $image->ticket()->associate($ticket);
            $image->save();

            for($y = 0 ; $y < 7 ; $y++){
                $image = new TicketImage();
                $image->uid = Carbon::now()->timestamp . '-' . (TicketImage::count() + 1);
                $image->name = $faker->unique()->jobTitle;
                $image->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
                $image->ticket()->associate($ticket);
                $image->save();
            }

            $category = Category::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
            $ticket->categories()->attach($category);

            $type = Type::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
            $ticket->types()->attach($type);

            $productfeature = ProductFeature::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
            $ticket->productfeatures()->attach($productfeature);

        }
    }
}
