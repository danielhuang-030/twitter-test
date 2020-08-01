<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * UserService.
     *
     * @var UserService
     */
    protected $userService;

    /**
     * construct.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * info.
     */
    public function info(Request $request)
    {
        return response()->json(Auth::user());
    }

    /**
     * following.
     *
     * @param Request $request
     * @param int     $id
     */
    public function following(Request $request, $id)
    {
        $user = (empty($id) ? Auth::user() : $this->userService->getUser($id));
        if (null === $user) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($user->following->all());
    }

    /**
     * followers.
     *
     * @param Request $request
     * @param int     $id
     */
    public function followers(Request $request, $id)
    {
        $user = (empty($id) ? Auth::user() : $this->userService->getUser($id));
        if (null === $user) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($user->followers->all());
    }

    /**
     * posts.
     *
     * @param Request $request
     * @param int     $id
     */
    public function posts(Request $request, $id)
    {
        $user = (empty($id) ? Auth::user() : $this->userService->getUser($id));
        if (null === $user) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($user->load(['posts' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }])->posts->all());
    }

    /**
     * liked posts.
     *
     * @param Request $request
     * @param int     $id
     */
    public function likedPosts(Request $request, $id)
    {
        $user = (empty($id) ? Auth::user() : $this->userService->getUser($id));
        if (null === $user) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($user->load(['likePosts' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }])->likePosts->all());
    }
}
