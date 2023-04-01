<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;


    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_get_followers_endpoint()
    {
        $response = $this->get(route('user.followers', ['user' => $this->user->id]));

        $response->assertStatus(self::HTTP_REDIRECT);
    }

    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_get_followings_endpoint()
    {
        $response = $this->get(route('user.following', ['user' => $this->user->id]));

        $response->assertStatus(self::HTTP_REDIRECT);
    }


    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_get_logged_in_user_followers_endpoint()
    {
        $response = $this->get(route('profile.followers'));

        $response->assertStatus(self::HTTP_REDIRECT);
    }


    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_get_logged_in_user_following_endpoint()
    {
        $response = $this->get(route('profile.following'));

        $response->assertStatus(self::HTTP_REDIRECT);
    }


    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_follow_user_endpoint()
    {
        $response = $this->post(route('user.follow', ['user' => $this->user->id]));

        $response->assertStatus(self::HTTP_REDIRECT);
    }


    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_unfollow_user_endpoint()
    {
        $response = $this->post(route('user.unfollow', ['user' => $this->user->id]));

        $response->assertStatus(self::HTTP_REDIRECT);
    }

    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_get_non_existent_user_followers()
    {
        $this->be( $this->user );

        $response = $this->get(route('user.followers', ['user' => 10000]));

        $response->assertStatus(self::HTTP_NOT_FOUND);
    }

    public function test_success_get_logged_in_user_followers()
    {
        $this->be( $this->user );

        $response = $this->get(route('profile.followers',));

        $response->assertStatus(self::HTTP_OK);
    }

    public function test_success_get_logged_in_user_following()
    {
        $this->be( $this->user );

        $response = $this->get(route('profile.following',));

        $response->assertStatus(self::HTTP_OK);
    }
    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_get_non_existent_user_following()
    {
        $this->be( $this->user );

        $response = $this->get(route('user.following', ['user' => 10000]));

        $response->assertStatus(self::HTTP_NOT_FOUND);
    }


    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_success_get_user_followers()
    {
        $this->be( $this->user );
        $user = User::factory()->create();

        $response = $this->get(route('user.followers', ['user' => $user->id]));

        $response->assertStatus(self::HTTP_OK);
    }

    /**
     * A basic endpoint test
     *
     * @return void
     */
    public function test_success_get_user_followings()
    {
        $this->be( $this->user );
        $user = User::factory()->create();

        $response = $this->get(route('user.following', ['user' => $user->id]));

        $response->assertStatus(self::HTTP_OK);
    }

    public function test_user_follows_themself()
    {
        $this->be( $this->user );

        $response = $this->post(route('user.follow', ['user' => $this->user->id ]) );

        $response->assertStatus(self::HTTP_BAD_REQUEST);
    }

    public function test_user_follows_user_already_followed()
    {
        $this->be( $this->user );

        $user = User::factory()->create();
        $user->followers()->attach($this->user->id);

        $response = $this->post(route('user.follow', ['user' => $user->id ]) );

        $response->assertStatus(self::HTTP_BAD_REQUEST);
    }

    public function test_success_follow_user()
    {
        $this->be( $this->user );

        $user = User::factory()->create();

        $response = $this->post(route('user.follow', ['user' => $user->id ]) );

        $response->assertStatus(self::HTTP_CREATED);
    }

    public function test_user_unfollows_themself()
    {
        $this->be( $this->user );

        $response = $this->post(route('user.unfollow', ['user' => $this->user->id ]) );

        $response->assertStatus(self::HTTP_BAD_REQUEST);
    }

    public function test_user_unfollows_user_not_followed()
    {
        $this->be( $this->user );

        $user = User::factory()->create();

        $response = $this->post(route('user.unfollow', ['user' => $user->id ]) );

        $response->assertStatus(self::HTTP_BAD_REQUEST);
    }

    public function test_success_unfollow_user()
    {
        $this->be( $this->user );

        $user = User::factory()->create();
        $user->followers()->attach($this->user->id);

        $response = $this->post(route('user.unfollow', ['user' => $user->id ]) );

        $response->assertStatus(self::HTTP_CREATED);
    }
}
