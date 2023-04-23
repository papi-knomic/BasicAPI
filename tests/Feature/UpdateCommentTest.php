<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateCommentTest extends TestCase
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
    public function test_endpoint()
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['post_id'=>$post->id, 'user_id' => $user->id]);
        $response = $this
            ->actingAs($this->user)
            ->patch(route('comment.update', ['comment' => $comment]));


        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
    }


    public function test_body_missing()
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['post_id'=>$post->id, 'user_id' => $user->id]);
        $response = $this
            ->actingAs($this->user)
            ->patch(route('comment.update', ['comment' => $comment]));


        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('body');
    }

    public function test_user_not_comment_owner()
    {
        $comment = Comment::factory()->create(['user_id' => User::factory()->create()->id]);
        $body = Factory::create()->text();
        $response = $this
            ->actingAs($this->user)
            ->patch(route('comment.update', ['comment' => $comment]), ['body' => $body]);

        $response->assertStatus(self::HTTP_FORBIDDEN);
    }

    public function test_comment_updated_successfully()
    {
        $user = $this->user;
        $post = Post::factory()->create();

        $comment = Comment::factory()->create(['post_id'=>$post->id, 'user_id' => $user->id]);

        $body = Factory::create()->text;
        $response = $this
            ->actingAs($this->user)
            ->patch(route('comment.update', ['comment' => $comment]), ['body' => $body]);

        $response->assertStatus(self::HTTP_OK);
    }
}
