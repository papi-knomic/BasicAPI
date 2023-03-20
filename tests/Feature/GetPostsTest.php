<?php

use App\Models\Post;
use Faker\Factory;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class GetPostsTest extends TestCase
{
    protected $endpoint = 'api/posts';

    protected $user;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->user = $this->createUser();
    }



    public function test_posts_endpoint()
    {
        $this->get($this->endpoint)
            ->assertStatus(self::HTTP_OK );
    }

    public function test_it_returns_a_success_response_with_post_data()
    {
        Post::factory()->count(5)->create();


        // When
        $response = $this->get($this->endpoint);

        // Then
        $response->assertStatus(self::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Posts gotten',
            ]);
    }

    public function test_it_returns_a_success_response_with_wrong_sort_filter()
    {
        Post::factory()->count(5)->create();


        // When
        $response = $this->get($this->endpoint.'?sort_by=fake');

        // Then
        $response->assertStatus(self::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Posts gotten',
            ]);
    }

    public function test_it_returns_a_success_response_with_post_data_for_latest_filter()
    {
        Post::factory()->count(5)->create();


        // When
        $response = $this->get($this->endpoint.'?sort_by=latest');

        // Then
        $response->assertStatus(self::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Posts gotten',
            ]);
    }

    public function test_it_returns_a_success_response_with_post_data_for_popular_filter()
    {
        Post::factory()->count(5)->create();


        // When
        $response = $this->get($this->endpoint.'?sort_by=popular');

        // Then
        $response->assertStatus(self::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Posts gotten',
            ]);
    }

    public function test_it_returns_a_success_response_with_post_data_for_search_filter()
    {
        $post = Post::factory()->create();


        // When
        $response = $this->get($this->endpoint.'?search='. $post->title );

        // Then
        $response->assertStatus(self::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Posts gotten',
            ]);
    }

    public function test_wrong_username_passed_to_get_user_posts()
    {
        $username = Factory::create()->userName();
        $this->get("api/$username/posts" )
            ->assertStatus(self::HTTP_NOT_FOUND);
    }

    public function test_wrong_id_passed_to_get_user_posts()
    {
        $fakeUserId = 9999;
        $this->get("api/$fakeUserId/posts" )
            ->assertStatus(self::HTTP_NOT_FOUND);
    }

    public function test_right_username_passed_to_get_user_posts()
    {
        $post = Post::factory()->raw();
        $this->user->posts()->create($post);
        $username = $this->user->username;

        $this->get("api/$username/posts" )
            ->assertStatus(self::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Posts gotten',
            ]);
    }

    public function test_right_id_passed_to_get_user_posts()
    {
        $post = Post::factory()->raw();
        $this->user->posts()->create($post);
        $id = $this->user->id;

        $this->get("api/$id/posts" )
            ->assertStatus(self::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Posts gotten',
            ]);
    }

    public function test_wrong_sort_filter_passed_to_get_user_posts()
    {
        $post = Post::factory()->raw();
        $this->user->posts()->create($post);
        $id = $this->user->id;

        // When
        $response = $this->get(  "api/$id/posts?sort_by=fake");

        // Then
        $response->assertStatus(self::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Posts gotten',
            ]);
    }
}
