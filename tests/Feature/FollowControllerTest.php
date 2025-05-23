<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserFollow;
use Laravel\Passport\Passport;
use App\Enums\ApiResponseCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $currentUser;
    protected User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currentUser = User::factory()->create();
        Passport::actingAs($this->currentUser);
        $this->targetUser = User::factory()->create();
    }

    // Test methods will be added here

    public function testFollowUser(): void
    {
        // Scenario 1: Successfully follow user
        $response = $this->patchJson("/api/v1/following/{$this->targetUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'code' => ApiResponseCode::SUCCESS->value,
                'message' => ApiResponseCode::SUCCESS->message(), // Assuming default success message or specific one
            ]);
        $this->assertDatabaseHas('user_follows', [
            'user_id' => $this->currentUser->id,
            'follow_id' => $this->targetUser->id,
        ]);

        // Scenario 2: Attempt to follow self
        $responseSelf = $this->patchJson("/api/v1/following/{$this->currentUser->id}");

        $responseSelf->assertStatus(400) // Assuming BaseController maps CustomException to 400 by default
            ->assertJson([
                'code' => ApiResponseCode::ERROR_FOLLOW_SELF->value,
                'message' => ApiResponseCode::ERROR_FOLLOW_SELF->message(),
            ]);
        // Ensure no new follow record for self-following
        $this->assertDatabaseMissing('user_follows', [
             'user_id' => $this->currentUser->id,
             'follow_id' => $this->currentUser->id,
        ]);


        // Scenario 3: Attempt to follow an already followed user
        // The targetUser is already followed from Scenario 1.
        $responseAlreadyFollowed = $this->patchJson("/api/v1/following/{$this->targetUser->id}");

        $responseAlreadyFollowed->assertStatus(400)
            ->assertJson([
                'code' => ApiResponseCode::ERROR_FOLLOW_HAVE_FOLLOWED->value,
                'message' => ApiResponseCode::ERROR_FOLLOW_HAVE_FOLLOWED->message(),
            ]);
        // Ensure only one follow record exists
        $this->assertDatabaseCount('user_follows', 1, [
            'user_id' => $this->currentUser->id,
            'follow_id' => $this->targetUser->id,
        ]);


        // Scenario 4: Attempt to follow a non-existent user
        $nonExistentUserId = 99999;
        $responseNonExistent = $this->patchJson("/api/v1/following/{$nonExistentUserId}");

        $responseNonExistent->assertStatus(400)
            ->assertJson([
                'code' => ApiResponseCode::ERROR_USER_NOT_EXIST->value,
                'message' => ApiResponseCode::ERROR_USER_NOT_EXIST->message(),
            ]);
    }

    public function testUnfollowUser(): void
    {
        // Scenario 1: Successfully unfollow user (must first create a follow relationship)
        UserFollow::factory()->create([
            'user_id' => $this->currentUser->id,
            'follow_id' => $this->targetUser->id,
        ]);
        $this->assertDatabaseHas('user_follows', [
            'user_id' => $this->currentUser->id,
            'follow_id' => $this->targetUser->id,
        ]);

        $response = $this->deleteJson("/api/v1/following/{$this->targetUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'code' => ApiResponseCode::SUCCESS->value,
                'message' => ApiResponseCode::SUCCESS->message(),
            ]);
        $this->assertDatabaseMissing('user_follows', [
            'user_id' => $this->currentUser->id,
            'follow_id' => $this->targetUser->id,
        ]);

        // Scenario 2: Attempt to unfollow self
        $responseSelf = $this->deleteJson("/api/v1/following/{$this->currentUser->id}");

        $responseSelf->assertStatus(400)
            ->assertJson([
                'code' => ApiResponseCode::ERROR_UNFOLLOW_SELF->value,
                'message' => ApiResponseCode::ERROR_UNFOLLOW_SELF->message(),
            ]);

        // Scenario 3: Attempt to unfollow a user not currently followed
        // Ensure no follow relationship exists between currentUser and a newUser for this test
        $newUserToNotFollow = User::factory()->create();
        $this->assertDatabaseMissing('user_follows', [ // Ensure no pre-existing follow
            'user_id' => $this->currentUser->id,
            'follow_id' => $newUserToNotFollow->id,
        ]);

        $responseNotFollowing = $this->deleteJson("/api/v1/following/{$newUserToNotFollow->id}");

        $responseNotFollowing->assertStatus(400)
            ->assertJson([
                'code' => ApiResponseCode::ERROR_UNFOLLOW_NOT_FOLLOWED->value,
                'message' => ApiResponseCode::ERROR_UNFOLLOW_NOT_FOLLOWED->message(),
            ]);


        // Scenario 4: Attempt to unfollow a non-existent user
        $nonExistentUserId = 88888; // Different from the one in testFollowUser for clarity
        $responseNonExistent = $this->deleteJson("/api/v1/following/{$nonExistentUserId}");

        $responseNonExistent->assertStatus(400)
            ->assertJson([
                'code' => ApiResponseCode::ERROR_USER_NOT_EXIST->value, // FollowService checks if followId (target user) exists
                'message' => ApiResponseCode::ERROR_USER_NOT_EXIST->message(),
            ]);
    }
}
