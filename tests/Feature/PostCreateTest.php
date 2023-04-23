<?php

namespace Tests\Feature;

use App\Models\Post;
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
        parent::setUp();

        $this->user = $this->createUser();
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_post_endpoint()
    {
        $response = $this
            ->actingAs($this->user)
            ->post($this->endpoint);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_title_missing()
    {
        $response = $this
            ->actingAs($this->user)
            ->post($this->endpoint);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('title');
    }


    public function test_title_characters_more_than_255()
    {
        $faker = Faker::create();
        $text = $faker->text( 270 );
        $specificLengthString = str_pad($text, 270, "a1");
        $response = $this
            ->actingAs($this->user)
            ->post($this->endpoint, ['title' => $specificLengthString]);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('title');
    }

    public function test_body_missing()
    {
        $response = $this
            ->actingAs($this->user)
            ->post($this->endpoint);

        $response->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('body');
    }

    public function test_post_create_success()
    {
        $post = Post::factory()->raw();
        $response = $this
            ->actingAs($this->user)
            ->post($this->endpoint, $post);

        $response->assertStatus(self::HTTP_CREATED )
            ->assertJsonStructure(['data' => ['title']]);
    }

    public function test_post_slug_success()
    {
        $post = Post::factory()->raw();
        $firstPost = json_decode($this->actingAs($this->user)->post($this->endpoint, $post )->getContent());
        $slug = $firstPost->data->slug;

        $secondSlug = generatePostSlug( $firstPost->data->title );

        $this->assertFalse( $slug === $secondSlug );
    }

}
