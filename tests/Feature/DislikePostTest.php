<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DislikePostTest extends TestCase
{
    use RefreshDatabase;

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
    public function test_endpoint()
    {
        $post = Post::factory()->create();
        $response = $this->post(route('post.dislike', ['post'=> $post->id]));

        $response->assertStatus(self::HTTP_REDIRECT );
    }

    public function test_wrong_post_passed()
    {
        $this->be( $this->user );

        $response = $this->post(route('post.dislike', ['post'=> '10000' ]));

        $response->assertStatus(self::HTTP_NOT_FOUND );
    }

    public function test_dislike_post_success()
    {
        $this->be( $this->user );
        $post = Post::factory()->create();

        $response = $this->post(route('post.dislike', ['post'=> $post->id ]));

        $response->assertStatus(self::HTTP_OK );
    }

    public function test_dislike_already_disliked_post()
    {
        $this->be( $this->user );
        $post = Post::factory()->create();
        $this->user->dislikes()->attach($post, ['liked' => false, 'disliked' => true]);

        $response = $this->post(route('post.dislike', ['post'=> $post->id ]));

        $response->assertStatus(self::HTTP_CONFLICT );
    }

    public function test_dislike_already_liked_post()
    {
        $this->be( $this->user );
        $post = Post::factory()->create();
        $this->user->likes()->attach($post, ['liked' => true, 'disliked' => false ]);

        $response = $this->post(route('post.dislike', ['post'=> $post->id ]));

        $response->assertStatus(self::HTTP_OK );
    }
}
