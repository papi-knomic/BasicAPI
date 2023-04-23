<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
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

        $response->assertStatus(Response::HTTP_FOUND );
    }

    public function test_wrong_post_passed()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('post.dislike', ['post'=> '10000' ]));

        $response->assertStatus(Response::HTTP_NOT_FOUND );
    }

    public function test_dislike_post_success()
    {
        $post = Post::factory()->create();

        $response = $this
            ->actingAs($this->user)
            ->post(route('post.dislike', ['post'=> $post->id ]));

        $response->assertStatus(Response::HTTP_OK );
    }

    public function test_dislike_already_disliked_post()
    {
        $post = Post::factory()->create();
        $this->user->dislikes()->attach($post, ['liked' => false, 'disliked' => true]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('post.dislike', ['post'=> $post->id ]));

        $response->assertStatus(Response::HTTP_CONFLICT );
    }

    public function test_dislike_already_liked_post()
    {
        $post = Post::factory()->create();
        $this->user->likes()->attach($post, ['liked' => true, 'disliked' => false ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('post.dislike', ['post'=> $post->id ]));

        $response->assertStatus(Response::HTTP_OK );
    }
}
