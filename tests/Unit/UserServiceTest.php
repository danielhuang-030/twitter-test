<?php

namespace Tests\Unit\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected UserService $userService;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->userService = new UserService($this->userRepository);
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
}