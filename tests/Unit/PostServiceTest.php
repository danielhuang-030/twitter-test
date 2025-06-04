<?php

namespace Tests\Unit\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\Post;
use App\Params\PostParam;
use App\Repositories\PostRepository;
use App\Services\PostService;
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


    public function testEditSuccessfully(): void
    {
        $postId = 1;
        $userId = 1;
        $requestData = ['title' => 'update'];

        $post = new Post();
        $post->user_id = $userId;

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($post);

        $updatedPost = new Post();
        $this->postRepository->shouldReceive('update')
            ->once()
            ->with($requestData, $postId)
            ->andReturn($updatedPost);

        $result = $this->postService->edit($requestData, $postId, $userId);
        $this->assertSame($updatedPost, $result);
    }

    public function testEditThrowsExceptionWhenNotAuthor(): void
    {
        $postId = 1;
        $userId = 1;
        $requestData = ['title' => 'update'];

        $post = new Post();
        $post->user_id = 2;

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($post);
        $this->postRepository->shouldNotReceive('update');

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_AUTHOR->message());

        $this->postService->edit($requestData, $postId, $userId);
    }

    public function testDeleteSuccessfully(): void
    {
        $postId = 1;
        $userId = 1;

        $post = new Post();
        $post->user_id = $userId;

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($post);
        $this->postRepository->shouldReceive('delete')
            ->once()
            ->with($postId);

        $result = $this->postService->del($postId, $userId);
        $this->assertTrue($result);
    }

    public function testDeleteThrowsExceptionWhenNotAuthor(): void
    {
        $postId = 1;
        $userId = 1;

        $post = new Post();
        $post->user_id = 2;

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($post);
        $this->postRepository->shouldNotReceive('delete');

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_NOT_AUTHOR->message());

        $this->postService->del($postId, $userId);
    }

    public function testLikeSuccessfully(): void
    {
        $postId = 1;
        $userId = 2;

        $post = \Mockery::mock(Post::class)->makePartial();
        $post->user_id = 1;
        $relation = \Mockery::mock();
        $relation->shouldReceive('syncWithoutDetaching')
            ->once()
            ->with((array) $userId);
        $post->shouldReceive('likedUsers')
            ->once()
            ->andReturn($relation);

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($post);

        $this->assertTrue($this->postService->like($postId, $userId));
    }

    public function testLikeThrowsExceptionWhenAuthor(): void
    {
        $postId = 1;
        $userId = 1;

        $post = new Post();
        $post->user_id = $userId;

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($post);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_AUTHOR_CAN_NOT_LIKE->message());

        $this->postService->like($postId, $userId);
    }

    public function testDislikeSuccessfully(): void
    {
        $postId = 1;
        $userId = 2;

        $post = \Mockery::mock(Post::class)->makePartial();
        $post->user_id = 1;
        $relation = \Mockery::mock();
        $relation->shouldReceive('detach')
            ->once()
            ->with((array) $userId);
        $post->shouldReceive('likedUsers')
            ->once()
            ->andReturn($relation);

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($post);

        $this->assertTrue($this->postService->dislike($postId, $userId));
    }

    public function testDislikeThrowsExceptionWhenAuthor(): void
    {
        $postId = 1;
        $userId = 1;

        $post = new Post();
        $post->user_id = $userId;

        $this->postRepository->shouldReceive('getById')
            ->once()
            ->with($postId, ['user'])
            ->andReturn($post);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage(ApiResponseCode::ERROR_POST_AUTHOR_CAN_NOT_LIKE->message());

        $this->postService->dislike($postId, $userId);
    }
}
