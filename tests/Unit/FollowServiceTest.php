<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\FollowService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\TestCase;

class FollowServiceTest extends TestCase
{
    protected FollowService $followService;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = \Mockery::mock(UserRepository::class);
        $this->followService = new FollowService($this->userRepository);
    }

    // Add more test methods to cover different scenarios...

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    public function testFollowWhenFollowerExistsAndUserExistsAndNotAlreadyFollowedReturnsTrue()
    {
        // Arrange
        $followId = 1;
        $userId = 2;

        $follower = \Mockery::mock(User::class);
        $follower->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($followId);
        $belongsToMany = \Mockery::mock(BelongsToMany::class);
        $belongsToMany->shouldReceive('syncWithoutDetaching')
            ->andReturn(Collection::make());
        $user = \Mockery::mock(User::class);
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

    public function testFollowThrowsExceptionWhenFollowIdDoesNotExist()
    {
        $followId = 1;
        $userId = 2;

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($followId)
            ->andReturnNull(); // Simulate followId not found

        $this->expectException(\App\Exceptions\CustomException::class);
        $this->expectExceptionMessage(\App\Enums\ApiResponseCode::ERROR_USER_NOT_EXIST->message());

        $this->followService->follow($followId, $userId);
    }

    public function testFollowThrowsExceptionWhenUserIdDoesNotExist()
    {
        $followId = 1;
        $userId = 2;

        $followerMock = \Mockery::mock(User::class); // User to be followed

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($followId)
            ->andReturn($followerMock); // followId exists

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId, ['following']) // Correctly check for userId with relations
            ->andReturnNull(); // Simulate userId not found

        $this->expectException(\App\Exceptions\CustomException::class);
        $this->expectExceptionMessage(\App\Enums\ApiResponseCode::ERROR_USER_NOT_EXIST->message());

        $this->followService->follow($followId, $userId);
    }

    public function testFollowThrowsExceptionWhenFollowingSelf()
    {
        $userId = 1; // followId and userId are the same

        $userMock = \Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn($userId);

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId) // For followId
            ->andReturn($userMock);

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId, ['following']) // For userId
            ->andReturn($userMock);

        $this->expectException(\App\Exceptions\CustomException::class);
        $this->expectExceptionMessage(\App\Enums\ApiResponseCode::ERROR_FOLLOW_SELF->message());

        $this->followService->follow($userId, $userId);
    }

    public function testFollowThrowsExceptionWhenAlreadyFollowing()
    {
        $followId = 1;
        $userId = 2;

        $followerMock = \Mockery::mock(User::class); // User to be followed
        $followerMock->shouldReceive('getAttribute')->with('id')->andReturn($followId);

        $userMock = \Mockery::mock(User::class); // Current user
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn($userId);

        // Mock the 'following' relationship and its methods
        $followingCollectionMock = \Mockery::mock(\Illuminate\Database\Eloquent\Collection::class);
        $followingCollectionMock->shouldReceive('pluck->contains')->with($followId)->andReturn(true);
        $userMock->shouldReceive('getAttribute')->with('following')->andReturn($followingCollectionMock);


        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($followId)
            ->andReturn($followerMock);

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId, ['following'])
            ->andReturn($userMock);

        $this->expectException(\App\Exceptions\CustomException::class);
        $this->expectExceptionMessage(\App\Enums\ApiResponseCode::ERROR_FOLLOW_HAVE_FOLLOWED->message());

        $this->followService->follow($followId, $userId);
    }

    // Add more test methods to cover different scenarios...

    public function testUnfollowThrowsExceptionWhenFollowIdDoesNotExist()
    {
        $followId = 1;
        $userId = 2;

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($followId)
            ->andReturnNull(); // Simulate followId not found

        $this->expectException(\App\Exceptions\CustomException::class);
        $this->expectExceptionMessage(\App\Enums\ApiResponseCode::ERROR_USER_NOT_EXIST->message());

        $this->followService->unfollow($followId, $userId);
    }

    public function testUnfollowThrowsExceptionWhenUserIdDoesNotExist()
    {
        $followId = 1;
        $userId = 2;

        $followerMock = \Mockery::mock(User::class); // User to be unfollowed

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($followId)
            ->andReturn($followerMock); // followId exists

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId, ['following']) // Correctly check for userId with relations
            ->andReturnNull(); // Simulate userId not found

        $this->expectException(\App\Exceptions\CustomException::class);
        $this->expectExceptionMessage(\App\Enums\ApiResponseCode::ERROR_USER_NOT_EXIST->message());

        $this->followService->unfollow($followId, $userId);
    }

    public function testUnfollowThrowsExceptionWhenUnfollowingSelf()
    {
        $userId = 1; // followId and userId are the same

        $userMock = \Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn($userId);

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId) // For followId
            ->andReturn($userMock);

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId, ['following']) // For userId
            ->andReturn($userMock);

        $this->expectException(\App\Exceptions\CustomException::class);
        $this->expectExceptionMessage(\App\Enums\ApiResponseCode::ERROR_UNFOLLOW_SELF->message());

        $this->followService->unfollow($userId, $userId);
    }

    public function testUnfollowThrowsExceptionWhenNotFollowing()
    {
        $followId = 1;
        $userId = 2;

        $followerMock = \Mockery::mock(User::class); // User to be unfollowed
        $followerMock->shouldReceive('getAttribute')->with('id')->andReturn($followId);


        $userMock = \Mockery::mock(User::class); // Current user
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn($userId);

        // Mock the 'following' relationship to simulate not following
        $followingCollectionMock = \Mockery::mock(\Illuminate\Database\Eloquent\Collection::class);
        $followingCollectionMock->shouldReceive('pluck->contains')->with($followId)->andReturn(false);
        $userMock->shouldReceive('getAttribute')->with('following')->andReturn($followingCollectionMock);


        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($followId)
            ->andReturn($followerMock);

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId, ['following'])
            ->andReturn($userMock);

        $this->expectException(\App\Exceptions\CustomException::class);
        $this->expectExceptionMessage(\App\Enums\ApiResponseCode::ERROR_UNFOLLOW_NOT_FOLLOWED->message());

        $this->followService->unfollow($followId, $userId);
    }

    public function testUnfollowWhenFollowerExistsAndUserExistsAndAlreadyFollowedReturnsTrue()
    {
        // Arrange
        $followId = 1;
        $userId = 2;

        $follower = \Mockery::mock(User::class);
        $follower->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($followId)
            ->shouldReceive('offsetExists')
            ->andReturn(true)
            ->shouldReceive('offsetGet')
            ->andReturn($followId);
        $belongsToMany = \Mockery::mock(BelongsToMany::class);
        $belongsToMany->shouldReceive('detach')
            ->andReturn(1);
        $user = \Mockery::mock(User::class);
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
}
