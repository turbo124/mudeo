<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
        Relation::morphMap([
            'songs' => '\App\Models\Song',
            'tracks' => '\App\Models\Track',
            'comments' => '\App\Models\Comment',
            'tags' => '\App\Models\Tag',
        ]);
        */
    }
}
