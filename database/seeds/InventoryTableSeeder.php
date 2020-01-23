<?php

use Illuminate\Database\Seeder;
use App\Inventory;
use App\Store;
use Faker\Factory as Faker;
use App\Company;
use App\Category;
use App\InventoryImage;
use App\Pattern;
use App\Type;
use App\ProductFeature;
use App\ProductReview;
use App\ProductCharacteristic;
use App\InventoryFamily;
use App\Warranty;
use App\Shipping;
use App\ProductPromotion;
use App\Batch;
use App\User;
use Carbon\Carbon;


class InventoryTableSeeder extends Seeder
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
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546657/maxresdefault_zbhu9s.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546692/doggy-day-care_zjjlau.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546693/Document_bfwsws.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546696/20190530_092328_dxtsnz.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546700/abp-008_ndoxa0.png",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546705/doggie-daycare-1-612x250_w66qb8.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546711/Brady-and-Chess-for-CL_lxhwcq.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546720/german-shepherd-248622-1920_pzu7by.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546727/6741447-dog-of-breed-the-griffon-bruxellois-after-grooming-the-doggie-is-dressed-in-a-striped-vest_oei5fz.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546735/51cHSmiDkhL._AC_SY355__j8ho3h.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546757/5377_57_rrau2q.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546769/ENbnKHpAo18GJVST4dC8BDiVmqxzGtIPP14pHOeQhJrXFR4YV125RZHyRvPPLc6DD9-8YXK6_w1080-h608-p-no-v0_azajcm.jpg",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546782/823b8c_613b0ee90bb9436e871600bed5741373_mv2_dkwhqb.webp",
            "https://res.cloudinary.com/dmtxkcmay/image/upload/v1577546796/doggie-school-bus_oduanr.jpg",
        ];
        for($x=0 ; $x<50 ; $x++){
            $inventory = new Inventory();
            $checkid = false;
            $uid = '';
            while(!$checkid){
                $uid = '4' . Carbon::now()->timestamp;
                if (!Inventory::where('uid', '=', $uid)->exists()) {
                    // user found
                    $checkid = true;
                }
            }

            $inventory->uid = $uid;
            $inventory->code = $faker->unique()->ean8;
            $inventory->name = $faker->unique()->jobTitle;
            $inventory->sku = $faker->unique()->ean8;
            $inventory->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
            $inventory->imgpublicid = Carbon::now()->timestamp . Inventory::count();
            $inventory->cost = $faker->numberBetween($min = 1, $max = 1000);
            $inventory->price = $faker->numberBetween($min = 1, $max = 1000);
            $inventory->rating = $faker->randomElement([0,1,2,3,4,5]);
            $inventory->desc = $faker->sentence;
            $inventory->qty = $faker->numberBetween($min = 100, $max = 1000);
            $inventory->stockthreshold = $faker->numberBetween($min = 1, $max = 1000);
            $inventory->salesqty = 0;

            $store = Store::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
            $inventory->store()->associate($store);
            
            $productpromotion = ProductPromotion::find($faker->randomElement([1,2,3,null]));
            $inventory->promotion()->associate($productpromotion);

            $shipping = Shipping::find($faker->randomElement([1,2,null]));
            $inventory->shipping()->associate($shipping);

            $warranty = Warranty::find($faker->randomElement([1,2,null]));
            $inventory->warranty()->associate($warranty);

            if($inventory->promotion != null){
                if($inventory->promotion->qty > 0){
                    $inventory->promoendqty = $inventory->salesqty + $inventory->promotion->qty;
                }
            }

            $inventory->save();

            $inventoryfamily = new InventoryFamily();
            $inventoryfamily->uid = Carbon::now()->timestamp . '-' . (InventoryFamily::count() + 1);
            $inventoryfamily->name = $faker->unique()->jobTitle;
            $inventoryfamily->desc = $faker->sentence;
            $inventoryfamily->cost = $faker->numberBetween($min = 1, $max = 1000);
            $inventoryfamily->price = $faker->numberBetween($min = 1, $max = 1000);
            $inventoryfamily->qty = $inventory->qty;
            $inventoryfamily->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
            $inventoryfamily->imgpublicid = Carbon::now()->timestamp . '-' . (InventoryFamily::count() + 1);
            $inventoryfamily->inventory()->associate($inventory);
            $inventoryfamily->save();

            for($y = 0 ; $y < 4 ; $y++){
                $inventoryfamily = new InventoryFamily();
                $inventoryfamily->uid = Carbon::now()->timestamp . '-' . (InventoryFamily::count() + 1);
                $inventoryfamily->name = $faker->unique()->jobTitle;
                $inventoryfamily->desc = $faker->sentence;
                $inventoryfamily->cost = $faker->numberBetween($min = 1, $max = 1000);
                $inventoryfamily->price = $faker->numberBetween($min = 1, $max = 1000);
                $inventoryfamily->qty = $faker->numberBetween($min = 100, $max = 1000);
                $inventoryfamily->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
                $inventoryfamily->imgpublicid = Carbon::now()->timestamp . '-' . (InventoryFamily::count() + 1);
                $inventoryfamily->inventory()->associate($inventory);
                $inventoryfamily->save();

                for($z = 0 ; $z < $faker->randomElement([0,1,2,3,4,5]) ; $z++){
                    $pattern = new Pattern();
                    $pattern->uid = Carbon::now()->timestamp . '-' . (Pattern::count() + 1);
                    $pattern->name = $faker->unique()->jobTitle;
                    $pattern->desc = $faker->sentence;
                    $pattern->cost = $faker->numberBetween($min = 1, $max = 1000);
                    $pattern->price = $faker->numberBetween($min = 1, $max = 1000);
                    $pattern->qty = $faker->numberBetween($min = 100, $max = 1000);
                    $pattern->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
                    $pattern->inventoryfamily()->associate($inventoryfamily);
                    $pattern->save();
                }
            }

            $image = new InventoryImage();
            $image->uid = Carbon::now()->timestamp . '-' . (InventoryImage::count() + 1);
            $image->name = $faker->unique()->jobTitle;
            $image->imgpath = $inventory->imgpath;
            $image->imgpublicid = Carbon::now()->timestamp . '-' . (InventoryImage::count() + 1);
            $image->inventory()->associate($inventory);
            $image->save();

            for($y = 0 ; $y < 7 ; $y++){
                $image = new InventoryImage();
                $image->uid = Carbon::now()->timestamp . '-' . (InventoryImage::count() + 1);
                $image->name = $faker->jobTitle;
                $image->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
                $image->imgpublicid = Carbon::now()->timestamp . '-' . (InventoryImage::count() + 1);
                $image->inventory()->associate($inventory);
                $image->save();
            }

            $category = Category::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]));
            $inventory->categories()->attach($category);

            $type = Type::find($faker->randomElement([1,2,3,4,5]));
            $inventory->types()->attach($type);

            $productfeature = ProductFeature::find($faker->randomElement([1,2,3,4]));
            $inventory->productfeatures()->attach($productfeature);
            
            $productcharacteristic = ProductCharacteristic::find($faker->randomElement([1,2,3,null]));
            $inventory->characteristics()->attach($productcharacteristic);

            // $batch = new Batch();
            // $batch->uid = $inventory->uid.'-'.($inventory->batches()->where('status','!=','cancel')->count() + 1);
            // $batch->cost = $inventory->cost;
            // $batch->price = $inventory->price;
            // $batch->stock = $inventory->stock;
            // $batch->salesqty = $inventory->salesqty;
            // $batch->batchno = $inventory->batches()->where('status', true)->count() + 1;
            // $batch->curbatch = true;
            // $batch->inventory()->associate($inventory);
            // $batch->save();
        }
        
        for($x=0; $x<200; $x++){
            $productreview = new ProductReview();
            $productreview->uid =  Carbon::now()->timestamp . '-' . (ProductReview::count() + 1);
            $productreview->title =  $faker->jobTitle;
            $productreview->desc = $faker->sentence;
            $productreview->imgpath = $imgs[$faker->randomElement([0,1,2,3,4,5,6,7,8,9,10,11,12])];
            $productreview->imgpublicid = Carbon::now()->timestamp . '-' . (ProductReview::count() + 1);
            $productreview->rating = $faker->randomElement([0,1,2,3,4,5]);
            $productreview->like = $faker->numberBetween($min = 1, $max = 1000);
            $productreview->dislike = $faker->numberBetween($min = 1, $max = 1000);
            $productreview->status = true;
            $inventory = Inventory::find( $faker->numberBetween($min = 1, $max = 50));
            $productreview->inventory()->associate($inventory);
            $productreview->user()->associate(User::find($faker->randomElement([1,2,3,4,5,6,7,8,9,10,11,12])));
            $productreview->save();
        }
    }
}
