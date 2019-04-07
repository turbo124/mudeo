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

    public function testSaveSong()
    {

        $song = [
            'id' => 1,
            'user_id' => 1,
            'title' => 'testing',
            'description' =>'description',
            'duration' => 2788,
            'is_flagged' => false,
            'is_public' => true,
            'song_videos' => [
                    [
                    'volume' => 100,
                    'order_id' => 1,
                    'video' =>[
                        'id' => 1,
                        'title' => 'the video',
                        'description' => 'the video description'
                    ],
                ]
            ]
        ];

        $this->assertTrue(true);


    }

}
