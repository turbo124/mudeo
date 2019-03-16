<?php

namespace Tests\Feature;


use FFMpeg\FFMpeg;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\Video\X264;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationTest extends TestCase
{

    public function setUp() :void
    {
    
        parent::setUp();
    
    }


    public function testModel()
    {
    	$faker = \Faker\Factory::create();

        $user = factory(\App\Models\User::class)->create();

        $songs = factory(\App\Models\Song::class,10)->create([
            'user_id' => $user->id,
        ])->each(function ($song) use ($user){

            $videos = factory(\App\Models\Video::class,3)->create([
                'user_id' => $user->id,
            ]);

                $song->videos()->sync($videos);
            
        });

        $song = $songs->first();

        $song_video = $song->song_videos->first();

        $video = $song_video->video;

        $song_relate = $song_video->song;

        $this->assertEquals($song_video->song_id, $song->id);

        $this->assertEquals($song_video->video_id, $video->id);

        $this->assertEquals($song_relate->id, $song->id);
    }
}
