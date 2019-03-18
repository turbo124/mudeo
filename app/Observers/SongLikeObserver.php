<?php

namespace App\Observers;

use App\Models\SongLike;

class SongLikeObserver
{
    /**
     * Handle the SongLike "created" event.
     *
     * @param  \App\SongLike  $song_like
     * @return void
     */
    public function created(SongLike $song_like)
    {
    	$song = $song_like->song;
        $song->increment('count_like')->save();

    }

    /**
     * Handle the SongLike "updated" event.
     *
     * @param  \App\SongLike  $song_like
     * @return void
     */
    public function updated(SongLike $song_like)
    {
        //
    }

    /**
     * Handle the SongLike "deleted" event.
     *
     * @param  \App\SongLike  $song_like
     * @return void
     */
    public function deleted(SongLike $song_like)
    {
        $song = $song_like->song;
        $song->decrement('count_like')->save();
    }

    /**
     * Handle the SongLike "restored" event.
     *
     * @param  \App\SongLike  $song_like
     * @return void
     */
    public function restored(SongLike $song_like)
    {
        //
    }

    /**
     * Handle the SongLike "force deleted" event.
     *
     * @param  \App\SongLike  $song_like
     * @return void
     */
    public function forceDeleted(SongLike $song_like)
    {
        //
    }
}
