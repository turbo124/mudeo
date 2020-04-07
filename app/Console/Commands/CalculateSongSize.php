<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;

class CalculateSongSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mudeo:song-size';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate song video simensions';

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
        $this->info('Starting...');

        $songs = Song::where('height', '=', 0)
            ->where('youtube_id', '!=', '')
            ->orderBy('id')
            ->get();

        foreach ($songs as $song) {
            $this->info('## Song: ' . $song->id);

            try {
                $image = @imagecreatefromjpeg($song->youTubeThumbnailUrl());

                if ($image) {
                    $image = imagecropauto($image, IMG_CROP_SIDES);

                    $song->width = imagesx($image);
                    $song->height = imagesy($image);
                    $song->save();                    
                }

            } catch (Exception $e) {
                // do nothing
            }
        }

        $this->info('Done');
    }
}
