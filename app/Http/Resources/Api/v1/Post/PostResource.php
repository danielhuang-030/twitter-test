<?php

namespace App\Http\Resources\Api\v1\Post;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $id = (int) $this->id,
            'author_id' => $authorId = (int) $this->user->id,
            'author' => (string) $this->user->name,
            'content' => (string) $this->content,
            'is_liked' => (bool) in_array($id, $request->get('liked_post_ids', [])),
            'is_followed' => (bool) in_array($authorId, $request->get('followed_user_ids', [])),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
