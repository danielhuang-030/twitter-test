<?php

namespace App\Models;

class Post extends BaseModel
{
    /**
     * user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * liked users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, PostLike::class)
            ->where('liked', PostLike::LIKED_LIKE)
            ->withTimestamps();
    }
}
