<?php

namespace App\Params\Permission;

use App\Params\BaseParam;
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

        // 分頁
        $this->setPage((int) $request->input('current_page', $this->getPage()))
            ->setPerPage((int) $request->input('per_page', config('website.default_rows_per_page')));

        // 排序
        if (!$request->has('sort_by')) {
            // 預設更新時間新到舊
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
