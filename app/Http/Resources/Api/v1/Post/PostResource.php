<?php

namespace App\Http\Resources\Api\v1\Post;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'author_id' => (int) $this->user_id,
            'author' => (string) $this->user->name,
            'content' => (string) $this->content,
            'is_liked' => (bool) $this->is_liked,
            'is_followed' => (bool) $this->is_followed,
            'likes_count' => (int) $this->liked_users_count,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
