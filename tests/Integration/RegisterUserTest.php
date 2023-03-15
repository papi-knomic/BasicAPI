<?php

namespace Tests\Integration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    private string $register = 'api/register';


    public function test_entity_created_into_database()
    {
        $user = User::factory()->raw();
        $user['password_confirmation'] = $user['password'];

        $result = json_decode( $this->post($this->register, $user )->getContent() );

        $this->assertDatabaseHas('users',[ 'id' => $result->data->id ] );
    }


}
