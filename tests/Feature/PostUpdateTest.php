<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $endpoint = 'api/post';


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
    public function test_update_post_endpoint()
    {
        $this->be($this->user);
        $post = Post::factory()->create();
        $response = $this->post(route('post.update', ['post'=> $post]));

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_wrong_post_passed()
    {
        $this->be($this->user);
        $response = $this->post(route('post.update', ['post'=> 9999]));

        $response->assertStatus(self::HTTP_NOT_FOUND);
    }

    public function test_not_post_owner()
    {
        $post = Post::factory()->create();
        $postData = Post::factory()->raw();

        $this->be(User::factory()->create());
        $response = $this->post(route('post.update', ['post'=> $post]), $postData);

        $response->assertStatus(self::HTTP_FORBIDDEN);
    }

    public function test_post_updated_successfully()
    {
        $this->be( $this->user );
        $post = $this->user->posts()->create(Post::factory()->raw());
        $postData = Post::factory()->raw();

        $response = $this->post(route('post.update', ['post'=> $post]), $postData);

        $response->assertStatus(self::HTTP_CREATED);
    }

}
