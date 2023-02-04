<?php

namespace App\Params;

use App\Http\Requests\Api\v1\User\PostsRequest;
use Illuminate\Http\Request;

class PostParam extends BaseParam
{
    private $userId;
    private $author;

    public function __construct(Request $request = null)
    {
        if (empty($request)) {
            return;
        }

        // pagination
        $this->setPage((int) $request->input('page', $this->getPage()))
            ->setPerPage((int) $request->input('per_page', config('app.per_page')));

        // sort
        if (!$request->has('sort_by')) {
            // default sort by updated_at DESC
            $this->setSortBy(PostsRequest::SORT_BY_UPDATED_AT, true);
        } else {
            $this->setSortBy((string) $request->input('sort_by'), (bool) $request->input('is_desc', false));
        }
    }

    public function getUserId(): int
    {
        return (int) $this->userId;
    }

    public function setUserId($userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAuthor(): string
    {
        return (string) $this->author;
    }

    public function setAuthor($author): self
    {
        $this->author = $author;

        return $this;
    }
}
