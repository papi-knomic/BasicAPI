<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
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
    public function test_store_comment_endpoint()
    {
        $post = Post::factory()->create();
        $response = $this->post( route('comment.store', ['post' => $post->id]) );

        $response->assertStatus(Response::HTTP_FOUND );
    }


    public function test_can_not_add_comment_if_wrong_post_passed()
    {

        $response = $this
            ->actingAs($this->user)
            ->post(route('comment.store', ['post'=> '10000' ]));

        $response->assertStatus(Response::HTTP_NOT_FOUND );
    }

    public function test_can_not_add_comment_if_body_missing()
    {
        $post = Post::factory()->create();
        $response = $this
            ->actingAs($this->user)
            ->post(route('comment.store', ['post' => $post->id]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('body');
    }

    public function test_can_not_add_comment_if_body_less_than_10()
    {
        $post = Post::factory()->create();
        $body = Factory::create()->text(5);
        $response = $this
            ->actingAs($this->user)
            ->post(route('comment.store', ['post' => $post->id]), ['body' => $body]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('body');
    }

    public function test_can_not_add_comment_if_wrong_parent_id_passed()
    {
        $post = Post::factory()->create();
        $body = Factory::create()->sentence();

        $response = $this
            ->actingAs($this->user)
            ->post(route('comment.store', ['post' => $post->id]), [
            'body' => $body,
            'parent_id' => 1000
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('parent_id');
    }

    public function test_comment_added_success()
    {
        $post = Post::factory()->create();
        $body = "This is a valid comment.";

        $response = $this
            ->actingAs($this->user)
            ->post(route('comment.store', ['post' => $post->id]), [
            'body' => $body,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_comment_added_to_parent_success()
    {
        $post = Post::factory()->create();
        $parentComment = Comment::factory()->create(['post_id'=>$post->id]);
        $comment = Comment::factory()->raw(['post_id' => $post->id]);
        $comment['parent_id'] = $parentComment->id;

        $response = $this
            ->actingAs($this->user)
            ->post(route('comment.store', ['post' => $post->id]), $comment);

        $response->assertStatus(Response::HTTP_CREATED);
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


        $response = $this
            ->actingAs($this->user)
            ->post(route('comment.store', ['post' => $post->id]), $newComment);

        $response->assertStatus(Response::HTTP_BAD_REQUEST );
    }
}
