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
        $songs = Song::where('blurhash', '!=', '')->orderBy('id')->get();

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
                $image = @imagecreatefromjpeg($file);

                if (!$image) {
                    continue;
                }

                list($width, $height) = getimagesize($file);

                $pixels = [];
                $histo = [];
                $histo_color = [];
                $n = $width * $height;

                for ($y = 0; $y < $height; ++$y) {
                    $row = [];
                    for ($x = 0; $x < $width; ++$x) {
                        $rgb = imagecolorat($image, $x, $y);

                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;

                        $row[] = [$r, $g, $b];

                        $V = round(($r + $g + $b) / 3);
                        if (!isset($histo[$V])) {
                            $histo[$V] = 0;
                        }
                        $histo[$V] += $V / $n;
                        $histo_color[$V] = $this->rgb2hex([$r,$g,$b]);
                    }
                    $pixels[] = $row;
                }


                $max = 0;
                for ($i=0; $i<255; $i++)
                {
                    if ($histo[$i] > $max)
                    {
                        $max = $histo[$i];
                    }
                }

                $key = array_search ($max, $histo);
                $color = $histo_color[$key];

                $components_x = 4;
                $components_y = 3;

                $song->blurhash = Blurhash::encode($pixels, $components_x, $components_y);
                $song->color = $color;
                $song->save();

                $this->info('Hash: ' . $song->blurhash . ' Color: ' . $col);
            } catch (Exception $e) {
                // do nothing
            }
        }
    }

    private function rgb2hex($rgb) {
        $hex = "#";
        $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        return $hex; // returns the hex value including the number sign (#)
    }

}
