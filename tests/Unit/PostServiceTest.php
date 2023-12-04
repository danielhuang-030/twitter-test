<?php

namespace App\Services;

use App\Enums\ApiResponseCode;
use App\Exceptions\CustomException;
use App\Models\Post;
use App\Models\User;
use App\Params\PostParam;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\TestCase;

class PostServiceTest extends TestCase
{
    protected PostService $postService;
    protected PostRepository $postRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = Mockery::mock(PostRepository::class);
        $this->postService = new PostService($this->postRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();

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
        $postMock = Mockery::mock(Post::class);
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

    // public function testDel(): void
    // {
    //     // Write your test logic for the `del` method here
    // }

    // public function testLike(): void
    // {
    //     // Write your test logic for the `like` method here
    // }

    // public function testDislike(): void
    // {
    //     // Write your test logic for the `dislike` method here
    // }
}