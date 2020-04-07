<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Song;
use kornrunner\Blurhash\Blurhash;

class CalculateBlurhash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mudeo:blurhash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate image blurhash';

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
        $songs = Song::where('blurhash', '=', '')
            ->orderBy('id')->get();

        foreach ($songs as $song) {
            if ($song->youtube_id) {
                $file = $song->youTubeThumbnailUrl();
            } else {
                $file = $song->thumbnail_url;
            }

            if (!$file) {
                continue;
            }

            $this->info('Song: ' . $file);

            try {
                $image = imagecreatefromjpeg($file);
                list($width, $height) = getimagesize($file);

                $pixels = [];
                for ($y = 0; $y < $height; ++$y) {
                    $row = [];
                    for ($x = 0; $x < $width; ++$x) {
                        $rgb = imagecolorat($image, $x, $y);

                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;

                        $row[] = [$r, $g, $b];
                    }
                    $pixels[] = $row;
                }

                $components_x = 4;
                $components_y = 3;

                $song->blurhash = Blurhash::encode($pixels, $components_x, $components_y);
                $song->save();

                $this->info('Hash: ' . $song->blurhash);
            } catch (Exception $e) {

            }

        }
    }
}
