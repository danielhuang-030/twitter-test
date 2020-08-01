<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

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
     * posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Posts::class);
    }

    /**
     * following.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, UserFollow::class, 'user_id', 'follow_id')
            ->withPivot([
                'created_at',
            ]);
    }

    /**
     * followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, UserFollow::class, 'follow_id', 'user_id')
            ->withPivot([
                'created_at',
            ]);

        return $this->hasMany(UserFollow::class, 'follow_id');
    }

    /**
     * like posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function likePosts()
    {
        return $this->hasManyThrough(Post::class, PostLike::class, 'user_id', 'id', null, 'post_id');
    }
}
