<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory as Faker;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    private string $endpoint = 'api/login';


    public function test_login_endpoint()
    {
        $this->post( $this->endpoint )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY );
    }

    public function test_email_missing()
    {
        $this->post( $this->endpoint )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('email');
    }

    public function test_email_does_not_exist_in_db()
    {
        $faker = Faker::create();
        $email = $faker->email();
        $login = [
            'email' => $email,
            'password' => 'password'
        ];

        $this->post( $this->endpoint, $login )
            ->assertStatus( self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('email');
    }

    public function test_password_missing()
    {
        $this->post( $this->endpoint )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonValidationErrors('password');
    }

    public function test_wrong_password()
    {
        $user = User::factory()->create();

        $login = [
            'email' => $user->email,
            'password' => 'pass'
        ];

        $this->post( $this->endpoint, $login )
            ->assertStatus(self::HTTP_BAD_REQUEST);
    }

    public function test_login_success()
    {
        $user = User::factory()->create();

        $login = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $this->post( $this->endpoint, $login )
            ->assertStatus(self::HTTP_OK)
            ->assertJsonStructure(['token']);
    }


}
