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

    public function testLoginValidationErrors()
    {
        // Test missing email
        $response = $this->postJson('/api/v1/login', [
            'password' => $this->password,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrorFor('email');

        // Test missing password
        $response = $this->postJson('/api/v1/login', [
            'email' => $this->email,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrorFor('password');

        // Test invalid email format
        $response = $this->postJson('/api/v1/login', [
            'email' => 'not-an-email',
            'password' => $this->password,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrorFor('email');
    }

    public function testLoginIncorrectCredentials()
    {
        // Test with non-existent email
        $response = $this->postJson('/api/v1/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);
        // Based on AuthService, this should be ERROR_USER_NOT_EXIST which translates to a specific response
        // The controller returns 401 for ERROR_UNAUTHORIZED, but for ERROR_USER_NOT_EXIST it might be different (e.g. 400 or 404)
        // Let's assume the controller normalizes both to 401 or a specific business error code.
        // From AuthController: if $user is empty (AuthService throws ERROR_USER_NOT_EXIST), it returns 401.
        $response->assertStatus(401) // Or the specific HTTP status code for ERROR_USER_NOT_EXIST if different
            ->assertJson([
                'code' => \App\Enums\ApiResponseCode::ERROR_UNAUTHORIZED->value, // Or ERROR_USER_NOT_EXIST->value
                'message' => 'Unauthorized', // Or appropriate message for user not found
            ]);

        // Create a user to test wrong password
        User::factory()->create([
            'email' => $this->email,
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $this->email,
            'password' => 'wrongpassword',
        ]);
        $response->assertStatus(401)
            ->assertJson([
                'code' => \App\Enums\ApiResponseCode::ERROR_UNAUTHORIZED->value,
                'message' => 'Unauthorized',
            ]);
    }

    public function testLogout()
    {
        // Create a user and login to get a token
        $user = User::factory()->create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token');

        // Call logout with the token
        $logoutResponse = $this->withToken($token)->getJson('/api/v1/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson([
                'code' => \App\Enums\ApiResponseCode::SUCCESS->value,
                'message' => 'Successfully logged out!',
            ]);

        // Attempt to access a protected route with the logged-out token
        // Assuming /api/v1/profile is a protected route.
        // If this route doesn't exist or isn't protected, this assertion might fail.
        $profileResponse = $this->withToken($token)->getJson('/api/v1/profile');
        $profileResponse->assertStatus(401); // Unauthorized
    }

    public function testAccessProtectedProfileWithInvalidToken()
    {
        $invalidToken = 'this.is.an.invalid.jwt.token';
        $response = $this->withToken($invalidToken)->getJson('/api/v1/profile');
        $response->assertStatus(401); // Unauthorized
    }

    public function testAccessProtectedProfileWithoutToken()
    {
        $response = $this->getJson('/api/v1/profile'); // No token provided
        $response->assertStatus(401); // Unauthorized
    }
}
