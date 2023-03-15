<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;
use Tests\TestCase;

class PostCreateTest extends TestCase
{
    use RefreshDatabase;

    private string $endpoint = 'api/post';

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_post_endpoint()
    {
        $user =  User::factory()->create();
        $this->be( $user );
        $response = $this->post($this->endpoint);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_title_missing()
    {
        $user =  User::factory()->create();
        $this->be( $user );
        $response = $this->post($this->endpoint);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('title');
    }


    public function test_title_characters_more_than_255()
    {
        $user =  User::factory()->create();
        $this->be( $user );
        $faker = Faker::create();
        $text = $faker->text( 270 );
        $specificLengthString = str_pad($text, 270, "a1");
        $response = $this->post($this->endpoint, ['title' => $specificLengthString]);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('title');
    }

    public function test_body_missing()
    {
        $user =  User::factory()->create();
        $this->be( $user );
        $response = $this->post($this->endpoint);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('body');
    }


}
