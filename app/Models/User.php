<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'api_token', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Posts::class);
    }

    /**
     * follows
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function follows()
    {
        return $this->hasManyThrough(User::class, UserFollow::class, 'user_id', 'id', null, 'follow_id');
    }

    /**
     * follow me
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function followMes()
    {
        return $this->hasManyThrough(User::class, UserFollow::class, 'follow_id', 'id', null, 'user_id');
    }

    /**
     * like posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function likePosts()
    {
        return $this->hasManyThrough(Post::class, PostLike::class, 'user_id', 'id', null, 'post_id');
    }
}
