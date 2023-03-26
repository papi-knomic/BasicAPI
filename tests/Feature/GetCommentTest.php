<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetCommentTest extends TestCase
{
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
    public function test_get_single_comment_endpoint()
    {
        $comment = Comment::factory()->create();
        $response = $this->get(route('comment.show', ['comment' => $comment]));

        $response->assertStatus(self::HTTP_REDIRECT);
    }

    public function test_get_single_comment_success()
    {
        $this->be($this->user);
        $comment = Comment::factory()->create();
        $response = $this->get(route('comment.show', ['comment' => $comment]));

        $response->assertStatus(self::HTTP_OK);
    }

    public function test_get_post_comments_endpoint()
    {
        $post = Post::factory()->create();
        $response = $this->get(route('comment.index', ['post' => $post]));

        $response->assertStatus(self::HTTP_REDIRECT);
    }


    public function test_get_post_comments_success()
    {
        $this->be($this->user);
        $post = Post::factory()->create();
        Comment::factory()->count(5)->create(['post_id' => $post->id]);
        $response = $this->get(route('comment.index', ['post' => $post]));


        $response->assertStatus(self::HTTP_OK);
    }
}
