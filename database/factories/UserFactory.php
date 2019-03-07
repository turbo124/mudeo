<?php

use App\Models\User;
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

$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'handle' => $faker->userName,
        'profile_image' => $faker->image($dir = null, $width=null, $height=null, 'cats', false),
        'profile_image_url' => $faker->imageUrl($width=null, $height=null, 'cats'),
        'header_image' => $faker->image($dir=null, $width=null, $height=null, 'cats', false),
        'header_image_url' => $faker->imageUrl($width=null, $height=null, 'cats'),
        'is_flagged' => $faker->boolean,
        'token' => str_random(64),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});


