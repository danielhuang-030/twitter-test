<?php

namespace App\Models;

class Post extends BaseModel
{
    /**
     * user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * liked users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likedUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, PostLike::class)
            ->where('liked', PostLike::LIKED_LIKE)
            ->withTimestamps();
    }
}
