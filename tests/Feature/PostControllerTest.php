<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\PostLike;
use App\Enums\ApiResponseCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    // Test methods will be added here

    public function testIndexPosts(): void
    {
        // Scenario 1: Successfully fetching post list (public)
        Post::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'content',
                            'user_id',
                            'created_at',
                            'updated_at',
                            'author' => [
                                'id',
                                'name',
                                'email',
                            ],
                            'is_liked', // Will be false for public or users not liking
                        ]
                    ],
                    'links',
                    'meta',
                ],
            ])
            ->assertJsonCount(5, 'data.data');

        // Scenario 2: Fetching list with liked and followed status (when user is logged in)
        $mainUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $postByOtherUser = Post::factory()->create(['user_id' => $otherUser->id]);

        // Main user likes the post by other user
        PostLike::factory()->create([
            'user_id' => $mainUser->id,
            'post_id' => $postByOtherUser->id,
            'liked' => PostLike::LIKED_LIKE,
        ]);

        // Main user follows other user (assuming UserFollow model and relationship exists)
        // If UserFollow model and its factory are not available, this part needs adjustment
        // For now, we'll assume a direct way to simulate following if UserFollow factory isn't set up.
        // This might involve directly creating a UserFollow record or mocking the relationship.
        // Let's assume a 'followers' relationship on User model for simplicity of this test.
        // This part might need adjustment based on actual FollowService/Repository implementation.
        // For the purpose of the test, we can directly check the 'is_followed' attribute if the controller sets it.
        // The actual following mechanism is tested in FollowServiceTest.
        // Here, we focus on the controller's output.

        Passport::actingAs($mainUser);
        $responseWithAuth = $this->getJson('/api/v1/posts');
        $responseWithAuth->assertStatus(200);

        $responseWithAuth->assertJsonPath('data.data.0.id', $postByOtherUser->id); // Assuming it's the first post due to ordering or count=1
        $responseWithAuth->assertJsonPath('data.data.0.is_liked', true);
        // The 'author.is_followed' depends on how PostResource and UserResource are structured.
        // Assuming PostResource includes 'author' which is a UserResource,
        // and UserResource has an 'is_followed' attribute accessor or appends it.
        // This assertion might need to be more specific based on the actual resource structure.
        // For example, if posts are ordered by creation date, the last created post will be first.
        // It's better to find the specific post in the response.

        $foundPost = null;
        foreach ($responseWithAuth->json('data.data') as $postData) {
            if ($postData['id'] === $postByOtherUser->id) {
                $foundPost = $postData;
                break;
            }
        }
        $this->assertNotNull($foundPost, "Post by other user not found in response.");
        $this->assertTrue($foundPost['is_liked']);
        // Assuming UserResource has an 'is_followed' attribute when the user is authenticated.
        // This relies on the PostResource correctly embedding an Author (UserResource)
        // that has an 'is_followed' attribute, which is set based on the authenticated user.
        if (isset($foundPost['author']['is_followed'])) { // Check if the attribute exists
             $this->assertTrue($foundPost['author']['is_followed'], "Author is_followed should be true but it's not set or false.");
        } else {
            // This else block is a fallback or indicator that 'is_followed' might not be set up as expected.
            // It could be that the UserResource doesn't include 'is_followed' or it's not being set correctly.
            // For now, we'll make this an optional check or log a warning.
            // In a real scenario, this would be a failing test if 'is_followed' is a requirement.
            // $this->markTestSkipped("Author 'is_followed' attribute not found in the response. Verify UserResource and PostResource structure.");
            // For now, let's assume it should be there. The controller should load this.
            // If the API doesn't directly support `author.is_followed` in the post listing for `$mainUser`
            // viewing `$otherUser`'s post, this assertion will fail and the API/Resource needs adjustment.
            // We will assume it's present for now.
            $this->assertTrue($foundPost['author']['is_followed'] ?? false, "Author is_followed attribute missing or false.");
        }
    }

    public function testStorePost(): void
    {
        // Scenario 1: Successfully creating a post
        $postData = ['content' => $this->faker->paragraph];
        $response = $this->postJson('/api/v1/posts', $postData);

        $response->assertStatus(201) // Assuming 201 Created for successful resource creation
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'content',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'author', // Assuming PostResource includes author
                ]
            ])
            ->assertJsonPath('data.content', $postData['content'])
            ->assertJsonPath('data.user_id', $this->user->id)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value);


        $this->assertDatabaseHas('posts', [
            'content' => $postData['content'],
            'user_id' => $this->user->id,
        ]);

        // Scenario 2: Validation error when creating a post with empty content
        $invalidPostData = ['content' => ''];
        $responseInvalid = $this->postJson('/api/v1/posts', $invalidPostData);

        $responseInvalid->assertStatus(422) // Unprocessable Entity for validation errors
            ->assertJsonValidationErrorFor('content');
    }

    public function testShowPost(): void
    {
        // Scenario 1: Successfully viewing a post
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'content',
                    'user_id',
                    // Add other expected fields based on PostResource
                    'author',
                    'is_liked',
                ]
            ])
            ->assertJsonPath('data.id', $post->id)
            ->assertJsonPath('data.content', $post->content)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value);

        // Scenario 2: Viewing a non-existent post
        $nonExistentPostId = 99999;
        $responseNotFound = $this->getJson("/api/v1/posts/{$nonExistentPostId}");

        // The PostController@show uses findOrFail which results in 404 if not found
        // If the service layer handles it and throws CustomException with ERROR_POST_NOT_EXIST,
        // the BaseController's exception handler would convert it.
        // Assuming the controller or service throws an exception that results in ApiResponseCode::ERROR_POST_NOT_EXIST,
        // which should be translated to a 404 or 400 by the exception handler.
        // PostController's show method uses PostService->find which throws CustomException(ERROR_POST_NOT_EXIST)
        // This exception should be handled by BaseController's render method.
        // The default for CustomException is 400 if not specified.
        $responseNotFound->assertStatus(400) // Or 404 if findOrFail is used directly in controller or handler mapped to 404
            ->assertJson([
                'code' => ApiResponseCode::ERROR_POST_NOT_EXIST->value,
                'message' => ApiResponseCode::ERROR_POST_NOT_EXIST->message(),
            ]);
    }

    public function testUpdatePost(): void
    {
        // Scenario 1: Successfully updating own post
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $updateData = ['content' => 'Updated content by owner'];

        $response = $this->putJson("/api/v1/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.content', $updateData['content'])
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'content' => $updateData['content']]);

        // Scenario 2: Non-author attempts to update post
        $anotherUser = User::factory()->create();
        $postByAnotherUser = Post::factory()->create(['user_id' => $anotherUser->id]);
        $updateDataAttempt = ['content' => 'Attempt to update others post'];

        $responseNotAuthor = $this->putJson("/api/v1/posts/{$postByAnotherUser->id}", $updateDataAttempt);

        // PostController's update method uses PostService->edit which throws CustomException(ERROR_POST_NOT_AUTHOR)
        // This should be handled by BaseController's render method.
        $responseNotAuthor->assertStatus(400) // Or 403 if mapped differently
            ->assertJson([
                'code' => ApiResponseCode::ERROR_POST_NOT_AUTHOR->value,
                'message' => ApiResponseCode::ERROR_POST_NOT_AUTHOR->message(),
            ]);
        $this->assertDatabaseHas('posts', ['id' => $postByAnotherUser->id, 'content' => $postByAnotherUser->content]); // Content should not change

        // Scenario 3: Updating post with validation error (empty content)
        $postToValidate = Post::factory()->create(['user_id' => $this->user->id]);
        $invalidUpdateData = ['content' => ''];

        $responseValidation = $this->putJson("/api/v1/posts/{$postToValidate->id}", $invalidUpdateData);

        $responseValidation->assertStatus(422)
            ->assertJsonValidationErrorFor('content');
    }

    public function testDeletePost(): void
    {
        // Scenario 1: Successfully deleting own post
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200) // Or 204 No Content if API returns no body
            ->assertJson([
                'code' => ApiResponseCode::SUCCESS->value,
                // 'message' => 'Successfully deleted' // Or whatever success message is configured
            ]);
        $this->assertSoftDeleted('posts', ['id' => $post->id]); // Assuming SoftDeletes trait is used on Post model

        // Scenario 2: Non-author attempts to delete post
        $anotherUser = User::factory()->create();
        $postByAnotherUser = Post::factory()->create(['user_id' => $anotherUser->id]);

        $responseNotAuthor = $this->deleteJson("/api/v1/posts/{$postByAnotherUser->id}");

        // PostController's destroy method uses PostService->del which throws CustomException(ERROR_POST_NOT_AUTHOR)
        $responseNotAuthor->assertStatus(400) // Or 403 if mapped differently
            ->assertJson([
                'code' => ApiResponseCode::ERROR_POST_NOT_AUTHOR->value,
                'message' => ApiResponseCode::ERROR_POST_NOT_AUTHOR->message(),
            ]);
        $this->assertDatabaseHas('posts', ['id' => $postByAnotherUser->id, 'deleted_at' => null]); // Ensure it's not deleted
    }

    public function testLikePost(): void
    {
        $otherUser = User::factory()->create();
        $otherUserPost = Post::factory()->create(['user_id' => $otherUser->id]);

        // Scenario 1: Successfully like a post
        $response = $this->patchJson("/api/v1/posts/{$otherUserPost->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'code' => ApiResponseCode::SUCCESS->value,
                'message' => ApiResponseCode::SUCCESS->message(), // Assuming default success message
            ]);
        $this->assertDatabaseHas('post_likes', [
            'user_id' => $this->user->id,
            'post_id' => $otherUserPost->id,
            'liked' => PostLike::LIKED_LIKE,
        ]);

        // Scenario 2: Author attempts to like their own post (should fail)
        $myPost = Post::factory()->create(['user_id' => $this->user->id]);
        $responseOwnLike = $this->patchJson("/api/v1/posts/{$myPost->id}/like");

        $responseOwnLike->assertStatus(400) // Or specific code for ERROR_POST_AUTHOR_CAN_NOT_LIKE
            ->assertJson([
                'code' => ApiResponseCode::ERROR_POST_AUTHOR_CAN_NOT_LIKE->value,
                'message' => ApiResponseCode::ERROR_POST_AUTHOR_CAN_NOT_LIKE->message(),
            ]);
    }

    public function testDislikePost(): void
    {
        $otherUser = User::factory()->create();
        $otherUserPost = Post::factory()->create(['user_id' => $otherUser->id]);

        // First, like the post to be able to dislike it
        PostLike::factory()->create([
            'user_id' => $this->user->id,
            'post_id' => $otherUserPost->id,
            'liked' => PostLike::LIKED_LIKE,
        ]);
        $this->assertDatabaseHas('post_likes', [ // Verify it was liked
            'user_id' => $this->user->id,
            'post_id' => $otherUserPost->id,
            'liked' => PostLike::LIKED_LIKE,
        ]);


        // Scenario 1: Successfully dislike a post
        $response = $this->deleteJson("/api/v1/posts/{$otherUserPost->id}/like"); // API uses DELETE for dislike

        $response->assertStatus(200)
            ->assertJson([
                'code' => ApiResponseCode::SUCCESS->value,
                'message' => ApiResponseCode::SUCCESS->message(),
            ]);
        // Check if the record is removed or marked as 'disliked' if your system uses soft deletes or status changes for likes
        // Assuming it's a hard delete from post_likes table or 'liked' status changes
        $this->assertDatabaseMissing('post_likes', [
             'user_id' => $this->user->id,
             'post_id' => $otherUserPost->id,
             'liked' => PostLike::LIKED_LIKE, // Ensure the 'like' record is gone
        ]);
        // If your app marks it as 'disliked' instead of deleting:
        // $this->assertDatabaseHas('post_likes', ['user_id' => $this->user->id, 'post_id' => $otherUserPost->id, 'liked' => PostLike::LIKED_DISLIKE]);
    }

    public function testGetLikedUsers(): void
    {
        $post = Post::factory()->create();
        $usersWhoLiked = User::factory()->count(3)->create();

        foreach ($usersWhoLiked as $userLiking) {
            PostLike::factory()->create([
                'user_id' => $userLiking->id,
                'post_id' => $post->id,
                'liked' => PostLike::LIKED_LIKE,
            ]);
        }

        // A user who did not like the post
        User::factory()->create();

        $response = $this->getJson("/api/v1/posts/{$post->id}/liked_users");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'data' => [ // Assuming pagination structure
                        '*' => [
                            'id',
                            'name',
                            'email',
                            // other user fields from UserResource
                        ]
                    ],
                    'links',
                    'meta',
                ]
            ])
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(3, 'data.data'); // Ensure only 3 users are returned

        // Verify the IDs of the users returned
        $returnedUserIds = collect($response->json('data.data'))->pluck('id')->all();
        $expectedUserIds = $usersWhoLiked->pluck('id')->all();
        sort($returnedUserIds); // Sort for consistent comparison
        sort($expectedUserIds);
        $this->assertEquals($expectedUserIds, $returnedUserIds);
    }
}
