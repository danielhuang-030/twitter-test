<?php

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $userRepository;
    private $email;
    private $password;
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->mock(UserRepository::class);
        $this->authService = new AuthService($this->userRepository);

        // init passport
        // Check if passport client already exists to avoid re-creating it
        // This is a simplified check; a more robust check might query the database
        // or use a flag if this setup runs multiple times in a test suite.
        if (!\Laravel\Passport\Client::where('personal_access_client', 1)->exists()) {
            $this->artisan('passport:client', [
                '--personal' => true,
                '--name' => config('app.name'),
                '--redirect_uri' => config('app.url'),
                '--no-interaction' => true,
            ]);
        }

        $this->email = $this->faker->email();
        $this->password = $this->faker->password(6, 12);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testAttemptThrowsCustomExceptionIfUserDoesNotExist()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $this->userRepository
            ->shouldReceive('getByEmail')
            ->once()
            ->with($credentials['email'])
            ->andReturn(null);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_USER_NOT_EXIST->message());

        $this->authService->attempt($credentials);
    }

    public function testAttemptThrowsCustomExceptionIfPasswordIsIncorrect()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User();
        $user->password = Hash::make('wrongpassword');

        $this->userRepository
            ->shouldReceive('getByEmail')
            ->once()
            ->with($credentials['email'])
            ->andReturn($user);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_UNAUTHORIZED->message());

        $this->authService->attempt($credentials);
    }

    public function testAttemptSetsUserTokenAndAuthenticatesUser()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User();
        $user->id = 9999;
        $user->password = Hash::make('password');

        $this->userRepository
            ->shouldReceive('getByEmail')
            ->once()
            ->with($credentials['email'])
            ->andReturn($user);

        Hash::shouldReceive('driver')->andReturnSelf();
        Hash::shouldReceive('check')
            ->once()
            ->with($credentials['password'], $user->password)
            ->andReturn(true);

        $this->actingAs($user);

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->andReturn($user);

        $result = $this->authService->attempt($credentials);

        $this->assertSame($user, $result);
    }

    /**
     * login.
     *
     * @return void
     */
    public function testLogin()
    {
        $userFaker = new User();
        $userFaker->id = 999;
        $userFaker->name = $this->faker->name(); // Use faker for name consistency if needed, or a fixed name
        $userFaker->email = $this->email;
        $userFaker->password = \Hash::make($this->password);

        $this->userRepository->shouldReceive('getByEmail')
            ->once()
            ->with($this->email)
            ->andReturn($userFaker);

        // Resolve AuthService with the mocked UserRepository
        // The existing $this->authService is already instantiated with the mock.
        // No need to resolve it again unless it's a different instance or specific context.

        $postData = [
            'email' => $this->email,
            'password' => $this->password,
        ];
        $result = $this->authService->attempt($postData); // Use the class property $this->authService

        $this->assertEquals($userFaker->name, $result->name);
        $this->assertEquals($this->email, $result->email);
    }
}
