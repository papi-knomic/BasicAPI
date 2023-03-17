<?php

namespace Tests\Feature;

use Tests\TestCase;

class PostLikeTest extends TestCase
{

    protected $endpoint = 'api/post/like';


    protected $user;


    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->user = $this->createUser();
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_post_like_endpoint()
    {
        $response = $this->post($this->endpoint);

        $response->assertStatus(self::HTTP_INTERNAL_ERROR );
    }

    public function test_post_id_missing()
    {
        $this->be( $this->user );

        $this->post($this->endpoint)
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('post_id');
    }

    public function test_post_id_does_not_exist()
    {
        $this->be( $this->user );
        $fake_post_id = 999;

        $this->post($this->endpoint, ['post_id' => $fake_post_id ] )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('post_id');
    }
}
