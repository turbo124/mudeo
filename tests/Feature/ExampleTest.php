<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    public function setUp() :void
    {
    
        parent::setUp();
    
        $this->faker = \Faker\Factory::create();

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
            'password' => '123123123',
            'handle' => $this->faker->userName
        ]);

        $this->user = $response;

        $response->assertStatus(200);
    }

    public function createVideo()
    {
        
    }
}
