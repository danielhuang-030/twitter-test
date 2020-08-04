<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;

    private $password = 'mypassword';

    public function testAuthSignup()
    {
        $name = $this->faker->name();
        $email = $this->faker->email();

        $response = $this->postJson('/api/signup', [
            'name'                  => $name,
            'email'                 => $email,
            'password'              => $this->password,
            'password_confirmation' => $this->password,
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'message' => 'Successfully created user!',
            ]);
    }
}
