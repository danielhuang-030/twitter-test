<?php

namespace Tests\Unit\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserServiceTest extends TestCase
{
    use WithFaker;

    protected UserService $userService;
    protected UserRepository $userRepository;

    protected UserRepository $userRepository;
    private $name;
    private $email;
    private $password;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->userService = new UserService($this->userRepository);

        $this->name = $this->faker->name();
        $this->email = $this->faker->email();
        $this->password = $this->faker->password(6, 12);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testCreate(): void
    {
        // Prepare the test data and expectations
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $createdUser = new User();
        $this->userRepository->shouldReceive('create')->once()->andReturn($createdUser);

        // Call the method being tested
        $result = $this->userService->create($data);

        // Assert the result
        $this->assertSame($createdUser, $result);
    }

    public function testCreateNullUser(): void
    {
        // Prepare the test data and expectations
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $this->userRepository->shouldReceive('create')->once()->andReturnNull();

        // Assert that the CustomException is thrown
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_USER_ADD->message());

        // Call the method being tested
        $this->userService->create($data);
    }

    public function testGetUser(): void
    {
        // Prepare the test data and expectations
        $userId = 1;
        $user = new User();
        $this->userRepository->shouldReceive('getById')->once()->with($userId)->andReturn($user);

        // Call the method being tested
        $result = $this->userService->getUser($userId);

        // Assert the result
        $this->assertSame($user, $result);
    }

    public function testGetUserNullUser(): void
    {
        // Prepare the test data and expectations
        $userId = 1;
        $this->userRepository->shouldReceive('getById')->once()->with($userId)->andReturnNull();

        // Assert that the CustomException is thrown
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_USER_NOT_EXIST->message());

        // Call the method being tested
        $this->userService->getUser($userId);
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
        $userFaker->password = Hash::make($this->password); // Use Hash facade

        $this->userRepository->shouldReceive('create')
            ->once()
            // ->with($this->data) // This was commented out, ensure it's correct or remove
            ->andReturn($userFaker);

        // $this->app->instance(UserRepository::class, $userRepositoryMock); // Not needed as it's injected
        // $userService = app(UserService::class); // Use $this->userService

        $postData = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
        $result = $this->userService->create($postData);

        $this->assertEquals($this->name, $result->name);
        $this->assertEquals($this->email, $result->email);
    }
}