<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Hashids\Hashids;
use App\Models\Song;
use App\Jobs\UploadSongToTwitter;
use App\Jobs\UploadSongToYouTube;
use Carbon\Carbon;

class Publish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mudeo:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish video to Twitter and YouTube';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Publish to YouTube...');
        $song = Song::whereNull('youtube_id')
            ->where('is_approved', '=', true)
            ->where('is_public', '=', true)
            ->where('approved_at', '<', Carbon::now()->subDays(1))
            ->orderBy('id')
            ->first();

        if ($song) {
            $this->info('Song: ' . $song->title);
            UploadSongToYouTube::dispatch($song);
        } else {
            $this->info('No songs found');
        }

        $this->info('Publish to Twitter...');
        $song = Song::whereNull('twitter_id')
            ->where('is_approved', '=', true)
            ->where('is_public', '=', true)
            ->where('approved_at', '<', Carbon::now()->subDays(1))
            ->orderBy('id')
            ->first();

        if ($song) {
            $this->info('Song: ' . $song->title);
            UploadSongToTwitter::dispatch($song);
        } else {
            $this->info('No songs found');
        }
    }
}
