<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        "title"=>$faker->text(20),
        "content"=>$faker->text(250),
        "created_at"=>now(),
        "updated_at"=>now()
    ];
});
