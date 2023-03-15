<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory as Faker;


class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    private string $register = 'api/register';


    /**
     * Tests if the register endpoint exists
     *
     * @return void
     */
    public function test_register_endpoint()
    {
        $response = $this->post( $this->register );

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY );
    }

    public function test_firstname_missing() {
        $this->post( $this->register )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonStructure(['errors'=>['first_name']]);
    }

    public function test_lastname_missing() {
        $this->post( $this->register )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonStructure(['errors'=>['last_name']]);
    }


    public function test_username_missing() {
        $this->post( $this->register )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonStructure(['errors'=>['username']]);
    }

    public function test_username_is_not_unique() {
        $user = User::factory()->create();

        $this->post( $this->register, ['username' => $user->username ] )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonStructure(['errors'=>['username']]);
    }

    public function test_email_missing() {
        $this->post( $this->register )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonStructure(['errors'=>['email']]);
    }

    public function test_email_is_not_unique() {
        $user = User::factory()->create();

        $this->post( $this->register, ['email' => $user->email ] )
            ->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY )
            ->assertJsonStructure(['errors'=>['email']]);
    }

    public function test_password_missing()
    {
        $response = $this->post($this->register, [
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertJsonStructure(['errors'=>['password']]);
    }

    public function test_password_length_less_than_8()
    {
        $response = $this->post($this->register, [
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);

        $response->assertJsonStructure(['errors'=>['password']]);
    }

    public function test_password_not_confirmed()
    {
        $response = $this->post($this->register, [
            'password' => 'password123',
            'password_confirmation' => '',
        ]);

        $response->assertJsonStructure(['errors'=>['password']]);
    }

    public function test_bio_missing()
    {
        $response = $this->post($this->register);

        $response->assertJsonStructure(['errors'=>['bio']]);
    }

    public function test_bio_must_be_at_least_fifty_characters()
    {
        $faker = Faker::create();
        $text = $faker->text( 40 );
        $response = $this->post($this->register, [
                'bio' => $text
            ]
        );

        $response->assertJsonStructure(['errors'=>['bio']]);
    }

    public function test_bio_must_be_at_most_five_hundred_characters()
    {
        $faker = Faker::create();
        $text = $faker->text( 700 );
        $specificLengthString = str_pad($text, 700, "afjfjf");
        $response = $this->post($this->register, [
                'bio' => $specificLengthString
            ]
        );

        $response->assertJsonStructure(['errors'=>['bio']]);
    }

    public function test_registered_user()
    {
        $user = User::factory()->raw();
        $user['password_confirmation'] = $user['password'];

        $this->post($this->register, $user)
            ->assertStatus(self::HTTP_CREATED )
            ->assertJsonStructure(['data' => ['first_name']]);
    }


}
