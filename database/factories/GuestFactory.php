<?php

use Faker\Generator as Faker;

$factory->define(App\Guest::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'status' => $faker->shuffleArray(['undecided', 'confirmed', 'unable', ])
    ];
});
