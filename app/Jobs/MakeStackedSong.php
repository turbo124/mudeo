<?php

namespace App\Jobs;

use App\Models\Song;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\Video\X264;
use GuzzleHttp\Client;
use Hashids\Hashids;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MakeStackedSong implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $song;
    protected $working_dir;

    public function __construct(Song $song)
    {
        $this->song = $song;
        $this->working_dir = sha1(time()) . '/';
    }

    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        // Don't include YouTube videos in the stacked video
        $tracks = $this->song->local_song_videos;

        File::makeDirectory(storage_path($this->working_dir), 0755, true, true);

        $client = new Client();
        foreach ($tracks as $track) {
            $client->request('GET', $track->video->url, ['sink' => $this->getUrl($track->video)]);
        }

        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 0, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ]);

        $video = false;
        $count = 0;
        $filterVideo = '[0:v]';
        $filterAudio = '[0:a]';

        foreach ($tracks as $track) {
            if ($video) {
                if ($track->delay < 0 && false) {
                    $video->addFilter(new SimpleFilter(['-ss', $delay / 1000 * -1]));
                }

                $video->addFilter(new SimpleFilter(['-i', $this->getUrl($track->video)]));

                if ($track->delay > 0 && false) {

                } else {
                    $filterVideo .= "[{$count}:v]";
                    $filterAudio .= "[{$count}:a]";
                }

            } else {
                $video = $this->ffmpeg->open($this->getUrl($track->video));
            }

            $count++;
        }

        $filter = "{$filterVideo}hstack=inputs=2[v];{$filterAudio}amix=inputs=2[a]";

        $video->addFilter(new SimpleFilter(['-filter_complex', $filter]))
            ->addFilter(new SimpleFilter(['-map', '[v]']))
            ->addFilter(new SimpleFilter(['-map', '[a]']))
            ->addFilter(new SimpleFilter(['-ac', '2']))
            ->filters();

        $format = new X264();

        $format->setPasses(1)
            ->setAudioCodec('aac')
            ->setKiloBitrate(1200)
            ->setAudioChannels(2)
            ->setAudioKiloBitrate(126)
            ->setAdditionalParameters(['-vprofile', 'baseline', '-level', 3.0, '-movflags', '+faststart']);

        $filepath = storage_path($this->working_dir) . sha1(time()) . '.mp4';
        $video->save($format, $filepath);

        $hashids = new Hashids('', 10);
        $remote_storage_file_name = 'videos/' . $hashids->encode( $this->song->user_id ) . '/' . $hashids->encode( $this->song->id ) . '.mp4';
        $file = file_get_contents($filepath);

        $disk = Storage::disk('gcs');
        $disk->put($remote_storage_file_name, $file);

        File::deleteDirectory(storage_path($this->working_dir));
    }

    private function getUrl($video)
    {
        return storage_path($this->working_dir) . basename($video->url);
    }
}
