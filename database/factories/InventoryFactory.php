<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Inventory;
use Faker\Generator as Faker;

$factory->define(Inventory::class, function (Faker $faker) {
    return [
        'uid' => $faker->ean8,
        'company_id' => $faker->randomElement([1,2,3,4,5]),
        'name' => $faker->unique()->jobTitle,
        'desc' => $faker->sentence,
        'code' => $faker->unique()->ean8,
        'sku' => $faker->unique()->ean8,
        'price' => $faker->randomDigit,
        'cost' => $faker->randomDigit,
        'stock' => $faker->randomDigit,
        'stockthreshold' => $faker->randomDigit,
    ];
});
