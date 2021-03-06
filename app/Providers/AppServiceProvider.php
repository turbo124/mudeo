<?php

namespace App\Providers;

use App\Models\SongLike;
use App\Models\User;
use App\Models\UserFollower;
use App\Observers\SongLikeObserver;
use App\Observers\UserFollowerObserver;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
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
    
        User::observe(UserObserver::class);
        SongLike::observe(SongLikeObserver::class);
        UserFollower::observe(UserFollowerObserver::class);

        Schema::defaultStringLength(191);

    }
}
