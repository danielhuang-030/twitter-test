<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
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
        $this->password = $this->faker->password(6, 12);
    }

    /**
     * signup.
     *
     * @return void
     */
    public function testSignup()
    {
        $userFaker = new User();
        $userFaker->name = $this->name;
        $userFaker->email = $this->email;
        $userFaker->password = Hash::make($this->password);
        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('create')
            ->once()
            ->andReturn($userFaker);
        $userService = resolve(UserService::class, [
            'userRepository' => $userRepositoryMock,
        ]);
        $postData = [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ];
        $result = $userService->create($postData);
        $this->assertEquals($this->name, $result->name);
        $this->assertEquals($this->email, $result->email);
    }

    /**
     * login.
     *
     * @return void
     */
    public function testLogin()
    {
        $userFaker = new User();
        $userFaker->name = $this->name;
        $userFaker->email = $this->email;
        $userFaker->password = Hash::make($this->password);
        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('getByEmail')
            ->once()
            ->with($this->email)
            ->andReturn($userFaker);
        $userService = resolve(UserService::class, [
            'userRepository' => $userRepositoryMock,
        ]);
        $postData = [
            'email'    => $this->email,
            'password' => $this->password,
        ];
        $result = $userService->attempt($postData);
        $this->assertEquals($this->name, $result->name);
        $this->assertEquals($this->email, $result->email);
    }
}