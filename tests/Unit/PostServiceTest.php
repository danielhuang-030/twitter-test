<?php

namespace App\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\Post;
use App\Params\PostParam;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\TestCase;

class PostServiceTest extends TestCase
{
    protected PostService $postService;
    protected PostRepository $postRepository;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = \Mockery::mock(PostRepository::class);
        $this->userRepository = \Mockery::mock(UserRepository::class);
        $this->postService = new PostService($this->postRepository, $this->userRepository);
    }

    protected function tearDown(): void
    {
        \Mockery::close();

        parent::tearDown();
    }

    public function testGetPosts(): void
    {
        $param = $this->createMock(PostParam::class);
        $paginator = $this->createMock(LengthAwarePaginator::class);

        $this->postRepository->shouldReceive('getPaginatorByParam')
            ->once()
            ->with($param)
            ->andReturn($paginator);

        $result = $this->postService->getPosts($param);
        $this->assertSame($paginator, $result);
    }

    public function testAdd(): void
    {
        // Test case 1: When the post is successfully created
        $userId = 1;
        $requestData = [
            'title' => 'Test Post',
            'content' => 'Lorem ipsum dolor sit amet',
            'user_id' => $userId,
        ];
        $postMock = \Mockery::mock(Post::class);
        $postMock->shouldReceive('getAttribute')
            ->with('title')
            ->andReturn($requestData['title'])
            ->shouldReceive('getAttribute')
            ->with('content')
            ->andReturn($requestData['content'])
            ->shouldReceive('getAttribute')
            ->with('user_id')
            ->andReturn($requestData['user_id'])
            ->shouldReceive('load')
            ->andReturn($postMock);

        // Expect the create method to be called with the requestData
        $this->postRepository->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andReturn($postMock);

        $post = $this->postService->add($requestData, $userId);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($requestData['title'], $post->title);
        $this->assertEquals($requestData['content'], $post->content);
        $this->assertEquals($userId, $post->user_id);

        // Test case 2: When the post creation fails and throws an exception
        $userId = 2;
        $requestData = [
            'title' => 'Invalid Post',
            'content' => 'Invalid content',
            'user_id' => $userId,
        ];

        // Expect the create method to be called with the requestData
        $this->postRepository->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andReturn(null);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_ADD->message());

        $this->postService->add($requestData, $userId);
    }

    public function testFindExistingPost(): void
    {
        // Arrange
        $id = 1;
        $expectedPost = new Post();

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($id, ['user'])
            ->andReturn($expectedPost);

        // Act
        $result = $this->postService->find($id);

        // Assert
        $this->assertSame($expectedPost, $result);
    }

    public function testFindNonExistingPost(): void
    {
        // Arrange
        $id = 2;

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($id, ['user'])
            ->andReturn(null);

        // Assert
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_EXIST->message());

        // Act
        $this->postService->find($id);
    }

    // public function testEdit(): void
    // {
    //     // Write your test logic for the `edit` method here
    // }

    public function testEditPost(): void
    {
        $userId = 1;
        $postId = 1;
        $requestData = ['title' => 'Updated Title', 'content' => 'Updated Content'];

        // Scene 1: Successfully edit
        $postMock = \Mockery::mock(Post::class)->makePartial();
        $postMock->user_id = $userId; // Author is the current user
        $postMock->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);


        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMock);

        $updatedPostMock = \Mockery::mock(Post::class)->makePartial();
        $this->postRepository->shouldReceive('update')
            ->once()
            ->with($requestData, $postId)
            ->andReturn($updatedPostMock);

        $result = $this->postService->edit($requestData, $postId, $userId);
        $this->assertSame($updatedPostMock, $result);

        // Scene 2: Non-author attempts to edit
        $anotherUserId = 2;
        $postMockOtherAuthor = \Mockery::mock(Post::class)->makePartial();
        $postMockOtherAuthor->user_id = $anotherUserId; // Author is not the current user
        $postMockOtherAuthor->shouldReceive('getAttribute')->with('user_id')->andReturn($anotherUserId);


        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMockOtherAuthor);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_AUTHOR->message());
        $this->postService->edit($requestData, $postId, $userId); // Current user $userId tries to edit post of $anotherUserId

        // Scene 3: Edit non-existing post
        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn(null);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_EXIST->message());
        $this->postService->edit($requestData, $postId, $userId);

        // Scene 4: Update fails (Repository returns null)
        $postMockForFailedUpdate = \Mockery::mock(Post::class)->makePartial();
        $postMockForFailedUpdate->user_id = $userId; // Author is the current user
        $postMockForFailedUpdate->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMockForFailedUpdate);

        $this->postRepository->shouldReceive('update')
            ->once()
            ->with($requestData, $postId)
            ->andReturn(null);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_EDIT->message());
        $this->postService->edit($requestData, $postId, $userId);
    }

    // public function testDel(): void
    // {
    //     // Write your test logic for the `del` method here
    // }

    public function testDeletePost(): void
    {
        $userId = 1;
        $postId = 1;

        // Scene 1: Successfully delete
        $postMock = \Mockery::mock(Post::class)->makePartial();
        $postMock->user_id = $userId; // Author is the current user
        $postMock->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMock);

        $this->postRepository->shouldReceive('delete')
            ->once()
            ->with($postId)
            ->andReturn(true); // Assuming delete returns true on success

        $result = $this->postService->del($postId, $userId);
        $this->assertTrue($result);

        // Scene 2: Non-author attempts to delete
        $anotherUserId = 2;
        $postMockOtherAuthor = \Mockery::mock(Post::class)->makePartial();
        $postMockOtherAuthor->user_id = $anotherUserId; // Author is not the current user
        $postMockOtherAuthor->shouldReceive('getAttribute')->with('user_id')->andReturn($anotherUserId);

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMockOtherAuthor);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_AUTHOR->message());
        $this->postService->del($postId, $userId); // Current user $userId tries to delete post of $anotherUserId

        // Scene 3: Delete non-existing post
        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn(null);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_EXIST->message());
        $this->postService->del($postId, $userId);
    }

    // public function testLike(): void
    // {
    //     // Write your test logic for the `like` method here
    // }

    public function testLikePost(): void
    {
        $userId = 1; // User performing the like action
        $authorId = 2; // Author of the post
        $postId = 1;

        // Scene 1: Successfully like a post
        $postMock = \Mockery::mock(Post::class)->makePartial();
        $postMock->user_id = $authorId; // Post author is not the current user
        $postMock->shouldReceive('getAttribute')->with('user_id')->andReturn($authorId);
        $postMock->shouldReceive('likedUsers->syncWithoutDetaching')->once()->with((array)$userId);


        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMock);

        $result = $this->postService->like($postId, $userId);
        $this->assertTrue($result);

        // Scene 2: Author attempts to like their own post
        $postMockOwn = \Mockery::mock(Post::class)->makePartial();
        $postMockOwn->user_id = $userId; // Post author IS the current user
        $postMockOwn->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);


        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMockOwn);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_AUTHOR_CAN_NOT_LIKE->message());
        $this->postService->like($postId, $userId);

        // Scene 3: Like a non-existing post
        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn(null);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_EXIST->message());
        $this->postService->like($postId, $userId);
    }

    // public function testDislike(): void
    // {
    //     // Write your test logic for the `dislike` method here
    // }

    public function testDislikePost(): void
    {
        $userId = 1; // User performing the dislike action
        $authorId = 2; // Author of the post
        $postId = 1;

        // Scene 1: Successfully dislike a post
        $postMock = \Mockery::mock(Post::class)->makePartial();
        $postMock->user_id = $authorId; // Post author is not the current user
        $postMock->shouldReceive('getAttribute')->with('user_id')->andReturn($authorId);
        $postMock->shouldReceive('likedUsers->detach')->once()->with((array)$userId);

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMock);

        $result = $this->postService->dislike($postId, $userId);
        $this->assertTrue($result);

        // Scene 2: Author attempts to dislike their own post
        $postMockOwn = \Mockery::mock(Post::class)->makePartial();
        $postMockOwn->user_id = $userId; // Post author IS the current user
        $postMockOwn->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($postMockOwn);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_AUTHOR_CAN_NOT_LIKE->message());
        $this->postService->dislike($postId, $userId);

        // Scene 3: Dislike a non-existing post
        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn(null);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_EXIST->message());
        $this->postService->dislike($postId, $userId);
    }

    public function testGetUserLikedPostIds(): void
    {
        $userId = 1;
        $postIds = [1, 2, 3];

        // Scene 1: Returns liked post IDs
        $expectedPostIds = [1, 3];
        $post1 = \Mockery::mock(Post::class);
        $post1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $post3 = \Mockery::mock(Post::class);
        $post3->shouldReceive('getAttribute')->with('id')->andReturn(3);

        $likedPostsCollection = collect([$post1, $post3]);

        $this->postRepository->shouldReceive('getUserLikedPostsByUserIdAndPostIds')
            ->once()
            ->with($userId, $postIds)
            ->andReturn($likedPostsCollection);

        $result = $this->postService->getUserLikedPostIds($userId, $postIds);
        $this->assertEquals($expectedPostIds, $result);

        // Scene 2: Empty post IDs input
        $this->postRepository->shouldReceive('getUserLikedPostsByUserIdAndPostIds')
            ->once()
            ->with($userId, [])
            ->andReturn(collect([])); // Return an empty collection

        $resultEmptyInput = $this->postService->getUserLikedPostIds($userId, []);
        $this->assertEquals([], $resultEmptyInput);

        // Scene 3: No liked posts found (empty result set)
        $this->postRepository->shouldReceive('getUserLikedPostsByUserIdAndPostIds')
            ->once()
            ->with($userId, $postIds)
            ->andReturn(collect([])); // Return an empty collection for non-empty input

        $resultEmptySet = $this->postService->getUserLikedPostIds($userId, $postIds);
        $this->assertEquals([], $resultEmptySet);
    }

    public function testGetFollowedUserIds(): void
    {
        $userId = 1;
        $authorIds = [10, 20, 30];

        // Scene 1: Returns followed user IDs
        $expectedUserIds = [10, 30];
        // Mock User models (or just objects with an 'id' property if that's all pluck needs)
        $user10 = \Mockery::mock(\App\Models\User::class);
        $user10->shouldReceive('getAttribute')->with('id')->andReturn(10);
        $user30 = \Mockery::mock(\App\Models\User::class);
        $user30->shouldReceive('getAttribute')->with('id')->andReturn(30);

        $followedUsersCollection = collect([$user10, $user30]);

        $this->userRepository->shouldReceive('getUserFollowedAuthorsByUserIdAndAuthorIds')
            ->once()
            ->with($userId, $authorIds)
            ->andReturn($followedUsersCollection);

        $result = $this->postService->getFollowedUserIds($userId, $authorIds);
        $this->assertEquals($expectedUserIds, $result);

        // Scene 2: Empty author IDs input
        $this->userRepository->shouldReceive('getUserFollowedAuthorsByUserIdAndAuthorIds')
            ->once()
            ->with($userId, [])
            ->andReturn(collect([])); // Return an empty collection

        $resultEmptyInput = $this->postService->getFollowedUserIds($userId, []);
        $this->assertEquals([], $resultEmptyInput);

        // Scene 3: No followed users found (empty result set)
        $this->userRepository->shouldReceive('getUserFollowedAuthorsByUserIdAndAuthorIds')
            ->once()
            ->with($userId, $authorIds)
            ->andReturn(collect([])); // Return an empty collection for non-empty input

        $resultEmptySet = $this->postService->getFollowedUserIds($userId, $authorIds);
        $this->assertEquals([], $resultEmptySet);
    }
}
