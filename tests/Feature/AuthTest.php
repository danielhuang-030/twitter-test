<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Hash;
use Mockery;
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

        $this->authService = Mockery::mock(AuthService::class);
        $this->userService = Mockery::mock(UserService::class);
        $this->app->instance(AuthService::class, $this->authService);
        $this->app->instance(UserService::class, $this->userService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * signup.
     *
     * @return void
     */
    public function testSignup()
    {
        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;

        $this->userService->shouldReceive('create')
            ->once()
            ->andReturn($user);

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
        $user = new class extends User {
            public function token()
            {
                return 'fake-token';
            }
        };
        $user->id = 1;
        $user->name = $this->name;
        $user->email = $this->email;

        $this->authService->shouldReceive('attempt')
            ->once()
            ->andReturn($user);

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
