<?php

namespace App\Repositories;

use App\Http\Requests\Api\v1\User\PostsRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class PostRepository extends BaseRepository
{
    protected function model(): string
    {
        return Post::class;
    }

    protected function getQueryByParam($param): Builder
    {
        /** @var \App\Params\PostParam $param */
        $query = parent::getQueryByParam($param);

        // join users
        $tableUser = (new User())->getTable();
        $query->addSelect([
            $this->model->qualifyColumn('*'),
        ])->leftJoin($tableUser, $this->model->qualifyColumn('user_id'), '=', sprintf('%s.id', $tableUser));

        // user id
        $userId = $param->getUserId();
        if (!empty($userId)) {
            $query->where($this->model->qualifyColumn('user_id'), $userId);
        }

        // author
        $author = $param->getAuthor();
        if (!empty($author)) {
            $query->where(sprintf('%s.name', $tableUser), 'like', sprintf('%%%s%%', $author));
        }

        return $query;
    }

    protected function getSortByFullColumnName(string $sortBy): string
    {
        switch ($sortBy) {
            case PostsRequest::SORT_BY_AUTHOR:
                $tableUser = (new User())->getTable();

                return sprintf('%s.name', $tableUser);
                break;

            case PostsRequest::SORT_BY_CREATED_AT:
            case PostsRequest::SORT_BY_UPDATED_AT:
            default:
                return parent::getSortByFullColumnName($sortBy);
                break;
        }
    }
}
