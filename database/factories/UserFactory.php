<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'uid' => $faker->ean8,
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'uname' => $faker->userName,
        'icno' => $faker->ean13,
        'tel1' => $faker->e164PhoneNumber,
        'address1' => $faker->address,
        'postcode' => $faker->postcode,
        'city' => $faker->city,
        'role_id'=> $faker->randomElement([1,2,3,4]),
        'state' => $faker->state,
        'country' => $faker->country,
        'email' => $faker->unique()->safeEmail,
        'password' => Hash::make('111111'), // password
        'remember_token' => Str::random(10),
    ];
});
