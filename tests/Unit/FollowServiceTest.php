<?php

namespace Tests\Unit\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\FollowService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mockery;
use PHPUnit\Framework\TestCase;

class FollowServiceTest extends TestCase
{
    protected FollowService $followService;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->followService = new FollowService($this->userRepository);
    }

    public function testFollow_WhenFollowerExistsAndUserExistsAndNotAlreadyFollowed_ReturnsTrue()
    {
        // Arrange
        $followId = 1;
        $userId = 2;

        $follower = Mockery::mock(User::class);
        $follower->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($followId);
        $belongsToMany = Mockery::mock(BelongsToMany::class);
        $belongsToMany->shouldReceive('syncWithoutDetaching')
            ->andReturn(Collection::make());
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($userId);
        $user->shouldReceive('getAttribute')
            ->with('following')
            ->andReturn(Collection::make());
        $user->shouldReceive('following')
            ->andReturn($belongsToMany);
        $this->userRepository->shouldReceive('getById')
            ->with($followId)
            ->andReturn($follower)
            ->shouldReceive('getById')
            ->withArgs([$userId, ['following']])
            ->andReturn($user);

        // Act
        $result = $this->followService->follow($followId, $userId);

        // Assert
        $this->assertTrue($result);
    }

    // Add more test methods to cover different scenarios...

    public function testUnfollow_WhenFollowerExistsAndUserExistsAndAlreadyFollowed_ReturnsTrue()
    {
        // Arrange
        $followId = 1;
        $userId = 2;

        $follower = Mockery::mock(User::class);
        $follower->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($followId)
            ->shouldReceive('offsetExists')
            ->andReturn(true)
            ->shouldReceive('offsetGet')
            ->andReturn($followId);
        $belongsToMany = Mockery::mock(BelongsToMany::class);
        $belongsToMany->shouldReceive('detach')
            ->andReturn(1);
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($userId)
            ->shouldReceive('getAttribute')
            ->with('following')
            ->andReturn(Collection::make([$follower]))
            ->shouldReceive('following')
            ->andReturn($belongsToMany);
        $this->userRepository->shouldReceive('getById')
            ->with($followId)
            ->andReturn($follower)
            ->shouldReceive('getById')
            ->withArgs([$userId, ['following']])
            ->andReturn($user);

        // Act
        $result = $this->followService->unfollow($followId, $userId);

        // Assert
        $this->assertTrue($result);
    }

    // Add more test methods to cover different scenarios...

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}