<?php

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    protected $userRepository;
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->mock(UserRepository::class);
        $this->authService = new AuthService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
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

        $userMock = Mockery::mock(User::class);

        // 模擬 setAttribute 方法
        $userMock->shouldReceive('setAttribute')
            ->with('password', 'hashed_password')
            ->once();

        $userMock->password = 'hashed_password';

        $this->userRepository
            ->shouldReceive('getByEmail')
            ->once()
            ->with($credentials['email'])
            ->andReturn($userMock);

        $userMock->shouldReceive('getAttribute')
            ->with('password')
            ->andReturn('hashed_password');

        Hash::shouldReceive('check')
            ->once()
            ->with($credentials['password'], 'hashed_password')
            ->andReturn(true);

        // 模擬 token 物件
        $tokenMock = Mockery::mock();
        $tokenMock->shouldReceive('save')->once();

        // 模擬 createToken 返回的物件
        $tokenResult = (object) [
            'token' => $tokenMock,
            'accessToken' => 'test-access-token',
        ];

        $userMock->shouldReceive('createToken')
            ->with(AuthService::TOKEN_KEY)
            ->andReturn($tokenResult);

        $userMock->shouldReceive('withAccessToken')
            ->with('test-access-token')
            ->once();

        // 模擬 auth
        Auth::shouldReceive('setUser')
            ->with($userMock)
            ->once();

        $result = $this->authService->attempt($credentials);

        $this->assertSame($userMock, $result);
    }
}
