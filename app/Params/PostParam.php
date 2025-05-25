<?php

namespace App\Params;

use App\Http\Requests\Api\v1\User\PostsRequest;
use Illuminate\Http\Request;

class PostParam extends BaseParam
{
    private ?int $userId = null;
    private ?string $author = null;

    public function __construct(?Request $request = null)
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
        return (int) $this->userId; // Casting to int handles null, returning 0. If null should be possible, return type is ?int
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAuthor(): string
    {
        return (string) $this->author; // Casting to string handles null, returning "". If null should be possible, return type is ?string
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }
}
