<?php


use App\Models\User;
use Tests\TestCase;
use Faker\Factory as Faker;

class ViewProfileTest extends TestCase
{

    private $endpoint = 'api/profile';

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_profile_endpoint()
    {
        $this->get($this->endpoint)
            ->assertStatus(self::HTTP_NOT_FOUND );
    }

    public function test_user_id_missing()
    {
        $this->get($this->endpoint)
            ->assertStatus(self::HTTP_NOT_FOUND );
    }

    public function test_user_id_not_in_db()
    {
        $user_id = User::factory()->create()->id;
        $id = $user_id + 10;
        $this->get($this->endpoint . "/$id")
            ->assertStatus(self::HTTP_NOT_FOUND );
    }

    public function test_username_missing()
    {
        $this->get($this->endpoint)
            ->assertStatus(self::HTTP_NOT_FOUND );
    }

    public function test_username_not_in_db()
    {
        $faker = Faker::create();
        $username = $faker->unique()->userName;
        $this->get($this->endpoint . "/$username")
            ->assertStatus(self::HTTP_NOT_FOUND );
    }

    public function test_user_found_with_id_success()
    {
        $user_id = User::factory()->create()->id;
        $this->get($this->endpoint . "/$user_id")
            ->assertStatus(200);
    }

    public function test_user_found_with_username_success()
    {
        $username = User::factory()->create()->username;
        $this->get($this->endpoint . "/$username")
            ->assertStatus(200);
    }
}
