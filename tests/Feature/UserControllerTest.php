<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\UserFollow;
use App\Enums\ApiResponseCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $actingUser;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingUser = User::factory()->create();
        Passport::actingAs($this->actingUser);
        $this->otherUser = User::factory()->create();
    }

    // Test methods will be added here

    public function testShowCurrentUserProfile(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJson([
                'code' => ApiResponseCode::SUCCESS->value,
                'data' => [
                    'id' => $this->actingUser->id,
                    'name' => $this->actingUser->name,
                    'email' => $this->actingUser->email,
                ]
            ]);
    }

    public function testShowSpecificUserInfo(): void
    {
        // Scenario 1: Successfully fetching specific user's info
        $response = $this->getJson("/api/v1/users/{$this->otherUser->id}/info");

        $response->assertStatus(200)
            ->assertJson([
                'code' => ApiResponseCode::SUCCESS->value,
                'data' => [
                    'id' => $this->otherUser->id,
                    'name' => $this->otherUser->name,
                    'email' => $this->otherUser->email,
                    // 'is_followed' should also be present if the authenticated user is viewing another user
                ]
            ]);
        // Check if 'is_followed' exists and is false by default when not following
        $this->assertFalse($response->json('data.is_followed'), "'is_followed' should be false if not followed.");


        // Scenario 2: Fetching specific user's info when following them
        UserFollow::factory()->create(['user_id' => $this->actingUser->id, 'follow_id' => $this->otherUser->id]);
        $responseWhenFollowing = $this->getJson("/api/v1/users/{$this->otherUser->id}/info");

        $responseWhenFollowing->assertStatus(200)
            ->assertJsonPath('data.id', $this->otherUser->id)
            ->assertJsonPath('data.is_followed', true);


        // Scenario 3: Fetching non-existent user's info
        $nonExistentUserId = 99999;
        $responseNotFound = $this->getJson("/api/v1/users/{$nonExistentUserId}/info");

        // UserController@info uses UserService->getUser which throws CustomException(ERROR_USER_NOT_EXIST)
        // This is handled by BaseController's render method, typically resulting in a 400 or 404.
        // Assuming 400 as per previous similar cases (e.g., PostController show non-existent post)
        $responseNotFound->assertStatus(400)
            ->assertJson([
                'code' => ApiResponseCode::ERROR_USER_NOT_EXIST->value,
                'message' => ApiResponseCode::ERROR_USER_NOT_EXIST->message(),
            ]);
    }

    public function testUserFollowingList(): void
    {
        $anotherFollowedUser = User::factory()->create();

        // Scenario 1: actingUser follows otherUser and anotherFollowedUser
        UserFollow::factory()->create(['user_id' => $this->actingUser->id, 'follow_id' => $this->otherUser->id]);
        UserFollow::factory()->create(['user_id' => $this->actingUser->id, 'follow_id' => $anotherFollowedUser->id]);

        $response = $this->getJson("/api/v1/users/{$this->actingUser->id}/following");

        $response->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(2, 'data.data') // Expecting 2 followed users in pagination data
            ->assertJsonFragment(['id' => $this->otherUser->id])
            ->assertJsonFragment(['id' => $anotherFollowedUser->id]);

        // Scenario 2: Fetching following list for a user who follows no one (otherUser)
        $responseEmpty = $this->getJson("/api/v1/users/{$this->otherUser->id}/following");

        $responseEmpty->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(0, 'data.data'); // Expecting 0 followed users
    }

    public function testUserFollowersList(): void
    {
        $anotherFollower = User::factory()->create();

        // Scenario 1: otherUser and anotherFollower follow actingUser
        UserFollow::factory()->create(['user_id' => $this->otherUser->id, 'follow_id' => $this->actingUser->id]);
        UserFollow::factory()->create(['user_id' => $anotherFollower->id, 'follow_id' => $this->actingUser->id]);

        $response = $this->getJson("/api/v1/users/{$this->actingUser->id}/followers");

        $response->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(2, 'data.data') // Expecting 2 followers
            ->assertJsonFragment(['id' => $this->otherUser->id])
            ->assertJsonFragment(['id' => $anotherFollower->id]);

        // Scenario 2: Fetching followers list for a user with no followers (otherUser)
        $responseEmpty = $this->getJson("/api/v1/users/{$this->otherUser->id}/followers");

        $responseEmpty->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(0, 'data.data'); // Expecting 0 followers
    }

    public function testUserPostsList(): void
    {
        // Scenario 1: Fetching posts for otherUser who has posts
        Post::factory()->count(3)->create(['user_id' => $this->otherUser->id]);
        Post::factory()->count(2)->create(['user_id' => $this->actingUser->id]); // Posts by actingUser, should not appear

        $response = $this->getJson("/api/v1/users/{$this->otherUser->id}/posts");

        $response->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(3, 'data.data'); // Only otherUser's posts

        // Verify all returned posts belong to otherUser
        foreach ($response->json('data.data') as $post) {
            $this->assertEquals($this->otherUser->id, $post['author']['id']);
        }

        // Scenario 2: Fetching posts for a user with no posts
        $userWithNoPosts = User::factory()->create();
        $responseNoPosts = $this->getJson("/api/v1/users/{$userWithNoPosts->id}/posts");

        $responseNoPosts->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(0, 'data.data');

        // Scenario 3: Testing pagination parameters (per_page)
        Post::factory()->count(5)->create(['user_id' => $this->otherUser->id]); // Total 3 + 5 = 8 posts for otherUser
        $responsePaginated = $this->getJson("/api/v1/users/{$this->otherUser->id}/posts?per_page=4");

        $responsePaginated->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(4, 'data.data') // per_page should limit to 4
            ->assertJsonPath('data.meta.per_page', 4)
            ->assertJsonPath('data.meta.total', 8); // Total 8 posts for otherUser (3 from earlier, 5 new)


        // Scenario 4: Testing pagination (page)
        $responsePage2 = $this->getJson("/api/v1/users/{$this->otherUser->id}/posts?per_page=4&page=2");
        $responsePage2->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(4, 'data.data') // 4 posts on page 2
            ->assertJsonPath('data.meta.current_page', 2);
    }

    public function testUserLikedPostsList(): void
    {
        // Scenario 1: Fetching liked posts for actingUser
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create(['user_id' => $this->otherUser->id]); // Post by another user
        $post3 = Post::factory()->create(); // Another post

        // actingUser likes post1 and post2
        PostLike::factory()->create(['user_id' => $this->actingUser->id, 'post_id' => $post1->id, 'liked' => PostLike::LIKED_LIKE]);
        PostLike::factory()->create(['user_id' => $this->actingUser->id, 'post_id' => $post2->id, 'liked' => PostLike::LIKED_LIKE]);

        // otherUser also likes post2
        PostLike::factory()->create(['user_id' => $this->otherUser->id, 'post_id' => $post2->id, 'liked' => PostLike::LIKED_LIKE]);
        // otherUser likes post3, which actingUser does not
        PostLike::factory()->create(['user_id' => $this->otherUser->id, 'post_id' => $post3->id, 'liked' => PostLike::LIKED_LIKE]);


        $response = $this->getJson("/api/v1/users/{$this->actingUser->id}/liked_posts");

        $response->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(2, 'data.data'); // actingUser liked 2 posts

        // Verify the IDs of the liked posts
        $likedPostIds = collect($response->json('data.data'))->pluck('id')->all();
        sort($likedPostIds);
        $expectedLikedPostIds = [$post1->id, $post2->id];
        sort($expectedLikedPostIds);
        $this->assertEquals($expectedLikedPostIds, $likedPostIds);

        // Ensure that posts liked by others but not by actingUser are not present
        $this->assertNotContains($post3->id, $likedPostIds);


        // Scenario 2: Fetching liked posts for a user who has not liked any posts (otherUser has liked posts, but we need a new user)
        $userWithNoLikes = User::factory()->create();
        $responseNoLikes = $this->getJson("/api/v1/users/{$userWithNoLikes->id}/liked_posts");

        $responseNoLikes->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(0, 'data.data');

        // Scenario 3: Testing pagination for liked posts
        $postsForPagination = Post::factory()->count(5)->create();
        foreach($postsForPagination as $p) {
            PostLike::factory()->create(['user_id' => $this->actingUser->id, 'post_id' => $p->id, 'liked' => PostLike::LIKED_LIKE]);
        }
        // Total liked posts for actingUser = 2 (from scenario 1) + 5 (new) = 7

        $responsePaginated = $this->getJson("/api/v1/users/{$this->actingUser->id}/liked_posts?per_page=4");
        $responsePaginated->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(4, 'data.data')
            ->assertJsonPath('data.meta.per_page', 4)
            ->assertJsonPath('data.meta.total', 7);

        $responsePage2 = $this->getJson("/api/v1/users/{$this->actingUser->id}/liked_posts?per_page=4&page=2");
        $responsePage2->assertStatus(200)
            ->assertJsonPath('code', ApiResponseCode::SUCCESS->value)
            ->assertJsonCount(3, 'data.data') // Remaining 3 posts on page 2
            ->assertJsonPath('data.meta.current_page', 2);
    }
}
