<?php

use App\Models\Song;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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

$factory->define(Song::class, function (Faker $faker) {
    return [
        'title' => $faker->text(30),
        'description' => $faker->text(100),
        'url' => $faker->url,
        'duration' => $faker->numberBetween(1,200),
        'like_count' => $faker->randomDigit,
        'is_flagged' => $faker->boolean,
        'is_public' => $faker->boolean
    ];
});
