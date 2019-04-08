<?php

namespace App\Observers;

use App\Models\UserFollower;

class UserFollowerObserver
{adsfadsfadsfasdf
    public function created(UserFollower $follower)
    {
    	$user = $follower->user_following;

        $user->increment('follower_count')->save();

    }

    /**
     * Handle the SongLike "updated" event.
     *
     * @param  \App\SongLike  $song_like
     * @return void
     */
    public function updated(UserFollower $follower)
    {
        //
    }

    /**
     * Handle the SongLike "deleted" event.
     *
     * @param  \App\SongLike  $song_like
     * @return void
     */
    public function deleted(UserFollower $follower)
    {
    	$user = $follower->user_following;
        
        $user->decrement('follower_count')->save();
    }
}
