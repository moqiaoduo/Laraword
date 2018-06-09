<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        "title"=>$faker->text(20),
        "category"=>array(mt_rand(0,2)),
        "slug"=>str_random(),
        "content"=>$faker->text(250),
        "created_at"=>now(),
        "updated_at"=>now()
    ];
});
