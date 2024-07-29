<?php

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepository;
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->mock(UserRepository::class);
        $this->authService = new AuthService($this->userRepository);

        // init passport
        $this->artisan('passport:client', [
            '--personal' => true,
            '--name' => config('app.name'),
            '--redirect_uri' => config('app.url'),
            '--no-interaction' => true,
        ]);
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
}
