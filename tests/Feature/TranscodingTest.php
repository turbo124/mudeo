<?php

namespace Tests\Feature;


use FFMpeg\FFMpeg;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\Video\X264;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransCodingTest extends TestCase
{

    public function setUp() :void
    {
    
        parent::setUp();
    
    }

    
    public function testTranscode()
    {


            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/bin/ffprobe' 
            ]);

            $mp4_file = '/Users/davidbomba/Desktop/1.mp4';

            $video = $ffmpeg->open($mp4_file);
            
           // $video->addFilter(new SimpleFilter(['-i', '/Users/davidbomba/Desktop/2.mp4']))
            		$video->filters()
            		->custom('-i /Users/davidbomba/Desktop/2.mp4 \ -filter_complex [0:v]pad=iw*2:ih[int];[int][1:v]overlay=W/2:0[vid]')
            		->synchronize();

    		$format = new X264();
			$format->setAudioCodec("libmp3lame");

            $video->save($format, 'doozy.mp4');

            $this->assertTrue(true);

            //$vid_object = $vid->frame(TimeCode::fromSeconds(1))->save('', false, true);
    }

 /*
 

 ffmpeg \
  -i 1.mp4 \
  -i 2.mp4 \
  -i 1.mp4 \
  -filter_complex '[0:v]pad=iw*3:ih[int];[int][1:v]overlay=W/3:0[vid]' \
  -map [vid] \
  -c:v libx264 \
  -crf 23 \
  -preset veryfast \
  3.mp4



$video = $this->ffmpeg->open($imageSource);
$video->addFilter(new SimpleFilter(['-i ', $audioSource]));


  */   
}
