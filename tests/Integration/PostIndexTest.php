<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class PostIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        $this->artisan('migrate');
    }

    public function test_index_returns_posts(): void
    {
        $user = User::factory()->create();
        Post::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'code',
                     'message',
                     'data' => [
                         'pagination' => ['page', 'per_page', 'total'],
                         'data',
                     ],
                 ]);
        $this->assertCount(3, $response->json('data.data'));
    }
}
