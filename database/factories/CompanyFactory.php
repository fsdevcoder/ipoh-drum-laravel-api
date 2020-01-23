<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'uid' => $faker->ean8,
        'name' => $faker->company,
        'regno' => $faker->unique()->ean8,
        'tel1' => $faker->unique()->e164PhoneNumber,
        'fax1' => $faker->unique()->ean13,
        'email1' => $faker->unique()->safeEmail,
        'address1' => $faker->address,
        'postcode' => $faker->postcode,
        'city' => $faker->city,
        'state' => $faker->state,
        'company_type_id' =>1,
        'country' => $faker->country,
    ];
});
