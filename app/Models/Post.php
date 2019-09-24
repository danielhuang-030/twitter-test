<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    /**
     * user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * liked users
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function likedUsers()
    {
        return $this->hasManyThrough(User::class, PostLike::class, 'post_id', 'id', null, 'user_id');
    }
}
