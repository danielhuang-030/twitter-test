<?php

namespace App\Params;

use Illuminate\Http\Request;

class PostParam extends BaseParam
{
    private $userId;

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
            $this->setSortBy('updated_at', true);
        } else {
            $this->setSortBy($request->input('sort_by'), (bool) $request->input('is_desc', false));
        }
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId($userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
