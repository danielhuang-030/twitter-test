<?php

namespace App\Http\Controllers;

use App\Services\FollowService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * FollowService
     *
     * @var FollowService
     */
    protected $followService;

    /**
     * construct
     *
     * @param FollowService $followService
     */
    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    /**
     * store
     *
     * @param Request $request
     * @param integer $id
     */
    public function store(Request $request, int $id)
    {
        $userFollow = $this->followService->add($id, auth()->user()->id);
        if (null === $userFollow) {
            return response()->json([
                'message' => 'error'
            ], 403);
        }

        return response()->json([
            'message' => 'Successfully followed user!'
        ]);
    }

    /**
     * destroy
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        if (!$this->followService->del($id, auth()->user()->id)) {
            return response()->json([
                'message' => 'error'
            ], 403);
        }

        return response()->json([
            'message' => 'Successfully unfollowed user!'
        ]);
    }
}
