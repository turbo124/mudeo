<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Hashids\Hashids;
use App\Models\User;
use App\Models\Video;
use App\Models\Song;
use Storage;

class TransferFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mudeo:transfer-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer files to Digital Ocean';

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
        $users = User::orderBy('id')->get();
        foreach ($users as $user) {
            if ($user->profile_image_url) {
                $path = false;
                if (strpos($user->profile_image_url, 'googleusercontent') !== false) {
                    $hashids = new Hashids('', 10);
                    $path = '/users/' .  $hashids->encode( $user->id) . '/' . sha1(time()) . '.jpg';
                }
                if ($url = $this->uploadFile($user->profile_image_url, $path)) {
                    $user->profile_image_url = $url;
                    $user->save();
                }
            }
            if ($url = $this->uploadFile($user->header_image_url)) {
                $user->header_image_url = $url;
                $user->save();
            }
        }

        $songs = Song::orderBy('id')->get();
        foreach ($songs as $song) {
            if ($url = $this->uploadFile($song->video_url)) {
                $song->video_url = $url;
                $song->save();
            }
            if ($url = $this->uploadFile($song->thumbnail_url)) {
                $song->thumbnail_url = $url;
                $song->save();
            }
        }

        $videos = Video::orderBy('id')->get();
        foreach ($videos as $video) {
            if ($url = $this->uploadFile($video->url)) {
                $video->url = $url;
                $video->save();
            }
            if ($url = $this->uploadFile($video->thumbnail_url)) {
                $video->thumbnail_url = $url;
                $video->save();
            }
        }
    }

    private function uploadFile($url, $path = false)
    {
        if (! $url) {
            return false;
        }

        $this->info("Handling: $url");

        if (strpos($url, 'ocean') !== false) {
            $this->info("Skipping - DO is in URL");
            return false;
        } else if (strpos($url, 'google') === false) {
            $this->info("Skipping - google not in URL");
            return false;
        }

        if (! $path) {
            $path = str_replace('http://storage.googleapis.com/mudeo', '', $url);
            $path = str_replace('https://storage.googleapis.com/mudeo', '', $path);
        }

        if (Storage::disk('do_spaces')->has($path)) {
            $this->info("Skipping - already uploaded to DO");
            return false;
        }

        $file = @file_get_contents($url);

        if (! $file) {
            $this->info("Error - file does not exist");
        }

        if (! Storage::disk('do_spaces')->put($path, $file)) {
            $this->info("Error - failed to upload file");
            return false;
        }

        $path = "https://mudeo.nyc3.digitaloceanspaces.com{$path}";
        $this->info("Uploaded: $path");

        return $path;
    }
}
