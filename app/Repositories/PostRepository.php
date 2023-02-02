<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class PostRepository extends BaseRepository
{
    protected function model(): string
    {
        return Post::class;
    }

    protected function getQueryByParam($param): Builder
    {
        $query = parent::getQueryByParam($param);

        // user id
        $userId = $param->getUserId();
        if (!empty($userId)) {
            $query->where($this->model->qualifyColumn('user_id'), $userId);
        }

        return $query;
    }
}
