<?php

use Faker\Generator as Faker;

$factory->define(App\Event::class, function (Faker $faker) {
    return [
        'user_id' => $faker->unique(),
        'name' => $faker->domainName,
    ];
});
