<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddCommentTest extends TestCase
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
        $response = $this->post( route('comment.store', ['post' => $post->id]) );

        $response->assertStatus(self::HTTP_REDIRECT );
    }


    public function test_wrong_post_passed()
    {
        $this->be( $this->user );

        $response = $this->post(route('comment.store', ['post'=> '10000' ]));

        $response->assertStatus(self::HTTP_NOT_FOUND );
    }

    public function test_comment_body_missing()
    {
        $this->be( $this->user );

        $post = Post::factory()->create();
        $response = $this->post(route('comment.store', ['post' => $post->id]));

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('body');
    }

    public function test_comment_body_less_than_10()
    {
        $this->be( $this->user );

        $post = Post::factory()->create();
        $body = Factory::create()->text(5);
        $response = $this->post(route('comment.store', ['post' => $post->id]), ['body' => $body]);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('body');
    }

    public function test_wrong_parent_id_passed()
    {
        $this->be( $this->user );

        $post = Post::factory()->create();
        $body = Factory::create()->sentence();

        $response = $this->post(route('comment.store', ['post' => $post->id]), [
            'body' => $body,
            'parent_id' => 1000
        ]);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('parent_id');
    }

    public function test_comment_added_success()
    {
        $this->be( $this->user );

        $post = Post::factory()->create();
        $body = Factory::create()->sentence();

        $response = $this->post(route('comment.store', ['post' => $post->id]), [
            'body' => $body,
        ]);

        $response->assertStatus(self::HTTP_CREATED);
    }

    public function test_comment_added_to_parent_success()
    {
        $this->be( $this->user );

        $post = Post::factory()->create();
        $parentComment = Comment::factory()->create(['post_id'=>$post->id]);
        $comment = Comment::factory()->raw(['post_id' => $post->id]);
        $comment['parent_id'] = $parentComment->id;

        $response = $this->post(route('comment.store', ['post' => $post->id]), $comment);

        $response->assertStatus(self::HTTP_CREATED);
    }

    public function test_comment_added_to_child_error()
    {
        $this->be( $this->user );

        $post = Post::factory()->create();
        $parentComment = Comment::factory()->create();
        $comment = Comment::factory()->raw();
        $comment['parent_id'] = $parentComment->id;
        $childComment = Comment::create($comment);
        $newComment = Comment::factory()->raw();
        $newComment['parent_id'] = $childComment->id;


        $response = $this->post(route('comment.store', ['post' => $post->id]), $newComment);

        $response->assertStatus(self::HTTP_BAD_REQUEST );
    }


    public function test_comment_added_to_parent_with_different_post_id()
    {
        // create an authenticated user
        $this->be($this->user);

        // create a post and a parent comment
        $post = Post::factory()->create();
        $parentComment = Comment::factory()->create(['post_id' => $post->id]);

        // attempt to create a child comment with a different post ID
        $childComment = Comment::factory()->raw(['parent_id' => $parentComment->id]);
        $response = $this->post(route('comment.store', ['post' => Post::factory()->create()->id]), $childComment);

        // assert that the response status code is HTTP_UNPROCESSABLE_ENTITY
        $response->assertStatus( self::HTTP_BAD_REQUEST);
    }
}
