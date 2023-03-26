<?php

namespace Tests\Integration;

use App\Models\Comment;
use App\Models\Post;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddCommentTest extends TestCase
{
    use RefreshDatabase;

    protected $endpoint = 'api/post';

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    public function test_entity_created_into_database()
    {
        $this->be( $this->user );
        $post = Post::factory()->create();
        $body = Factory::create()->text();

        $result = json_decode( $this->post(route('comment.store', ['post' => $post->id]), [
            'body' => $body,
        ])->getContent() );

        $this->assertDatabaseHas('posts',[ 'id' => $result->data->id ] );
    }
}
