<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Type;
use Carbon\Carbon;

class TypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();

        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Instrument';
        $type->icon = 'microphone';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();

        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Accessories';
        $type->icon = 'bowtie';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();

        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Second Hand';
        $type->icon = 'trash';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();

        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Ticket';
        $type->icon = 'paper';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();

        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Souvenir';
        $type->icon = 'flower';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();


        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Electronic';
        $type->icon = 'outlet';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();


        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Kid';
        $type->icon = 'person';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();


        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Gaming';
        $type->icon = 'game-controller-a';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();

        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Sport';
        $type->icon = 'american-football';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();

        $type = new Type();
        $type->uid =  Carbon::now()->timestamp . '-' . (Type::count() + 1);
        $type->name =  'Clothing';
        $type->icon = 'shirt';
        $type->desc = $faker->sentence;
        $type->status = true;
        $type->save();
    }
}
