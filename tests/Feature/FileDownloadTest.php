<?php

namespace Tests\Feature;


use FFMpeg\FFMpeg;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\Video\X264;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileDownloadTest extends TestCase
{

    public function setUp() :void
    {
    
        parent::setUp();
    
    }
 

 	public function testFileDownload()
 	{
 		$client = new Client();

		$working_dir = sha1(time()) . '/';

        File::makeDirectory(storage_path($working_dir));
        
        $url = 'http://storage.googleapis.com/mudeo-staging/videos/VolejRejNm/g8PuMo1Tn6B6UR8l3kZ50mT5g8KnGo09jPi9lHwR.mp4';

	    $client->request('GET', $url, ['sink' => storage_path($working_dir) . basename($url)]);

		File::deleteDirectory(storage_path($working_dir));

       	$this->assertFalse(false);

 	}
}
