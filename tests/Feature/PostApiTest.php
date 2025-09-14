<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    private $userA;
    private $userB;
    private $userB_post;

    protected function setUp(): void
    {
        parent::setUp();

        // Create two users
        $this->userA = User::factory()->create();
        $this->userB = User::factory()->create();

        // Create posts for User B
        $this->userB_post = Post::factory()->create(['user_id' => $this->userB->id]);
        Post::factory()->count(2)->create(['user_id' => $this->userB->id]);

        // User A follows User B
        $this->userA->following()->attach($this->userB->id);

        // User A likes one of User B's posts
        $this->userB_post->likedUsers()->attach($this->userA->id);
    }

    /**
     * Test the main post feed endpoint.
     *
     * @return void
     */
    public function test_main_post_feed_returns_correct_state()
    {
        $response = $this->actingAs($this->userA, 'api')->getJson('/api/v1/posts');

        $response->assertStatus(200);
        
        $postData = $response->json('data.data');
        
        // Find the specific post we liked
        $likedPost = collect($postData)->firstWhere('id', $this->userB_post->id);

        $this->assertNotNull($likedPost, 'The specific post by User B was not found in the feed.');

        // Assert that all posts from User B have the correct state
        collect($postData)->where('author_id', $this->userB->id)->each(function ($post) {
            $this->assertTrue($post['is_followed'], 'is_followed should be true for User B\'s posts.');
            
            if ($post['id'] === $this->userB_post->id) {
                $this->assertTrue($post['is_liked'], 'is_liked should be true for the specific liked post.');
                $this->assertEquals(1, $post['likes_count'], 'likes_count should be 1 for the specific post.');
            } else {
                $this->assertFalse($post['is_liked'], 'is_liked should be false for other posts.');
                $this->assertEquals(0, $post['likes_count'], 'likes_count should be 0 for other posts.');
            }
        });
    }

    /**
     * Test the user-specific post feed endpoint.
     *
     * @return void
     */
    public function test_user_post_feed_returns_correct_state()
    {
        $response = $this->actingAs($this->userA, 'api')->getJson("/api/v1/users/{$this->userB->id}/posts");

        $response->assertStatus(200);

        $postData = $response->json('data.data');

        // Find the specific post we liked
        $likedPost = collect($postData)->firstWhere('id', $this->userB_post->id);

        $this->assertNotNull($likedPost, 'The specific post by User B was not found in their feed.');

        // Assert that all posts have the correct state
        collect($postData)->each(function ($post) {
            $this->assertTrue($post['is_followed'], 'is_followed should be true for all of User B\'s posts.');

            if ($post['id'] === $this->userB_post->id) {
                $this->assertTrue($post['is_liked'], 'is_liked should be true for the specific liked post.');
                $this->assertEquals(1, $post['likes_count'], 'likes_count should be 1 for the specific post.');
            } else {
                $this->assertFalse($post['is_liked'], 'is_liked should be false for other posts.');
                $this->assertEquals(0, $post['likes_count'], 'likes_count should be 0 for other posts.');
            }
        });
    }
}