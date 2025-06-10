<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
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

        // 移除 passport:client 命令執行
        // 改為模擬 Passport 行為

        $this->name = $this->faker->name();
        $this->email = $this->faker->email();
        $this->password = $this->faker->password(6, 12);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * signup.
     *
     * @return void
     */
    public function testSignup()
    {
        $userRepositoryMock = \Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn(new User());

        $this->app->instance(UserRepository::class, $userRepositoryMock);

        $userService = new UserService($userRepositoryMock);
        $result = $userService->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $this->assertInstanceOf(User::class, $result);
    }

    /**
     * login.
     *
     * @return void
     */
    public function testLogin()
    {
        $userMock = \Mockery::mock(User::class);
        $userMock->shouldReceive('getAuthIdentifier')
            ->andReturn(1);
        $userMock->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(999);
        $userMock->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn($this->name);
        $userMock->shouldReceive('getAttribute')
            ->with('email')
            ->andReturn($this->email);
        // 模擬 token 物件
        $tokenMock = \Mockery::mock();
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

        $userRepositoryMock = \Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('getByEmail')
            ->with($this->email)
            ->andReturn($userMock);

        $userMock->shouldReceive('getAttribute')
            ->with('password')
            ->andReturn('hashed_password');

        Hash::shouldReceive('check')
            ->with($this->password, 'hashed_password')
            ->andReturn(true);

        // 模擬 auth 而不使用 alias
        \Illuminate\Support\Facades\Auth::shouldReceive('setUser')
            ->with($userMock)
            ->once();

        $authService = new AuthService($userRepositoryMock);
        $user = $authService->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $this->assertInstanceOf(User::class, $user);
    }
}
