<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    public function setUp() :void
    {
    
        parent::setUp();
    
        $this->faker = \Faker\Factory::create();
        $this->user = factory(\App\Models\User::class)->create(['password' => Hash::make('abcdabcd')]);

        $this->password = 'abcdabcd';
    }

    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $response = $this->json('POST', '/api/user/create', [
            'email' => $this->faker->email,
            'password' => $this->password,
            'handle' => str_replace(" ", "", $this->faker->name)
        ]);

        $response->assertStatus(200);

    }

    public function testLoginUser()
    {
        $response = $this->withHeaders([
            'X-API-SECRET' => 'password',
        ])->json('POST', 'api/auth', [
            'email' => $this->user->email,
            'password' => $this->password,
        ]);

        $response->assertStatus(200);
    }

    public function testSongsApiCall()
    {
        $response = $this->withHeaders([
            'X-API-SECRET' => 'password',
            'X-API-TOKEN' => $this->user->token
        ])->json('GET', 'api/songs');

        $response->assertStatus(200);
    }

    public function testSaveSong()
    {

        $song = [
            'user_id' => 1,
            'title' => 'testing',
            'description' =>'description',
            'duration' => 2788,
            'is_flagged' => false,
            'is_public' => true,
            'song_videos' => [
                'volume' => 100,
                'order_id' => 1,
                'video' =>[
                    'title' => 'the video',
                    'description' => 'the video description'
                ]
            ]
        ];

        /*
        $song = factory(\App\Models\Song::class)->create([
            'user_id' => $this->user->id,
        ]);
        */
       
        $response = $this->withHeaders([
            'X-API-SECRET' => 'password',
            'X-API-TOKEN' => $this->user->token
        ])->json('POST', 'api/songs', $song);

        $response->assertStatus(200);
    


    }

    public function testCreateVideo()
    {

        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withHeaders([
            'X-API-SECRET' => 'password',
            'X-API-TOKEN' => $this->user->token
        ])->json('POST', 'api/videos', [
            'video' => $file,
        ]);

        // Assert the file was stored...
        Storage::disk('avatars')->assertExists($file->hashName());
    }
}
