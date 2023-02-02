<?php

namespace Tests\Feature;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;

    private $name;
    private $email;
    private $password;

    /**
     * set up.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->name = $this->faker->name();
        $this->email = $this->faker->email();
        $this->password = 'password';
    }

    /**
     * signup.
     *
     * @return void
     */
    public function testSignup()
    {
        $response = $this->postJson('/api/v1/signup', [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'code' => '000000',
                'message' => 'User created successfully!',
                'data' => [],
            ]);
    }

    /**
     * login.
     *
     * @return void
     */
    public function testLogin()
    {
        $user = User::factory()->create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ]);
    }
}
