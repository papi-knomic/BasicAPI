<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteCommentTest extends TestCase
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
        $response = $this
            ->actingAs($this->user)
            ->delete(route('comment.destroy', ['comment' => 9999]));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_user_not_comment_owner()
    {
        $comment = Comment::factory()->create([
            'user_id' => User::factory()->create()->id,
            'post_id' => Post::factory()->create()->id,

        ]);
        $response = $this
            ->actingAs($this->user)
            ->delete(route('comment.destroy', ['comment' => $comment]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_comment_delete_success()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => Post::factory()->create()->id,

        ]);
        $response = $this
            ->actingAs($this->user)
            ->delete(route('comment.destroy', ['comment' => $comment]));

        $response->assertStatus(Response::HTTP_OK);
    }
}
