<?php

namespace App\Params;

use App\Params\Traits\Pageable;
use App\Params\Traits\Sortable;
use App\Params\Traits\Withable;
use Illuminate\Http\Request;

class PostParam extends BaseParam
{
    use Pageable;
    use Sortable;
    use Withable;

    /**
     * user id.
     *
     * @var int
     */
    private $userId;

    /**
     * construct.
     *
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        if (null === $request) {
            return;
        }

        // pagination
        $this->setPage((int) $request->input('page', $this->getPage()))
            ->setPerPage((int) $request->input('page_size', config('app.page_size')));

        // sort
        if (!$request->has('sort_by')) {
            // default sort by updated_at DESC
            $this->setSortBy('updated_at', true);
        } else {
            $this->setSortBy($request->input('sort_by'), (bool) $request->input('is_desc'));
        }
    }

    /**
     * Get user id.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set user id.
     *
     * @param int $userId user id
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }
}
