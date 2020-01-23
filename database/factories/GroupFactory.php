<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'uid' => $faker->ean8,
        'name' => $faker->jobTitle,
        'desc' => $faker->sentence,
        'company_id' => factory(\App\Company::class)->create()->id,
    ];
});
