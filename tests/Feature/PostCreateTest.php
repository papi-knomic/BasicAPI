<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;
use Tests\TestCase;

class PostCreateTest extends TestCase
{
    use RefreshDatabase;

    protected $endpoint = 'api/post';


    protected $user;


    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->user = $this->createUser();
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_post_endpoint()
    {
        $this->be( $this->user );
        $response = $this->post($this->endpoint);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_title_missing()
    {
        $this->be( $this->user );
        $response = $this->post($this->endpoint);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('title');
    }


    public function test_title_characters_more_than_255()
    {
        $this->be( $this->user );
        $faker = Faker::create();
        $text = $faker->text( 270 );
        $specificLengthString = str_pad($text, 270, "a1");
        $response = $this->post($this->endpoint, ['title' => $specificLengthString]);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('title');
    }

    public function test_body_missing()
    {
        $this->be( $this->user );
        $response = $this->post($this->endpoint);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('body');
    }

    public function test_post_create_success()
    {
        $this->be( $this->user );
        $post = Post::factory()->raw();
        $response = $this->post($this->endpoint, $post);

        $response->assertStatus(self::HTTP_CREATED )
            ->assertJsonStructure(['data' => ['title']]);
    }

    public function test_post_slug_success()
    {
        $this->be( $this->user );
        $post = Post::factory()->raw();
        $firstPost = json_decode( $this->post($this->endpoint, $post )->getContent() );
        $slug = $firstPost->data->slug;



        $secondPost = $this->post( $this->endpoint, $post )
            ->assertStatus(self::HTTP_CREATED )
            ->getContent();
        $secondPost = json_decode( $secondPost );

        $this->assertFalse( $slug === $secondPost->data->slug );
    }

}
