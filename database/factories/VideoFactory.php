<?php

use App\Models\Video;
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

$factory->define(Video::class, function (Faker $faker) {
    return [
        'url' => $faker->url,
        'thumbnail_url' => $faker->url,
        'is_flagged' => $faker->boolean,
        'is_public' => $faker->boolean
    ];
});
