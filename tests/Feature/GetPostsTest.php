<?php

use App\Models\Post;
use Tests\TestCase;

class GetPostsTest extends TestCase
{
    protected $endpoint = 'api/posts';


    public function test_posts_endpoint()
    {
        $this->get($this->endpoint)
            ->assertStatus(self::HTTP_OK );
    }

//    public function test_it_returns_a_success_response_with_post_data()
//    {
//        // Given
//        Post::factory()->count(5)->create();
//
//        // When
//        $response = $this->get($this->endpoint);
//
//        // Then
//        $response->assertStatus(self::HTTP_OK)
//            ->assertJson(['message' => 'Posts gotten'])
//            ->assertJsonStructure([
//                'data' => [
//                    '*' => [
//                        'id',
//                        'title',
//                        'body',
//                        'created_at',
//                        'updated_at'
//                    ]
//                ]
//            ]);
//    }
}
