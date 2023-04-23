<?php

namespace Tests\Integration;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCreateTest extends TestCase
{

    use RefreshDatabase;

    protected $endpoint = 'api/post';

    protected $user;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->user = $this->createUser();
    }


    public function test_entity_created_into_database()
    {
        $post = Post::factory()->raw();

        $result = json_decode($this->actingAs($this->user)->post($this->endpoint, $post )->getContent());

        $this->assertDatabaseHas('posts',[ 'id' => $result->data->id ] );
    }
}
