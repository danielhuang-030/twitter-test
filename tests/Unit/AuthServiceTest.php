<?php

use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Exceptions\CustomException;
use App\Enums\ApiResponseCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }

    public function testAttempt_ThrowsCustomExceptionIfUserDoesNotExist()
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

    public function testAttempt_ThrowsCustomExceptionIfPasswordIsIncorrect()
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

    public function testAttempt_SetsUserTokenAndAuthenticatesUser()
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