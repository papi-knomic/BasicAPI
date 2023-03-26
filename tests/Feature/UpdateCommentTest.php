<?php

namespace Tests\Feature;

use App\Models\Comment;
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
        $this->be( $this->user );
        $comment = Comment::factory()->create();
        $response = $this->patch(route('comment.update', ['comment' => $comment]));


        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
    }


    public function test_body_missing()
    {
        $this->be( $this->user );
        $comment = Comment::factory()->create();
        $response = $this->patch(route('comment.update', ['comment' => $comment]));


        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('body');
    }

    public function test_user_not_comment_owner()
    {
        $comment = Comment::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->be( $this->user );
        $body = Factory::create()->text();
        $response = $this->patch(route('comment.update', ['comment' => $comment]), ['body' => $body]);

        $response->assertStatus(self::HTTP_FORBIDDEN);
    }

    public function test_comment_updated_successfully()
    {
        $user = $this->user;

        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $this->be( $user );

        $body = Factory::create()->text;
        $response = $this->patch(route('comment.update', ['comment' => $comment]), ['body' => $body]);

        $response->assertStatus(self::HTTP_OK);
    }
}
