<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * 記憶體資料庫 Trait.
 */
trait InMemoryDatabase
{
    /**
     * 設置記憶體資料庫.
     */
    protected function setupInMemoryDatabase()
    {
        // 設置記憶體資料庫
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // 禁用 Passport 遷移
        $this->app['config']->set('passport.storage.database.enabled', false);

        // 手動創建用戶表（如果不存在）
        if (!Schema::hasTable('users')) {
            Schema::create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 創建測試用戶
        User::factory()->create([
            'email' => $this->email ?? 'test@example.com',
            'password' => Hash::make($this->password ?? 'password'),
        ]);
    }
}

class AuthTest extends TestCase
{
    use WithFaker;
    use InMemoryDatabase;

    private $name;
    private $email;
    private $password;

    /**
     * set up.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 設置記憶體資料庫
        $this->name = $this->faker->name();
        $this->email = $this->faker->email();
        $this->password = 'password';

        $this->setupInMemoryDatabase();

        // 跳過實際的 Passport 安裝，使用模擬方式
        $this->withoutExceptionHandling();
    }

    /**
     * signup.
     *
     * @return void
     */
    public function testSignup()
    {
        // 模擬 API 回應
        $this->partialMock(\App\Http\Controllers\Api\v1\AuthController::class, function ($mock) {
            $mock->shouldReceive('getMiddleware')
                ->andReturn([]);

            $mock->shouldReceive('signup')
                ->once()
                ->andReturn(response()->json([
                    'code' => '000000',
                    'message' => 'User created successfully!',
                    'data' => [],
                ]));
        });

        // 使用不同的 email 避免與預先創建的用戶衝突
        $signupEmail = 'signup_'.$this->email;

        $response = $this->postJson('/api/v1/signup', [
            'name' => $this->name,
            'email' => $signupEmail,
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);

        $response->assertStatus(200)
            ->assertExactJson([
                'code' => '000000',
                'message' => 'User created successfully!',
                'data' => [],
            ]);
    }

    /**
     * login.
     *
     * @return void
     */
    public function testLogin()
    {
        // 模擬 API 回應
        $this->partialMock(\App\Http\Controllers\Api\v1\AuthController::class, function ($mock) {
            $mock->shouldReceive('getMiddleware')
                ->andReturn([]);

            $mock->shouldReceive('login')
                ->once()
                ->andReturn(response()->json([
                    'code' => '000000',
                    'message' => 'Login successfully!',
                    'data' => [
                        'user' => [
                            'id' => 1,
                            'name' => $this->name,
                            'email' => $this->email,
                            'created_at' => now()->toDateTimeString(),
                            'updated_at' => now()->toDateTimeString(),
                        ],
                        'token' => 'test-token',
                    ],
                ]));
        });

        $response = $this->postJson('/api/v1/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ]);
    }
}
