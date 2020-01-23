<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Sale;
use App\SaleItem;
use App\InventoryFamily;
use App\Pattern;
use App\Company;
use Carbon\Carbon;

class SaleTableSeeder extends Seeder
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
            $sale = new Sale();
            $sale->uid =  Carbon::now()->timestamp . '-' . (Sale::count() + 1);
            $sale->user_id =  $faker->numberBetween($min = 1, $max = 10);
            $sale->store_id =  $faker->numberBetween($min = 1, $max = 10);
            $sale->email =  $faker->safeEmail;
            $sale->contact =  $faker->ean13;
            $sale->qty =  $faker->numberBetween($min = 1, $max = 10);
            $sale->disc =  $faker->numberBetween($min = 1, $max = 100);
            $sale->totalcost =  $faker->numberBetween($min = 1, $max = 100);
            $sale->totalprice =  $faker->numberBetween($min = 1, $max = 100);
            $sale->charge =  $faker->numberBetween($min = 1, $max = 100);
            $sale->net =  $faker->numberBetween($min = 1, $max = 100);
            $sale->grandtotal =  $faker->numberBetween($min = 1, $max = 100);
            $sale->pos =  false;
            $sale->save();
            
            $saleitem = new SaleItem();
            $saleitem->uid =  Carbon::now()->timestamp . '-' . (SaleItem::count() + 1);
            $inventoryfamily = InventoryFamily::find($faker->numberBetween($min = 1, $max = 20));
            $saleitem->name =  $inventoryfamily->inventory->name.':'.$inventoryfamily->name;
            $saleitem->qty = $faker->numberBetween($min = 1, $max = 10);
            $saleitem->desc =  $inventoryfamily->desc;
            $saleitem->cost =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->price =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->disc =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->totalprice =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->totalcost =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->grandtotal =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->type =  "inventoryfamily";

            $saleitem->sale()->associate($sale);
            $saleitem->inventoryfamily()->associate($inventoryfamily);
            $saleitem->save();

            
            $saleitem = new SaleItem();
            $saleitem->uid =  Carbon::now()->timestamp . '-' . (SaleItem::count() + 1);
            $pattern = Pattern::find($faker->numberBetween($min = 1, $max = 20));
            $saleitem->name =  $pattern->inventoryfamily->inventory->name.':'.$pattern->inventoryfamily->name. ':'.$pattern->name;
            $saleitem->qty = $faker->numberBetween($min = 1, $max = 10);
            $saleitem->desc =  $pattern->desc;
            $saleitem->cost =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->price =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->disc =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->totalprice =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->totalcost =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->grandtotal =  $faker->numberBetween($min = 1, $max = 100);
            $saleitem->type =  "pattern";

            $saleitem->sale()->associate($sale);
            $saleitem->pattern()->associate($pattern);
            $saleitem->save();
        }
    }
}
