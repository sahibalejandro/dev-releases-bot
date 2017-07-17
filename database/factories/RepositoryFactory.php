<?php

$factory->define(App\Repository::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'last_tag_name' => $faker->word,
    ];
});
