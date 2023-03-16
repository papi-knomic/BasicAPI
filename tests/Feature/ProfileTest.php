<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    protected $endpoint = 'api/account/profile';


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_profile_update_endpoint()
    {
        $response = $this->post($this->endpoint);
        $response->assertStatus(self::HTTP_INTERNAL_ERROR );
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_profile_endpoint()
    {
        $response = $this->get($this->endpoint);
        $response->assertStatus(self::HTTP_INTERNAL_ERROR );
    }

    public function test_profile_update_success()
    {
        $user =  User::factory()->create();
        $this->be( $user );

        $userData = User::factory()->raw();

        $response = $this->post($this->endpoint, $userData);
        $response->assertStatus(self::HTTP_CREATED );
    }

    public function test_get_profile_success()
    {
        $user =  User::factory()->create();
        $this->be( $user );

        $userData = User::factory()->raw();

        $response = $this->get($this->endpoint, $userData);
        $response->assertStatus(self::HTTP_OK )
            ->assertJsonStructure(['token']);
    }
}
