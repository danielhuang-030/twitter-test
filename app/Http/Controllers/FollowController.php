<?php

namespace App\Http\Controllers;

use App\Services\FollowService;
use Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{
    /**
     * FollowService.
     *
     * @var FollowService
     */
    protected $followService;

    /**
     * construct.
     *
     * @param FollowService $followService
     */
    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    /**
     * store.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $userFollow = $this->followService->add(
            $request->input('user_id', 0),
            data_get(Auth::user(), 'id', 0)
        );
        if (null === $userFollow) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully followed user!',
        ]);
    }

    /**
     * destroy.
     *
     * @param int $id
     */
    public function destroy(int $id = 0)
    {
        if (!$this->followService->del($id, data_get(Auth::user(), 'id', 0))) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully unfollowed user!',
        ]);
    }
}
