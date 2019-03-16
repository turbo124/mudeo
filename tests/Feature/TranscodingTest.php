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

    public function testMe()
    {
      $this->assertTrue(true);
    }

    public function Transcode()
    {


            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/bin/ffprobe' 
            ]);

            $mp4_file[] = '/Users/davidbomba/Desktop/1.mp4';
            $mp4_file[] = '/Users/davidbomba/Desktop/1.mp4';
            $mp4_file[] = '/Users/davidbomba/Desktop/1.mp4';
            $mp4_file[] = '/Users/davidbomba/Desktop/1.mp4';
            $mp4_file[] = '/Users/davidbomba/Desktop/1.mp4';

            for($x=0; $x<count($mp4_file); $x++) {

              $video = $ffmpeg->open($mp4_file[$x]);
              $video->addFilter(new SimpleFilter(['-filter:a', 'volume=0.5'])) //level/100 = volume
              ->filters();
              
              
      		    $format = new X264();
  			      $format->setAudioCodec("aac");

              $video->save($format, $x . '.mp4'); //need to create a temp working dir so we don't overwrite other files that are crunching

            }

            $x = count($mp4_file);

            if($x >= 2){

              $filepath = $this->inAndOut($mp4_file[0], $mp4_file[1], 1);

              unset($mp4_file[0]);
              unset($mp4_file[1]);

              if(array_key_exists(2, $mp4_file)) {

              $filepath = $this->inAndOut($filepath, $mp4_file[2], 1);

              unset($mp4_file[2]);

              }

              if(array_key_exists(3, $mp4_file)) {

              $filepath = $this->inAndOut($filepath, $mp4_file[3], 1);

              unset($mp4_file[3]);

              }    

              if(array_key_exists(4, $mp4_file)) {

              $filepath = $this->inAndOut($filepath, $mp4_file[4], 1);

              unset($mp4_file[4]);

              }            

              return $filepath;

            }
            else
              return '0.mp4';


            //upload this final file to google storage and save it as the reference song 

            $this->assertTrue(true);

            //$vid_object = $vid->frame(TimeCode::fromSeconds(1))->save('', false, true);
    }

    public function inAndOut($parentVideo, $childVideo, $userHash)
    {
        $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/bin/ffprobe' 
            ]);


        $video = $ffmpeg->open($parentVideo);

        $video->addFilter(new SimpleFilter(['-i', $childVideo]))
              ->addFilter(new SimpleFilter(['-filter_complex', 'hstack']))
              ->filters();

        $format = new X264();
        $format->setAudioCodec("aac");

        $filepath = sha1(time()) . '.mp4';
        $video->save($format, $userHash . '/' . $filepath);

        return $filepath;
            
    }
 /*
 

 ffmpeg \
  -i 1.mp4 \
  -i 2.mp4 \
  -i 1.mp4 \
  -filter_complex '[0:v]pad=iw*2:ih[int];[int][1:v]overlay=W/2:0[vid]' \
  -map [vid] \
  -c:v libx264 \
  -crf 23 \
  -preset veryfast \
  3.mp4



$video = $this->ffmpeg->open($imageSource);
$video->addFilter(new SimpleFilter(['-i ', $audioSource]));


  */   
}
