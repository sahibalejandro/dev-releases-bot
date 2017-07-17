<?php

$factory->define(App\Subscription::class, function (Faker\Generator $faker) {
    return [
        'repository_id' => function () {
            return factory('App\Repository')->create()->id;
        },
        'chat_id' => $faker->randomDigit,
    ];
});