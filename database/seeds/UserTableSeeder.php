<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $user = new User();
        $user->uid = 'U111111';
        // $user->uname = 'admin';
        $user->name = 'admin';
        $user->icno = '11111111111';
        $user->email = 'admin@gmail.com';
        $user->password = Hash::make('111111');
        $user->save();
        $user->roles()->attach([['role_id' => 1 , 'company_id'=> 1]]);

        $user->groups()->attach(1);
        
        for($x=0 ; $x<50 ; $x++){
            $user = new User();
            $user->uid = $faker->ean8;
            // $user->uname = 'admin';
            $user->name = $faker->userName;
            $user->icno = $faker->ean13;
            $user->email =  $faker->unique()->safeEmail;
            $user->password = Hash::make('111111');
            // $user->role_id = $faker->randomElement([1,2,3,4]);
            $user->save();
            $user->roles()->attach([['role_id' => $faker->randomElement([1,2]) , 'company_id'=> $faker->randomElement([1,2,3,4,5])]]);

            $user->groups()->attach($faker->randomElement([1,2,3,4,5]));
        }
    }
}