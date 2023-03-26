<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteCommentTest extends TestCase
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
        $this->be( $this->user );
        $response = $this->delete(route('comment.destroy', ['comment' => 9999]));

        $response->assertStatus(self::HTTP_NOT_FOUND);
    }

    public function test_user_not_comment_owner()
    {
        $comment = Comment::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->be( $this->user );
        $response = $this->delete(route('comment.destroy', ['comment' => $comment]));

        $response->assertStatus(self::HTTP_FORBIDDEN);
    }

    public function test_comment_delete_success()
    {
        $this->be( $this->user );
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);
        $response = $this->delete(route('comment.destroy', ['comment' => $comment]));

        $response->assertStatus(self::HTTP_NO_CONTENT);
    }
}
