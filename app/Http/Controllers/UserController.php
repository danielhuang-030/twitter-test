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
     * User.
     *
     * @var User
     */
    protected $user;

    /**
     * construct.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        // get user
        $this->middleware(function ($request, $next) {
            $id = $request->route('id', 0);
            $this->user = (empty($id) ? Auth::user() : $this->userService->getUser($id));
            if (null === $this->user) {
                return response()->json([
                    'message' => 'error',
                ], Response::HTTP_BAD_REQUEST);
            }

            return $next($request);
        });
    }

    /**
     * info.
     *
     * @param Request $request
     * @param int     $id
     */
    public function info(Request $request, $id)
    {
        return response()->json($this->user);
    }

    /**
     * following.
     *
     * @param Request $request
     * @param int     $id
     */
    public function following(Request $request, $id)
    {
        return response()->json($this->user->following);
    }

    /**
     * followers.
     *
     * @param Request $request
     * @param int     $id
     */
    public function followers(Request $request, $id)
    {
        return response()->json($this->user->followers);
    }

    /**
     * posts.
     *
     * @param Request $request
     * @param int     $id
     */
    public function posts(Request $request, $id)
    {
        return response()->json($this->user->load(['posts' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }])->posts);
    }

    /**
     * liked posts.
     *
     * @param Request $request
     * @param int     $id
     */
    public function likedPosts(Request $request, $id)
    {
        return response()->json($this->user->load(['likePosts' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }])->likePosts);
    }
}
