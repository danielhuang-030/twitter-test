<?php

namespace App\Models;

class User extends BaseModelAuthenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        return $this->hasMany(Post::class);
    }

    /**
     * following.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, UserFollow::class, 'follow_id', 'user_id')
            ->withPivot([
                'created_at',
            ]);
    }

    /**
     * like posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likePosts()
    {
        return $this->belongsToMany(Post::class, PostLike::class)
            ->where('liked', PostLike::LIKED_LIKE)
            ->withTimestamps();
    }
}
